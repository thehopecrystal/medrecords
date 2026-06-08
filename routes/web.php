<?php

use App\Http\Controllers\Admin\BreakGlassController as AdminBreakGlass;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\MdcnController;
use App\Http\Controllers\Admin\SettingsController as AdminSettings;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Patient\AuditLogController;
use App\Http\Controllers\Patient\ConsentController;
use App\Http\Controllers\Patient\ConsentRequestController as PatientConsentRequest;
use App\Http\Controllers\Patient\DashboardController as PatientDashboard;
use App\Http\Controllers\Patient\DependentController;
use App\Http\Controllers\Patient\ProfileController as PatientProfile;
use App\Http\Controllers\Patient\RecordController;
use App\Http\Controllers\Pharmacy\DispenseController;
use App\Http\Controllers\Provider\ConsentRequestController as ProviderConsentRequest;
use App\Http\Controllers\Provider\EncounterPrintController;
use App\Http\Controllers\Provider\LabResultController;
use App\Http\Controllers\Provider\ReferralController;
use App\Http\Controllers\Provider\WorkstationController;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────────────────────────
Route::get('/', fn () => view('landing'))->name('home');

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

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
    Route::post('/access-code/{id}/sms',  [PatientDashboard::class, 'sendSms'])->name('access-code.sms');

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
});
