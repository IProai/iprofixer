# IPROFIXER — PRODUCTION ENVIRONMENT CHECKLIST

Verify every item before making the production site live.

---

## 1. Application Security Core
- [ ] `APP_ENV` is explicitly set to `production`
- [ ] `APP_DEBUG` is explicitly set to `false` (no stack traces exposed)
- [ ] `APP_KEY` is a fresh 32-byte random base64 string (`php artisan key:generate --show`)
- [ ] `APP_URL` is set to canonical HTTPS domain (`https://www.iprofixer.com`)

## 2. Database Connection
- [ ] `DB_CONNECTION` set to `mysql`
- [ ] Hostinger MySQL database created in hPanel
- [ ] Production DB user created with strong password
- [ ] `DB_CHARSET` set to `utf8mb4` and `DB_COLLATION` set to `utf8mb4_unicode_ci`

## 3. Session & Security
- [ ] `SESSION_DRIVER` set to `database`
- [ ] `SESSION_SECURE_COOKIE` set to `true` (HTTPS only)
- [ ] `SESSION_DOMAIN` set to production domain (`.iprofixer.com`)
- [ ] `TRUSTED_PROXIES` set to `*` for Hostinger proxy layer

## 4. Mail & Notifications
- [ ] `MAIL_MAILER` set to `smtp` (not `log`)
- [ ] `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION` configured
- [ ] `MAIL_FROM_ADDRESS` set to valid domain email (`no-reply@iprofixer.com`)
- [ ] `RFQ_OPERATIONS_EMAIL` set to internal operational team inbox (`ops@iprofixer.com`)

## 5. Storage & Uploads
- [ ] `FILESYSTEM_DISK` set to `local`
- [ ] `storage/app/rfq/` directory exists and is writable by PHP process but NOT in `public_html`
- [ ] `public_html/storage` symlink points to `storage/app/public`

## 6. Hostinger Cron Jobs
- [ ] Cron job 1 (`schedule:run`) configured every minute
- [ ] Cron job 2 (`queue:work database --stop-when-empty`) configured every 5 minutes
