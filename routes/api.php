<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController as ApiAuth;
use App\Http\Controllers\Api\Admin\BreakGlassController as AdminBreakGlass;
use App\Http\Controllers\Api\Admin\BroadcastNotificationController as AdminBroadcast;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Api\Admin\FacilityImportController as AdminFacilityImport;
use App\Http\Controllers\Api\Admin\MdcnController;
use App\Http\Controllers\Api\Admin\PlanController as AdminPlans;
use App\Http\Controllers\Api\Admin\SettingsController as AdminSettings;
use App\Http\Controllers\Api\Admin\TeamController as AdminTeam;
use App\Http\Controllers\Api\Admin\UserController as AdminUsers;
use App\Http\Controllers\Api\Patient\AppointmentController as PatientAppointment;
use App\Http\Controllers\Api\Patient\AuditLogController as PatientAuditLog;
use App\Http\Controllers\Api\Patient\ConsentController as PatientConsent;
use App\Http\Controllers\Api\Patient\DashboardController as PatientDashboard;
use App\Http\Controllers\Api\Patient\DependentController as PatientDependent;
use App\Http\Controllers\Api\Patient\EncounterPrintController as PatientEncounterPrint;
use App\Http\Controllers\Api\Patient\FamilySummaryController as PatientFamilySummary;
use App\Http\Controllers\Api\Patient\LabUploadController as PatientLabUpload;
use App\Http\Controllers\Api\Patient\NotificationController as PatientNotifications;
use App\Http\Controllers\Api\Patient\ProfileController as PatientProfile;
use App\Http\Controllers\Api\Patient\RecordController as PatientRecord;
use App\Http\Controllers\Api\Patient\RecordExportController as PatientRecordExport;
use App\Http\Controllers\Api\Patient\RefillRequestController as PatientRefill;
use App\Http\Controllers\Api\Patient\VitalLogController as PatientVitals;
use App\Http\Controllers\Api\Pharmacy\DispenseController as PharmacyDispense;
use App\Http\Controllers\Api\Provider\AppointmentController as ProviderAppointment;
use App\Http\Controllers\Api\Provider\ConsentRequestController as ProviderConsentRequest;
use App\Http\Controllers\Api\Provider\EncounterPrintController as ProviderEncounterPrint;
use App\Http\Controllers\Api\Provider\LabResultController as ProviderLab;
use App\Http\Controllers\Api\Provider\ReferralController as ProviderReferral;
use App\Http\Controllers\Api\Provider\RefillRequestController as ProviderRefill;
use App\Http\Controllers\Api\Provider\WorkstationController as ProviderWorkstation;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MedRecords REST API (JWT via tymon/jwt-auth)
|--------------------------------------------------------------------------
| Every endpoint mirrors its web counterpart. Send the token as:
|   Authorization: Bearer <access_token>
| Two JWT guards are used:
|   api        -> users (patient, provider, pharmacist)
|   admin-api  -> admins (facility_admin, super_admin)
*/

// Blocks 2FA-pending tokens everywhere except the allowed auth routes.
Route::middleware('api.2fa')->group(function () {

    // == Public auth =========================================================
    Route::post('/auth/login',            [ApiAuth::class, 'login'])->middleware('throttle:10,1')->name('api.auth.login');
    Route::post('/auth/register',         [ApiAuth::class, 'register'])->middleware('throttle:5,1')->name('api.auth.register');
    Route::post('/auth/2fa/verify',       [ApiAuth::class, 'verify2fa'])->middleware('throttle:5,1')->name('api.auth.2fa.verify');
    Route::post('/auth/forgot-password',  [ApiAuth::class, 'forgotPassword'])->middleware('throttle:5,1')->name('api.auth.forgot');
    Route::post('/auth/reset-password',   [ApiAuth::class, 'resetPassword'])->middleware('throttle:5,15')->name('api.auth.reset');

    // == Authenticated, any role (users or admins) ============================
    Route::middleware('auth:api,admin-api')->group(function () {
        Route::get('/auth/me',            [AccountController::class, 'me'])->name('api.auth.me');
        Route::post('/auth/refresh',      [ApiAuth::class, 'refresh'])->name('api.auth.refresh');
        Route::post('/auth/logout',       [ApiAuth::class, 'logout'])->name('api.auth.logout');

        // OTP email verification (pre-verification safe zone)
        Route::get('/auth/email/status',  [AccountController::class, 'emailStatus'])->name('api.email.status');
        Route::post('/auth/email/verify', [AccountController::class, 'emailVerify'])->middleware('throttle:6,1')->name('api.email.verify');
        Route::post('/auth/email/resend', [AccountController::class, 'emailResend'])->middleware('throttle:3,1')->name('api.email.resend');

        // 2FA setup / avatar (shared with all roles)
        Route::get('/account/2fa',        [AccountController::class, 'show2fa'])->name('api.account.2fa');
        Route::post('/account/2fa',       [AccountController::class, 'toggle2fa'])->name('api.account.2fa.toggle');
        Route::post('/account/avatar',    [AccountController::class, 'updateAvatar'])->name('api.account.avatar');
    });

    // == Patient ==============================================================
    // Welcome/onboarding is reachable before email verification.
    Route::prefix('patient')->middleware(['auth:api', 'role:patient'])->group(function () {
        Route::get('/welcome', [PatientDashboard::class, 'welcome'])->name('api.patient.welcome');
    });

    Route::prefix('patient')->middleware(['auth:api', 'verified', 'role:patient'])->group(function () {
        Route::get('/dashboard',                    [PatientDashboard::class, 'index'])->name('api.patient.dashboard');
        Route::get('/record/{tab?}',                [PatientRecord::class, 'index'])->name('api.patient.record');
        Route::get('/prescriptions/{id}',           [PatientRecord::class, 'prescription'])->name('api.patient.prescription');

        // Consents
        Route::get('/consents',                     [PatientConsent::class, 'index'])->name('api.patient.consents');
        Route::patch('/consents/{id}/revoke',       [PatientConsent::class, 'revoke'])->name('api.patient.consents.revoke')->middleware('throttle:consent-actions');
        Route::patch('/consents/{id}/renew',        [PatientConsent::class, 'renew'])->name('api.patient.consents.renew')->middleware('throttle:consent-actions');
        Route::patch('/consents/{id}/approve',      [PatientConsent::class, 'approve'])->name('api.patient.consents.approve')->middleware('throttle:consent-actions');
        Route::delete('/consents/{id}/decline',     [PatientConsent::class, 'decline'])->name('api.patient.consents.decline')->middleware('throttle:consent-actions');

        // Access codes
        Route::get('/access-code',                  [PatientDashboard::class, 'showAccessCode'])->name('api.patient.access-code');
        Route::post('/access-code/generate',        [PatientDashboard::class, 'generateCode'])->name('api.patient.access-code.generate');
        Route::post('/access-codes/{id}/sms',       [PatientDashboard::class, 'sendSms'])->name('api.patient.access-code.sms')->middleware('throttle:3,5');

        // Audit log
        Route::get('/audit-log',                    [PatientAuditLog::class, 'index'])->name('api.patient.audit-log');
        Route::get('/audit-log/export',             [PatientAuditLog::class, 'export'])->name('api.patient.audit-log.export')->middleware('throttle:5,10');

        // Family / dependents
        Route::get('/family',                       [PatientDependent::class, 'index'])->name('api.patient.family');
        Route::get('/family/options',               [PatientDependent::class, 'create'])->name('api.patient.family.options');
        Route::post('/family',                      [PatientDependent::class, 'store'])->name('api.patient.family.store');
        Route::get('/family/summary',               [PatientFamilySummary::class, 'index'])->name('api.patient.family.summary');

        // Profile
        Route::get('/profile',                      [PatientProfile::class, 'edit'])->name('api.patient.profile');
        Route::put('/profile',                      [PatientProfile::class, 'update'])->name('api.patient.profile.update');
        Route::put('/profile/password',             [PatientProfile::class, 'changePassword'])->name('api.patient.profile.password');
        Route::post('/profile/2fa',                 [PatientProfile::class, 'toggle2fa'])->name('api.patient.profile.2fa');

        // Appointments
        Route::get('/appointments',                 [PatientAppointment::class, 'index'])->name('api.patient.appointments');
        Route::get('/appointments/options',         [PatientAppointment::class, 'create'])->name('api.patient.appointments.options');
        Route::post('/appointments',                [PatientAppointment::class, 'store'])->name('api.patient.appointments.store');
        Route::post('/appointments/{id}/cancel',    [PatientAppointment::class, 'cancel'])->name('api.patient.appointments.cancel');

        // Vitals diary
        Route::get('/vitals',                       [PatientVitals::class, 'index'])->name('api.patient.vitals');
        Route::post('/vitals',                      [PatientVitals::class, 'store'])->name('api.patient.vitals.store');
        Route::delete('/vitals/{id}',               [PatientVitals::class, 'destroy'])->name('api.patient.vitals.destroy');

        // Refill requests
        Route::get('/refills',                      [PatientRefill::class, 'index'])->name('api.patient.refills');
        Route::post('/refills',                     [PatientRefill::class, 'store'])->name('api.patient.refills.store');

        // Lab uploads
        Route::get('/lab-upload/options',           [PatientLabUpload::class, 'create'])->name('api.patient.lab-upload.options');
        Route::post('/lab-upload',                  [PatientLabUpload::class, 'store'])->name('api.patient.lab-upload.store')->middleware('throttle:patient-writes');
        Route::get('/lab-results/{id}/download',    [PatientLabUpload::class, 'download'])->name('api.patient.lab.download');

        // Notifications
        Route::get('/notifications',                [PatientNotifications::class, 'index'])->name('api.patient.notifications');
        Route::delete('/notifications/{id}',        [PatientNotifications::class, 'destroy'])->name('api.patient.notifications.destroy');

        // Documents
        Route::get('/encounters/{id}/print',        [PatientEncounterPrint::class, 'show'])->name('api.patient.encounter.print');
        Route::get('/export',                       [PatientRecordExport::class, 'export'])->name('api.patient.record.export')->middleware('throttle:patient-writes');
    });

    // == Provider =============================================================
    Route::prefix('provider')->middleware(['auth:api', 'verified', 'role:provider', 'require.2fa'])->group(function () {
        Route::get('/dashboard',                [ProviderWorkstation::class, 'dashboard'])->name('api.provider.dashboard');
        Route::get('/search',                   [ProviderWorkstation::class, 'search'])->name('api.provider.search');
        Route::get('/pending-verification',     fn () => response()->json([
            'message' => 'Your MDCN credentials have not been verified yet.',
            'code'    => 'provider_pending_verification',
        ]))->name('api.provider.pending-verification');

        // Patient-access routes require MDCN verification
        Route::middleware('mdcn.verified')->group(function () {
            Route::get('/patients/{id}',                        [ProviderWorkstation::class, 'patient'])->name('api.provider.patient');
            Route::get('/patients/{id}/encounters/options',     [ProviderWorkstation::class, 'newEncounter'])->name('api.provider.encounter.options');
            Route::post('/patients/{id}/encounters',            [ProviderWorkstation::class, 'storeEncounter'])->name('api.provider.encounter.store')->middleware('throttle:provider-writes');
            Route::get('/patients/{id}/encounters/{eid}/print', [ProviderEncounterPrint::class, 'show'])->name('api.provider.encounter.print');
            Route::get('/patients/{id}/break-glass',            [ProviderWorkstation::class, 'breakGlass'])->name('api.provider.break-glass');
            Route::post('/patients/{id}/break-glass',           [ProviderWorkstation::class, 'storeBreakGlass'])->name('api.provider.break-glass.store')->middleware('throttle:break-glass');
            Route::get('/patients/{id}/consent-request',        [ProviderConsentRequest::class, 'create'])->name('api.provider.consent-request.options');
            Route::post('/patients/{id}/consent-request',       [ProviderConsentRequest::class, 'store'])->name('api.provider.consent-request.store');
            Route::get('/patients/{id}/referrals/options',      [ProviderReferral::class, 'create'])->name('api.provider.referral.options');
            Route::post('/patients/{id}/referrals',             [ProviderReferral::class, 'store'])->name('api.provider.referral.store');
            Route::get('/patients/{id}/labs/options',           [ProviderLab::class, 'create'])->name('api.provider.lab.options');
            Route::post('/patients/{id}/labs',                  [ProviderLab::class, 'store'])->name('api.provider.lab.store');
        });

        // Provider appointments
        Route::get('/appointments',                     [ProviderAppointment::class, 'index'])->name('api.provider.appointments');
        Route::post('/appointments/{id}/confirm',       [ProviderAppointment::class, 'confirm'])->name('api.provider.appointments.confirm');
        Route::post('/appointments/{id}/complete',      [ProviderAppointment::class, 'complete'])->name('api.provider.appointments.complete');
        Route::post('/appointments/{id}/no-show',       [ProviderAppointment::class, 'noShow'])->name('api.provider.appointments.no-show');

        // Refill requests
        Route::get('/refills',                          [ProviderRefill::class, 'index'])->name('api.provider.refills');
        Route::post('/refills/{id}/approve',            [ProviderRefill::class, 'approve'])->name('api.provider.refills.approve');
        Route::post('/refills/{id}/deny',               [ProviderRefill::class, 'deny'])->name('api.provider.refills.deny');

        // Referral inbox
        Route::get('/referrals',                        [ProviderReferral::class, 'index'])->name('api.provider.referrals');
        Route::patch('/referrals/{id}/status',          [ProviderReferral::class, 'updateStatus'])->name('api.provider.referrals.status');
    });

    // == Pharmacy =============================================================
    Route::prefix('pharmacy')->middleware(['auth:api', 'verified', 'role:pharmacist', 'require.2fa'])->group(function () {
        Route::get('/dashboard',                [PharmacyDispense::class, 'dashboard'])->name('api.pharmacy.dashboard');
        Route::get('/lookup',                   [PharmacyDispense::class, 'lookup'])->name('api.pharmacy.lookup');
        Route::post('/dispense/{id}',           [PharmacyDispense::class, 'dispense'])->name('api.pharmacy.dispense');
        Route::get('/history',                  [PharmacyDispense::class, 'history'])->name('api.pharmacy.history');
    });

    // == Admin ================================================================
    Route::prefix('admin')->middleware(['auth:admin-api', 'verified', 'role:facility_admin,super_admin', 'require.2fa'])->group(function () {
        Route::get('/dashboard',                     [AdminDashboard::class, 'index'])->name('api.admin.dashboard');
        Route::get('/facilities',                    [AdminDashboard::class, 'facilities'])->name('api.admin.facilities');
        Route::get('/break-glass',                   [AdminDashboard::class, 'breakGlass'])->name('api.admin.break-glass');
        Route::post('/break-glass/{id}/approve',     [AdminBreakGlass::class, 'approve'])->name('api.admin.break-glass.approve');
        Route::post('/break-glass/{id}/dispute',     [AdminBreakGlass::class, 'dispute'])->name('api.admin.break-glass.dispute');
        Route::post('/break-glass/{id}/close',       [AdminBreakGlass::class, 'close'])->name('api.admin.break-glass.close');
        Route::get('/providers',                     [MdcnController::class, 'providers'])->name('api.admin.providers');
        Route::post('/providers/{id}/verify',        [MdcnController::class, 'verify'])->name('api.admin.mdcn.verify');

        // Settings
        Route::get('/settings',                      [AdminSettings::class, 'index'])->name('api.admin.settings');
        Route::post('/settings',                     [AdminSettings::class, 'update'])->name('api.admin.settings.update');
        Route::post('/settings/smtp-test',           [AdminSettings::class, 'testSmtp'])->name('api.admin.settings.smtp-test');
        Route::post('/settings/sms-test',            [AdminSettings::class, 'testSms'])->name('api.admin.settings.sms-test');

        // Broadcast notifications
        Route::get('/notifications',                 [AdminBroadcast::class, 'index'])->name('api.admin.notifications');
        Route::post('/notifications',                [AdminBroadcast::class, 'store'])->name('api.admin.notifications.send');

        // Users & Roles (super_admin only - enforced inside the controller)
        Route::get('/users',                         [AdminUsers::class, 'index'])->name('api.admin.users');
        Route::get('/users/options',                 [AdminUsers::class, 'create'])->name('api.admin.users.options');
        Route::post('/users',                        [AdminUsers::class, 'store'])->name('api.admin.users.store');
        Route::get('/users/{id}',                    [AdminUsers::class, 'show'])->name('api.admin.users.show');
        Route::put('/users/{id}',                    [AdminUsers::class, 'update'])->name('api.admin.users.update');
        Route::patch('/users/{id}/toggle-active',    [AdminUsers::class, 'toggleActive'])->name('api.admin.users.toggle-active');
        Route::post('/users/{id}/reset-password',    [AdminUsers::class, 'resetPassword'])->name('api.admin.users.reset-password');
        Route::delete('/users/{id}',                 [AdminUsers::class, 'destroy'])->name('api.admin.users.destroy');

        // Plans & Pricing (super_admin only - enforced inside the controller)
        Route::get('/plans',                         [AdminPlans::class, 'index'])->name('api.admin.plans');
        Route::get('/plans/options',                 [AdminPlans::class, 'create'])->name('api.admin.plans.options');
        Route::post('/plans',                        [AdminPlans::class, 'store'])->name('api.admin.plans.store');
        Route::get('/plans/{id}',                    [AdminPlans::class, 'show'])->name('api.admin.plans.show');
        Route::put('/plans/{id}',                    [AdminPlans::class, 'update'])->name('api.admin.plans.update');
        Route::delete('/plans/{id}',                 [AdminPlans::class, 'destroy'])->name('api.admin.plans.destroy');

        // Facility CSV import (super_admin only - enforced inside the controller)
        Route::get('/facilities/import',             [AdminFacilityImport::class, 'create'])->name('api.admin.facilities.import');
        Route::post('/facilities/import',            [AdminFacilityImport::class, 'store'])->name('api.admin.facilities.import.store');

        // Team management
        Route::get('/team',                          [AdminTeam::class, 'index'])->name('api.admin.team');
        Route::post('/team/add',                     [AdminTeam::class, 'add'])->name('api.admin.team.add');
        Route::delete('/team/{id}',                  [AdminTeam::class, 'remove'])->name('api.admin.team.remove');
    });
});
