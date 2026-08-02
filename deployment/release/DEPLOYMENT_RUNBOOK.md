# IPROFIXER — HOSTINGER PRODUCTION DEPLOYMENT RUNBOOK

## Overview
This runbook defines the exact deployment procedure for publishing IProFixer Production V1 onto Hostinger shared or VPS web hosting.

---

## Target Hostinger Directory Structure
Hostinger web root for primary domain is `public_html`.
To keep Laravel application files secure outside the public web root:

```text
/home/u123456789/
├── iprofixer_app/                  <-- Application root (all codebase except public/)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env                       <-- Production secrets (NOT in public_html)
│   └── artisan
└── public_html/                    <-- Public web root (contents of Laravel public/ directory)
    ├── index.php
    ├── favicon.ico
    ├── robots.txt
    ├── .htaccess
    └── build/                      <-- Compiled Vite assets
```

---

## STEP-BY-STEP DEPLOYMENT PROCEDURE

### Step 1: Upload Release Package
1. Download `iprofixer-production-v1.zip` and verify its SHA-256 checksum against the release notes.
2. Log into Hostinger hPanel > File Manager (or connect via SSH/SFTP).
3. Upload `iprofixer-production-v1.zip` to your user home directory (above `public_html`).
4. Extract the ZIP into `/home/u123456789/iprofixer_app/`.

### Step 2: Configure Public Web Root (`public_html`)
1. Move all files from `/home/u123456789/iprofixer_app/public/` into `/home/u123456789/public_html/`.
2. Edit `/home/u123456789/public_html/index.php`:
   - Change line: `require __DIR__.'/../vendor/autoload.php';`
     To: `require __DIR__.'/../iprofixer_app/vendor/autoload.php';`
   - Change line: `$app = require_once __DIR__.'/../bootstrap/app.php';`
     To: `$app = require_once __DIR__.'/../iprofixer_app/bootstrap/app.php';`

### Step 3: Configure Hostinger Production `.env`
1. Copy `.env.hostinger.example` to `/home/u123456789/iprofixer_app/.env`.
2. Fill in database credentials, mail settings, `APP_KEY`, and `APP_URL`.
3. Set `APP_ENV=production` and `APP_DEBUG=false`.

### Step 4: Storage Symlink
Via Hostinger SSH or Terminal:
```bash
ln -s /home/u123456789/iprofixer_app/storage/app/public /home/u123456789/public_html/storage
```
(Alternative via index.php if no SSH access: see Hostinger hPanel File Manager).

### Step 5: Database Migration & Seeding
Via Hostinger SSH or Terminal:
```bash
cd /home/u123456789/iprofixer_app
php artisan migrate --force
php artisan db:seed --class=CmsPermissionSeeder --force
php artisan db:seed --class=NavigationSeeder --force
```

### Step 6: Production Caching
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 7: Hostinger Cron Setup (Queue & Scheduler)
In Hostinger hPanel > Advanced > Cron Jobs, add:

1. **Scheduled Content Publisher & Tasks** (Every Minute):
   ```text
   * * * * * cd /home/u123456789/iprofixer_app && php artisan schedule:run >> /dev/null 2>&1
   ```

2. **Database Queue Worker** (Every 5 Minutes):
   ```text
   */5 * * * * cd /home/u123456789/iprofixer_app && php artisan queue:work database --stop-when-empty --tries=3 >> /dev/null 2>&1
   ```

---

## Verification
Follow `SMOKE_TEST_CHECKLIST.md` to confirm production health.
