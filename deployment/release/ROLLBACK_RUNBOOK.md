# IPROFIXER — PRODUCTION ROLLBACK RUNBOOK

If a critical blocker or outage occurs immediately following deployment, execute these emergency rollback steps.

---

## Pre-Rollback Requirements
Before starting, ensure you have:
1. Access to Hostinger File Manager or SSH.
2. The previous release directory backup (e.g. `iprofixer_app_backup_YYYYMMDD_HHMM`).
3. The pre-deployment database dump (e.g. `iprofixer_db_backup_YYYYMMDD_HHMM.sql`).

---

## Step-by-Step Rollback Procedure

### Step 1: Enable Maintenance Mode
Via SSH or Terminal:
```bash
cd /home/u123456789/iprofixer_app
php artisan down --secret="emergency-rollback-bypass"
```

### Step 2: Restore Previous Application Code
1. Rename current app directory:
   ```bash
   mv /home/u123456789/iprofixer_app /home/u123456789/iprofixer_app_failed
   ```
2. Restore previous app directory:
   ```bash
   cp -r /home/u123456789/iprofixer_app_backup_YYYYMMDD_HHMM /home/u123456789/iprofixer_app
   ```
3. Copy production `.env` into restored directory:
   ```bash
   cp /home/u123456789/iprofixer_app_failed/.env /home/u123456789/iprofixer_app/.env
   ```

### Step 3: Restore Database Dump (If Schema Migrations Were Run)
Via Hostinger hPanel > Databases > Import:
1. Select the target database.
2. Import `iprofixer_db_backup_YYYYMMDD_HHMM.sql`.

### Step 4: Re-cache Optimizations
```bash
cd /home/u123456789/iprofixer_app
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 5: Disable Maintenance Mode
```bash
php artisan up
```

### Step 6: Verify Post-Rollback Health
Run basic smoke tests on `/` and `/health` to confirm normal operation.
