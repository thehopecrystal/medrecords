# MedRecords REST API

Stateless JWT authentication (tymon/jwt-auth) exposed under `/api`. Every web
endpoint has an API equivalent; all responses are JSON (except file downloads).

## Authentication

Send the token on every request:

```
Authorization: Bearer <access_token>
Accept: application/json
```

Two JWT guards are used, resolved automatically at login:

| Guard       | Accounts                                              |
|-------------|-------------------------------------------------------|
| `api`       | users — patient, provider, pharmacist                 |
| `admin-api` | admins — facility_admin, super_admin                  |

### POST /api/auth/login

`{ "login": "email-or-phone", "password": "..." }`

**200 (no 2FA)** — `access_token`, `token_type: bearer`, `expires_in`, `role`,
`email_verified`, `two_factor_enabled`, `user`.

**200 (2FA enabled)** — `requires_2fa: true`, a short-lived (5 min) **pending**
token and `otp_channels` (`sms`/`email`). The pending token only works on the
endpoints listed below until the OTP is verified:

```
POST /api/auth/2fa/verify   { "otp": "123456" }   -> full token
GET  /api/auth/me
POST /api/auth/refresh
POST /api/auth/logout
GET|POST /api/account/2fa   (2FA setup)
```

Super admins bypass 2FA-at-login (mirrors the web flow).

### Other auth endpoints

| Method | Endpoint                  | Notes                                        |
|--------|---------------------------|----------------------------------------------|
| POST   | `/api/auth/register`      | `reg_type`: patient (default), provider, hospital, clinic, pharmacy |
| POST   | `/api/auth/refresh`       | rotate token (blacklists the old one)        |
| POST   | `/api/auth/logout`        | invalidate token                             |
| GET    | `/api/auth/me`            | current user + role + patient profile        |
| POST   | `/api/auth/forgot-password` | `{email}`                                  |
| POST   | `/api/auth/reset-password`  | `{token, email, password, password_confirmation}` |
| GET    | `/api/auth/email/status`  | verification state (proactively issues OTP)  |
| POST   | `/api/auth/email/verify`  | `{code}` — 6-digit email OTP                 |
| POST   | `/api/auth/email/resend`  | new OTP (throttled 3/min)                    |
| GET    | `/api/account/2fa`        | 2FA state                                    |
| POST   | `/api/account/2fa`        | toggle (`current_password` required to disable) |
| POST   | `/api/account/avatar`     | multipart `avatar` (jpeg/png/webp, <=2 MB)   |

## Authorization

Mirrors the web app exactly:

- Role gates per section (`role:patient` / `provider` / `pharmacist` / `facility_admin,super_admin`).
- `verified` email middleware → **409** until the email OTP is confirmed.
- `require.2fa` for provider/pharmacist/admin roles → **403** `two_factor_required` until 2FA is enabled.
- `mdcn.verified` for provider patient-data routes → **403** `provider_pending_verification`.
- Super-admin-only endpoints (users, plans, facility import) → **403** inside the controller.

## Endpoint map

### Patient (`/api/patient/...`)

`welcome`, `dashboard`, `record/{tab?}`, `prescriptions/{id}`, `consents`
(+ `revoke`/`renew`/`approve`/`decline`), `access-code` (+ `generate`, `sms`),
`audit-log` (+ `export` CSV), `family` (+ `options`, `store`, `summary`),
`profile` (+ `update`, `password`, `2fa`), `appointments` (+ `options`, `store`,
`cancel`), `vitals` (+ `store`, `destroy`), `refills` (+ `store`),
`lab-upload` (+ `options`, `store`), `lab-results/{id}/download`,
`notifications` (+ `destroy`), `encounters/{id}/print` (PDF), `export` (PDF).

### Provider (`/api/provider/...`)

`dashboard`, `search?q=`, `pending-verification`, `patients/{id}` (`?tab=`),
`patients/{id}/encounters/options`, `POST patients/{id}/encounters`
(`sign` bool, optional inline prescription fields),
`patients/{id}/encounters/{eid}/print` (PDF), `patients/{id}/break-glass`
(GET context + POST store), `patients/{id}/consent-request` (GET/POST),
`patients/{id}/referrals` (POST), `patients/{id}/referrals/options`,
`patients/{id}/labs` (POST), `patients/{id}/labs/options`,
`appointments?date=` (+ `confirm`/`complete`/`no-show`), `refills`
(+ `approve`/`deny`), `referrals` (+ `PATCH status`).

### Pharmacy (`/api/pharmacy/...`)

`dashboard`, `lookup?rx_id=`, `POST dispense/{id}` (locked transaction,
double-dispense guard), `history`.

### Admin (`/api/admin/...`)

`dashboard`, `facilities`, `break-glass` (+ `approve`/`dispute`/`close`),
`providers` (+ `POST {id}/verify` MDCN), `settings` (+ `update`,
`smtp-test`, `sms-test`), `notifications` (broadcast + GET history),
`users` (+ `options`, `store`, `show`, `update`, `toggle-active`,
`reset-password`, `destroy`), `plans` (+ `options`, `store`, `show`, `update`,
`destroy`), `facilities/import` (GET spec + POST CSV), `team` (+ `add`, `remove`).

## Errors

| Status | Meaning                                                     |
|--------|-------------------------------------------------------------|
| 401    | Missing/invalid/blacklisted token or deactivated account    |
| 403    | Role/MDCN/2FA/consent gate, or forbidden resource           |
| 404    | Resource not found                                          |
| 409    | Email not verified (`verified` middleware)                  |
| 422    | Validation errors (`errors` object) or business rule        |
| 429    | Rate limited (per-user limiters mirror the web throttles)   |

## Configuration

`.env` keys (see `.env.example`): `JWT_SECRET` (generate with
`php artisan jwt:secret`), `JWT_TTL` (minutes, default 60),
`JWT_REFRESH_TTL` (minutes, default 20160).
