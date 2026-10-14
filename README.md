# MedRecords

**Patient-owned, consent-driven Electronic Health Record (EHR) platform for Nigeria.**

MedRecords gives patients full custody of their medical history. Providers access records only after explicit patient consent or a logged emergency break-glass event. Every access is audited. Built with Nigerian healthcare workflows in mind — NHIA eligibility checks, MDCN provider verification, Paystack/Flutterwave payments, and Twilio SMS.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the App](#running-the-app)
- [Demo Credentials](#demo-credentials)
- [Role Overview](#role-overview)
- [Architecture](#architecture)
- [Security](#security)
- [Admin Panel](#admin-panel)
- [REST API (JWT)](#rest-api-jwt)
- [Project Structure](#project-structure)
- [Deployment](#deployment)

---

## REST API (JWT)

A stateless REST API is exposed under `/api` using JWT authentication
(`tymon/jwt-auth`). Every web endpoint has an API equivalent, with the same
role, verification, 2FA, MDCN and consent gates enforced as JSON responses.

```bash
# Get a token
curl -X POST http://localhost:8000/api/auth/login \
  -H "Accept: application/json" \
  -d '{"login":"patient@example.com","password":"secret"}'

# Use it
curl http://localhost:8000/api/patient/dashboard \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <access_token>"
```

Full endpoint map, 2FA login flow and error semantics: see [API.md](API.md).


---

## Features

### Patient
- Personal health record — encounters, prescriptions, lab results, vitals, vaccinations, allergies, conditions, procedures, hospitalizations
- Consent management — grant and revoke provider access with configurable scope and expiry
- Access codes — generate a time-limited code to share at a reception desk, deliverable by SMS
- Two-factor authentication (OTP via SMS)
- Lab result file uploads (private storage, authenticated download only)
- Appointment booking and medication refill requests
- Family / dependent record management
- Encounter PDF export
- In-app notifications with email delivery
- Full self-service audit log — see exactly who accessed your record and when

### Provider / Doctor
- Patient search by name, phone number, or MedID
- Consent-gated workstation — clinical PHI only loads after active consent or break-glass approval
- Encounter authoring with ICD-10 coding, vitals, history, assessment/plan, inline prescription
- Lab result entry with LOINC code support
- Referral creation and referral inbox
- Appointment schedule view and management
- Refill request review and approval
- Break-glass emergency access (24-hour window, fully audited, admin-reviewed)
- MDCN verification status on provider profile

### Pharmacist
- Prescription lookup by Rx ID
- NHIA insurance eligibility check at point of dispense
- Double-dispense prevention (status + existing record check)
- Full dispense history

### Facility Admin / Super Admin
- User management — create, activate/deactivate, reset passwords
- MDCN provider verification workflow
- Break-glass event review — approve, dispute, or close emergency sessions
- Facility management and CSV bulk import
- Plans and pricing management
- Broadcast notifications — send in-app + email to all users, by role, or a specific user
- Site settings — branding, SMTP, SMS (Twilio), payment gateways (Paystack / Flutterwave)
- Landing page content management

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12 (PHP 8.2+) |
| Database | MySQL / MariaDB 10.4+ |
| Auth | Laravel session auth + OTP 2FA |
| Frontend | Blade templates, Tailwind CSS v4, vanilla JS |
| Build tool | Vite 7 (`npm run dev` / `npm run build`) |
| UI components | Bootstrap 5.3, Chart.js 4 |
| PDF generation | barryvdh/laravel-dompdf |
| SMS | Twilio REST API via Laravel HTTP client |
| Email | SMTP (admin-configurable, any provider) |
| Payments | Paystack / Flutterwave (keys encrypted at rest) |
| File storage | Local disk (non-web-accessible) for lab uploads |
| Sessions | Database driver, encrypted, SameSite=Strict |
| Queue | Database driver |
| Data model | FHIR R4-aligned resource types |

---

## Requirements

- PHP 8.2+ with extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`
- MySQL 8 / MariaDB 10.4+
- Apache 2.4+ with `mod_rewrite` enabled — XAMPP works out of the box
- Composer 2
- Node.js 20+ and npm

---

## Installation

### 1. Place the project

```bash
cd C:\xampp\htdocs
git clone <repository-url> medrecords
cd medrecords
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install JS dependencies

```bash
npm install
```

### 4. Set up the environment file

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Create the database

Create a MySQL database named `medrecords`, then set credentials in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medrecords
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Run migrations and seed demo data

```bash
php artisan migrate --seed
```

### 7. Build frontend assets

```bash
npm run build
```

### 8. Create the private storage symlink

```bash
php artisan storage:link
```

### 9. Cache everything

```bash
php artisan optimize
```

---

## Configuration

### `.env` essentials

```env
APP_NAME=MedRecords
APP_ENV=local          # use 'production' on a live server
APP_DEBUG=true         # set to false in production
APP_URL=http://localhost/medrecords

# Session hardening
SESSION_DRIVER=database
SESSION_LIFETIME=60
SESSION_ENCRYPT=true
SESSION_SAME_SITE=strict
SESSION_SECURE_COOKIE=false   # set to true when behind HTTPS

# Mail — required for notification emails
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourmailprovider.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@medrecords.ng
MAIL_FROM_NAME="MedRecords"
```

### Admin-panel managed settings

The following are configured from **Admin → Settings** and stored encrypted in the database. No `.env` edit required after initial setup.

| Setting | Tab |
|---|---|
| Site name, logo, tagline, footer text | General |
| Contact info (phones, email, address) | Contact |
| Social media links | Social Media |
| SMTP host, port, encryption, credentials, sender identity | Email / SMTP |
| SMS on/off toggle | SMS / Twilio |
| Twilio Account SID, Auth Token, From Number | SMS / Twilio |
| Active payment gateway (None / Paystack / Flutterwave) | Payments |
| Paystack public + secret keys | Payments |
| Flutterwave public + secret keys | Payments |
| Landing page hero headline, subtext, CTA buttons | Landing Page |

---

## Running the App

### Option A — XAMPP Apache (recommended)

1. Open the XAMPP Control Panel
2. Start **Apache** and **MySQL**
3. Visit `http://localhost/medrecords/`

A root `.htaccess` transparently forwards all requests into `public/` so `/public` never appears in the URL.

> MySQL **must** be running before loading any page. Sessions, cache, and queues all use the database driver — if MySQL is down, every request fails.

### Option B — Artisan + Vite dev server

Run everything in one command (PHP server, queue worker, log streamer, Vite HMR):

```bash
composer run dev
```

Or start them separately:

```bash
php artisan serve --host=127.0.0.1 --port=8085
npm run dev
php artisan queue:listen --timeout=60
```

Then visit `http://127.0.0.1:8085`

### Scheduled tasks

```bash
# Add to crontab (Linux/macOS) or Windows Task Scheduler
* * * * * php /path/to/medrecords/artisan schedule:run >> /dev/null 2>&1
```

This runs the daily consent-expiry notification at 08:00.

---

## Demo Credentials

All demo accounts use the password: **`password`**

| Role | Email |
|---|---|
| Patient | adaeze@gmail.com |
| Provider — Doctor | tunde@reddington.ng |
| Provider — Dentist | bola@smilebright.ng |
| Pharmacist | okafor@healthplus.ng |
| Facility Admin | admin@medrecords.ng |

---

## Role Overview

| Role | URL prefix | Access |
|---|---|---|
| `patient` | `/patient/*` | Own health record, consents, access codes, notifications |
| `provider` | `/provider/*` | Consent-gated workstation, encounters, labs, referrals, refills |
| `pharmacist` | `/pharmacy/*` | Dispense queue, Rx lookup, history |
| `facility_admin` | `/admin/*` | Facilities, users, break-glass review, settings |
| `super_admin` | `/admin/*` | Everything above plus plans, system-wide settings |

Role is checked on every request by `RoleMiddleware`, which also immediately logs out deactivated accounts.

---

## Architecture

### Consent gate

Providers cannot read clinical PHI without one of:

- An **active Consent** — patient-approved, with scope (full / summary / labs-only) and optional expiry date
- An **active Break-glass Event** — emergency override, 24-hour window, mandatory written justification, automatically notifies the patient and flags for admin review

`requireConsent(int $patientId)` on the base `Controller` class enforces this. It is called at the start of every provider action that reads or writes PHI. The patient workstation view (`WorkstationController::patient`) lazy-loads PHI relations — encounters, prescriptions, lab results, vaccinations — only after the gate passes.

### Audit log

Every significant event writes to `audit_logs`:
- PHI view (consent-gated and break-glass)
- Encounter created / signed
- Prescription issued
- Dispense recorded
- Consent granted / revoked
- Break-glass invoked

Patients can view their complete log from the app. The log is append-only — rows are never updated or deleted.

### Email notifications

Every row created in the `notifications` table — from any code path, whether a consent request, break-glass alert, or admin broadcast — automatically dispatches a `UserNotificationMail` email via a `booted()` model observer. Failed sends are logged as warnings and never break the originating request.

### Data model

```
users ──┬── patients ──┬── encounters ──── prescriptions
        │              ├── lab_results
        │              ├── allergies / conditions / vaccinations
        │              ├── vital_logs / appointments / refill_requests
        │              ├── dependents
        │              ├── access_codes
        │              └── consents ◄──── providers
        │
        └── providers ─┬── facilities
                       ├── break_glass_events
                       └── referrals

audit_logs ◄── (every role writes here)
notifications ◄── (every role reads here; email auto-sent on insert)
site_settings ── (key/value, per-request cached)
```

---

## Security

| Control | Detail |
|---|---|
| Session fixation | Session ID regenerated after login, 2FA verify, logout, and password change |
| 2FA OTP | 6-digit code via SMS, `hash_equals()` comparison, 10-minute expiry, cleared after use |
| Email verification OTP | 6-digit code emailed at registration, bcrypt-stored, 10-minute expiry, 5-attempt lockout, throttled to 6 submits / min and 3 resends / min |
| Consent gate | `requireConsent()` on all provider PHI endpoints — aborts 403 if no consent or break-glass |
| IDOR prevention | All queries scoped to authenticated user's `patient_id` or `user_id` before `findOrFail()` |
| Double-dispense | Prescription `status` + existing `Dispense` row checked before processing |
| Duplicate break-glass | Blocked if an active session already exists for the same provider/patient pair |
| Lab file privacy | Stored on `local` disk (not web-accessible); served only through an authenticated download route |
| SMS rate limiting | `throttle:3,5` middleware on the access-code SMS endpoint (3 sends per 5 minutes) |
| Encrypted secrets | Twilio auth token, Paystack and Flutterwave secret keys encrypted via Laravel `Crypt` |
| Security headers | `X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`, `Referrer-Policy`, `Permissions-Policy`, `Content-Security-Policy` |
| HTTPS enforcement | `URL::forceScheme('https')` active in production environment |
| Session hardening | `SESSION_ENCRYPT=true`, `SESSION_SAME_SITE=strict`, configurable secure cookie |
| Account enumeration | Registration error messages say "unavailable" not "already registered" |
| Med ID entropy | `Str::random(6)` × 2 — approximately 2.2 billion combinations |
| Other sessions on password change | All other DB sessions for the user are deleted; current session is regenerated |

---

## Admin Panel

### Send Notification — `/admin/notifications`

Compose a title and message body, choose an audience, and send:

- **All Users** — every active account
- **By Role** — e.g. all patients, all providers
- **Specific User** — looked up by email or MedID

Each dispatch creates one in-app notification and sends one email per recipient. Broadcasts are tagged with a UUID so the history table shows one row per send (not one per recipient), with recipient count, sender, and timestamp.

### Settings — `/admin/settings`

**SMS / Twilio tab** — toggle SMS on or off globally, enter Twilio credentials. Nigerian numbers (`08XXXXXXXXXX`) are automatically normalised to E.164 (`+234XXXXXXXXX`). A test-send button verifies the configuration immediately.

**Payments tab** — choose None, Paystack, or Flutterwave. Only the selected gateway's key fields are shown. Secret keys are stored encrypted; leaving the field blank on subsequent saves preserves the existing key.

---

## Project Structure

```
app/
  Http/
    Controllers/
      Admin/       — Dashboard, Users, Settings, BroadcastNotification, MDCN, BreakGlass
      Patient/     — Profile, Record, Consents, LabUpload, Vitals, Notifications, …
      Provider/    — Workstation, Encounters, Labs, Referrals, Refills, Appointments
      Pharmacy/    — Dispense, Lookup, History
    Middleware/
      RoleMiddleware.php      — role check + deactivation logout
      SecurityHeaders.php     — HTTP security headers on every response
  Mail/
    UserNotificationMail.php
  Models/          — 26 Eloquent models
  Services/
    SmsService.php            — Twilio via HTTP client, E.164 normalisation
    NhiaService.php           — NHIA eligibility check

resources/views/
  layouts/
    app.blade.php             — Main shell: role-aware sidebar, dark mode toggle
    auth.blade.php            — Auth pages shell
  components/
    phone-input.blade.php     — Country flag + dial code picker (39 countries, searchable)
  patient/                    — Dashboard, record, profile, consents, access codes, vitals, …
  provider/                   — Workstation, encounter forms, patient chart, print view, …
  pharmacy/                   — Lookup, dispense form, history
  admin/                      — Dashboard, users, settings, notifications, facilities, …
  emails/
    user-notification.blade.php  — Responsive HTML email template
  errors/                     — Custom 403, 404, 419, 429, 500, 503 pages

database/migrations/          — 35 migrations covering full schema

routes/web.php                — ~80 named routes across 5 role groups
```

---

## Deployment

See [DEPLOY.md](DEPLOY.md) for the complete production runbook:

- Server requirements and PHP extension checklist
- First-deploy steps (composer, migrate, optimize)
- Apache virtual host configuration (point `DocumentRoot` at `public/`)
- Cron setup for the task scheduler
- Queue worker systemd service configuration
- Post-deploy verification checklist
