<?php

use App\Http\Controllers\Admin\BreakGlassController as AdminBreakGlass;
use App\Http\Controllers\Admin\TeamController as AdminTeam;
use App\Http\Controllers\Admin\BroadcastNotificationController as AdminBroadcast;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\FacilityImportController as AdminFacilityImport;
use App\Http\Controllers\Admin\MdcnController;
use App\Http\Controllers\Admin\PlanController as AdminPlans;
use App\Http\Controllers\Admin\SettingsController as AdminSettings;
use App\Http\Controllers\Admin\UserController as AdminUsers;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointment;
use App\Http\Controllers\Patient\AuditLogController;
use App\Http\Controllers\Patient\ConsentController;
use App\Http\Controllers\Patient\ConsentRequestController as PatientConsentRequest;
use App\Http\Controllers\Patient\DashboardController as PatientDashboard;
use App\Http\Controllers\Patient\DependentController;
use App\Http\Controllers\Patient\FamilySummaryController;
use App\Http\Controllers\Patient\LabUploadController;
use App\Http\Controllers\Patient\NotificationController as PatientNotifications;
use App\Http\Controllers\Patient\EncounterPrintController as PatientEncounterPrint;
use App\Http\Controllers\Patient\ProfileController as PatientProfile;
use App\Http\Controllers\Patient\RecordController;
use App\Http\Controllers\Patient\RefillRequestController as PatientRefill;
use App\Http\Controllers\Patient\VitalLogController;
use App\Http\Controllers\Pharmacy\DispenseController;
use App\Http\Controllers\Provider\AppointmentController as ProviderAppointment;
use App\Http\Controllers\Provider\ConsentRequestController as ProviderConsentRequest;
use App\Http\Controllers\Provider\EncounterPrintController;
use App\Http\Controllers\Provider\LabResultController;
use App\Http\Controllers\Provider\ReferralController;
use App\Http\Controllers\Provider\RefillRequestController as ProviderRefill;
use App\Http\Controllers\Provider\WorkstationController;
use App\Http\Controllers\AvatarController;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────────────────────────
Route::get('/', fn () => view('landing'))->name('home');

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->middleware('throttle:3,1');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::get('/verify-otp',  [AuthController::class, 'showOtp'])->name('auth.otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:5,1');

    Route::get('/forgot-password',         [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password',        [ForgotPasswordController::class, 'sendLink'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/reset-password/{token}',  [ResetPasswordController::class, 'showForm'])->name('password.reset');
    Route::post('/reset-password',         [ResetPasswordController::class, 'reset'])->middleware('throttle:5,15')->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Shared (all authenticated roles) ─────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/profile/avatar', [AvatarController::class, 'update'])->name('profile.avatar');
});

// ─── Patient ──────────────────────────────────────────────────────────────────
Route::prefix('patient')->middleware(['auth', 'role:patient'])->name('patient.')->group(function () {
    Route::get('/welcome',                [PatientDashboard::class, 'welcome'])->name('welcome');
    Route::get('/dashboard',              [PatientDashboard::class, 'index'])->name('dashboard');
    Route::get('/record/{tab?}',          [RecordController::class, 'index'])->name('record');
    Route::get('/prescription/{id}',      [RecordController::class, 'prescription'])->name('prescription');

    // Consents
    Route::get('/consents',               [ConsentController::class, 'index'])->name('consents');
    Route::patch('/consents/{id}/revoke', [ConsentController::class, 'revoke'])->name('consents.revoke');
    Route::patch('/consents/{id}/approve',[PatientConsentRequest::class, 'approve'])->name('consents.approve');
    Route::delete('/consents/{id}/decline',[PatientConsentRequest::class, 'decline'])->name('consents.decline');

    // Access code
    Route::get('/access-code',            [PatientDashboard::class, 'showAccessCode'])->name('access-code');
    Route::post('/access-code/generate',  [PatientDashboard::class, 'generateCode'])->name('access-code.generate');
    Route::post('/access-code/{id}/sms',  [PatientDashboard::class, 'sendSms'])->name('access-code.sms')->middleware('throttle:3,5');

    // Audit log
    Route::get('/audit-log',              [AuditLogController::class, 'index'])->name('audit-log');

    // Family / dependents
    Route::get('/family',                 [DependentController::class, 'index'])->name('family');
    Route::get('/family/add',             [DependentController::class, 'create'])->name('family.add');
    Route::post('/family',                [DependentController::class, 'store'])->name('family.store');

    // Profile
    Route::get('/profile',                [PatientProfile::class, 'edit'])->name('profile');
    Route::put('/profile',                [PatientProfile::class, 'update'])->name('profile.update');
    Route::put('/profile/password',       [PatientProfile::class, 'changePassword'])->name('profile.password');
    Route::post('/profile/2fa',           [PatientProfile::class, 'toggle2fa'])->name('profile.2fa');

    // Appointments
    Route::get('/appointments',           [PatientAppointment::class, 'index'])->name('appointments');
    Route::get('/appointments/book',      [PatientAppointment::class, 'create'])->name('appointments.create');
    Route::post('/appointments',          [PatientAppointment::class, 'store'])->name('appointments.store');
    Route::post('/appointments/{id}/cancel', [PatientAppointment::class, 'cancel'])->name('appointments.cancel');

    // Vitals diary
    Route::get('/vitals',                 [VitalLogController::class, 'index'])->name('vitals');
    Route::post('/vitals',                [VitalLogController::class, 'store'])->name('vitals.store');
    Route::delete('/vitals/{id}',         [VitalLogController::class, 'destroy'])->name('vitals.destroy');

    // Refill requests
    Route::get('/refills',                [PatientRefill::class, 'index'])->name('refills');
    Route::post('/refills',               [PatientRefill::class, 'store'])->name('refills.store');

    // Lab upload
    Route::get('/lab-upload',             [LabUploadController::class, 'create'])->name('lab-upload');
    Route::post('/lab-upload',            [LabUploadController::class, 'store'])->name('lab-upload.store');
    Route::get('/lab-results/{id}/download', [LabUploadController::class, 'download'])->name('lab.download');

    // Notifications
    Route::get('/notifications',          [PatientNotifications::class, 'index'])->name('notifications');
    Route::delete('/notifications/{id}',  [PatientNotifications::class, 'destroy'])->name('notifications.destroy');

    // Audit log CSV export
    Route::get('/audit-log/export',       [AuditLogController::class, 'export'])->name('audit-log.export');

    // Family health summary
    Route::get('/family/summary',         [FamilySummaryController::class, 'index'])->name('family.summary');

    // Encounter PDF export (patient can print their own encounters)
    Route::get('/encounters/{id}/print',  [PatientEncounterPrint::class, 'show'])->name('encounter.print');
});

// ─── Provider ─────────────────────────────────────────────────────────────────
Route::prefix('provider')->middleware(['auth', 'role:provider'])->name('provider.')->group(function () {
    Route::get('/dashboard',  [WorkstationController::class, 'dashboard'])->name('dashboard');
    Route::get('/search',     [WorkstationController::class, 'search'])->name('search');

    // Specific sub-paths MUST come before the {tab?} wildcard
    Route::get('/patients/{id}/encounter/new',   [WorkstationController::class, 'newEncounter'])->name('encounter.new');
    Route::post('/patients/{id}/encounter',      [WorkstationController::class, 'storeEncounter'])->name('encounter.store');
    Route::get('/patients/{id}/encounters/{eid}/print', [EncounterPrintController::class, 'show'])->name('encounter.print');
    Route::get('/patients/{id}/break-glass',     [WorkstationController::class, 'breakGlass'])->name('break-glass');
    Route::post('/patients/{id}/break-glass',    [WorkstationController::class, 'storeBreakGlass'])->name('break-glass.store');
    Route::get('/patients/{id}/consent-request', [ProviderConsentRequest::class, 'create'])->name('consent-request.create');
    Route::post('/patients/{id}/consent-request',[ProviderConsentRequest::class, 'store'])->name('consent-request.store');
    Route::get('/patients/{id}/refer',           [ReferralController::class, 'create'])->name('referral.create');
    Route::post('/patients/{id}/refer',          [ReferralController::class, 'store'])->name('referral.store');
    Route::get('/patients/{id}/lab/new',         [LabResultController::class, 'create'])->name('lab.create');
    Route::post('/patients/{id}/lab',            [LabResultController::class, 'store'])->name('lab.store');

    Route::get('/patients/{id}/{tab?}',          [WorkstationController::class, 'patient'])->name('patient');

    // Provider appointments
    Route::get('/appointments',                  [ProviderAppointment::class, 'index'])->name('appointments');
    Route::post('/appointments/{id}/confirm',    [ProviderAppointment::class, 'confirm'])->name('appointments.confirm');
    Route::post('/appointments/{id}/complete',   [ProviderAppointment::class, 'complete'])->name('appointments.complete');
    Route::post('/appointments/{id}/no-show',    [ProviderAppointment::class, 'noShow'])->name('appointments.no-show');

    // Refill requests
    Route::get('/refills',                       [ProviderRefill::class, 'index'])->name('refills');
    Route::post('/refills/{id}/approve',         [ProviderRefill::class, 'approve'])->name('refills.approve');
    Route::post('/refills/{id}/deny',            [ProviderRefill::class, 'deny'])->name('refills.deny');

    // Referral inbox
    Route::get('/referrals',                     [ReferralController::class, 'index'])->name('referrals');
    Route::patch('/referrals/{id}/status',       [ReferralController::class, 'updateStatus'])->name('referrals.status');
});

// ─── Pharmacy ─────────────────────────────────────────────────────────────────
Route::prefix('pharmacy')->middleware(['auth', 'role:pharmacist'])->name('pharmacy.')->group(function () {
    Route::get('/dashboard',      [DispenseController::class, 'dashboard'])->name('dashboard');
    Route::get('/lookup',         [DispenseController::class, 'lookup'])->name('lookup');
    Route::post('/dispense/{id}', [DispenseController::class, 'dispense'])->name('dispense');
    Route::get('/history',        [DispenseController::class, 'history'])->name('history');
});

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::prefix('admin')->middleware(['auth', 'role:facility_admin,super_admin'])->name('admin.')->group(function () {
    Route::get('/dashboard',               [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/facilities',              [AdminDashboard::class, 'facilities'])->name('facilities');
    Route::get('/break-glass',             [AdminDashboard::class, 'breakGlass'])->name('break-glass');
    Route::post('/break-glass/{id}/approve',[AdminBreakGlass::class, 'approve'])->name('break-glass.approve');
    Route::post('/break-glass/{id}/dispute',[AdminBreakGlass::class, 'dispute'])->name('break-glass.dispute');
    Route::post('/break-glass/{id}/close', [AdminBreakGlass::class, 'close'])->name('break-glass.close');
    Route::get('/providers',               [MdcnController::class, 'providers'])->name('providers');
    Route::post('/providers/{id}/verify',  [MdcnController::class, 'verify'])->name('mdcn.verify');

    // Settings
    Route::get('/settings',               [AdminSettings::class, 'index'])->name('settings');
    Route::post('/settings',              [AdminSettings::class, 'update'])->name('settings.update');
    Route::post('/settings/smtp-test',    [AdminSettings::class, 'testSmtp'])->name('settings.smtp-test');
    Route::post('/settings/sms-test',     [AdminSettings::class, 'testSms'])->name('settings.sms-test');

    // Broadcast notifications
    Route::get('/notifications',          [AdminBroadcast::class, 'index'])->name('notifications');
    Route::post('/notifications',         [AdminBroadcast::class, 'store'])->name('notifications.send');

    // Users & Roles (super_admin only — enforced inside controller)
    Route::get('/users',                         [AdminUsers::class, 'index'])->name('users');
    Route::get('/users/create',                  [AdminUsers::class, 'create'])->name('users.create');
    Route::post('/users',                        [AdminUsers::class, 'store'])->name('users.store');
    Route::get('/users/{id}',                    [AdminUsers::class, 'show'])->name('users.show');
    Route::put('/users/{id}',                    [AdminUsers::class, 'update'])->name('users.update');
    Route::patch('/users/{id}/toggle-active',    [AdminUsers::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{id}/reset-password',    [AdminUsers::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/users/{id}',                 [AdminUsers::class, 'destroy'])->name('users.destroy');

    // Plans & Pricing (super_admin only — enforced inside controller)
    Route::get('/plans',                         [AdminPlans::class, 'index'])->name('plans');
    Route::get('/plans/create',                  [AdminPlans::class, 'create'])->name('plans.create');
    Route::post('/plans',                        [AdminPlans::class, 'store'])->name('plans.store');
    Route::get('/plans/{id}/edit',               [AdminPlans::class, 'edit'])->name('plans.edit');
    Route::put('/plans/{id}',                    [AdminPlans::class, 'update'])->name('plans.update');
    Route::delete('/plans/{id}',                 [AdminPlans::class, 'destroy'])->name('plans.destroy');

    // Facility CSV import (super_admin only — enforced inside controller)
    Route::get('/facilities/import',             [AdminFacilityImport::class, 'create'])->name('facilities.import');
    Route::post('/facilities/import',            [AdminFacilityImport::class, 'store'])->name('facilities.import.store');

    // Team management — facility_admin sees their own team; super_admin sees all
    Route::get('/team',                          [AdminTeam::class, 'index'])->name('team');
    Route::post('/team/add',                     [AdminTeam::class, 'add'])->name('team.add');
    Route::delete('/team/{id}',                  [AdminTeam::class, 'remove'])->name('team.remove');
});
