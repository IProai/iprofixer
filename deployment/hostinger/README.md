# IProFixer Hostinger Release Pack

This directory is the controlled deployment source for Hostinger preview and production releases. Deployment is not accepted merely because repository files were copied or a page returned HTTP 200.

## Non-negotiable rules

1. GitHub remains the source of truth.
2. Deploy only a named commit whose CI result is green.
3. Never commit `.env`, credentials, API keys, application keys, database exports, or customer files.
4. The real runtime `.env` is created inside Hostinger from `.env.hostinger.example` and retained outside Git.
5. `APP_DEBUG` stays `false` on every internet-accessible environment.
6. Migrations, bootstrap users, mail, uploads, permissions, health checks, backup and rollback must be verified before acceptance.
7. Preview data is disposable and must not be mixed with production data.

## Required release contents

A deployable release must contain:

- Laravel application source.
- `vendor/` produced from the committed `composer.lock` using production Composer options.
- `public/build/manifest.json` and hashed Vite assets produced from the committed `package-lock.json`.
- Root `.htaccess` copied from `deployment/hostinger/public-root.htaccess`.
- Laravel's existing `public/.htaccess`.
- Writable Laravel runtime directories under `storage/` and `bootstrap/cache/`.
- A real `.env` created from `.env.hostinger.example` with unique secrets and the correct environment database.

## Release artifact build

Build from a clean checkout of the exact green commit. The resulting artifact must exclude development-only and secret-bearing material while retaining runtime dependencies and compiled assets.

Recommended build commands on a machine with PHP, Composer and Node:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
npm ci
npm run build
php artisan test
```

Before packaging, confirm:

```text
composer.lock exists
package-lock.json exists
vendor/autoload.php exists
public/build/manifest.json exists
public/index.php exists
public/.htaccess exists
storage/framework/cache exists
storage/framework/sessions exists
storage/framework/views exists
storage/logs exists
bootstrap/cache exists
```

Do not include a real `.env` in the artifact.

## Hostinger file layout

The Laravel project is deployed under the website's `public_html` directory because the current hosting flow serves that directory. Copy `public-root.htaccess` to `public_html/.htaccess`. It routes browser traffic into `public/` and blocks direct access to sensitive project paths.

Expected structure:

```text
public_html/
  .env                       # created on Hostinger; never in Git
  .htaccess                  # copied from public-root.htaccess
  app/
  bootstrap/
  config/
  database/
  public/
    .htaccess
    index.php
    build/
  resources/
  routes/
  storage/
  vendor/
  artisan
  composer.json
  composer.lock
```

## Runtime environment

Create `public_html/.env` from `.env.hostinger.example` and replace every placeholder. Generate a unique Laravel `APP_KEY` on a trusted machine using:

```bash
php artisan key:generate --show
```

Paste only the generated `base64:` value into Hostinger's `.env`. Do not send it through chat, email or commit history.

Preview defaults intentionally use:

- file sessions;
- file cache;
- synchronous queues;
- log mail transport.

This removes hidden worker and database-table dependencies while the preview is being validated. Production changes require separate proof.

## Database gate

Provision a dedicated Hostinger database for the environment. Populate only the deployed `.env` with its credentials. Database acceptance requires:

- connection succeeds;
- all migrations run against the intended database;
- migration status contains no pending migration;
- required roles and permissions are seeded through the governed bootstrap process;
- no local, test or another environment's data is present.

Never upload a developer database as a substitute for migrations and seeds.

## Writable-directory gate

The web/PHP process must be able to write to:

```text
storage/app
storage/framework/cache
storage/framework/sessions
storage/framework/views
storage/logs
bootstrap/cache
```

Do not make the complete project world-writable. Fix only the required runtime directories using the least-permissive setting supported by the hosting plan.

## Deployment acceptance checks

Every release must pass all of the following:

1. Home page loads over HTTPS without debug output.
2. English and Arabic pages render; Arabic uses true RTL.
3. Compiled CSS and JavaScript load from hashed files in `public/build`.
4. `/health` and `/ready` return the expected non-secret status.
5. Public RFQ validation rejects invalid requests.
6. A valid RFQ persists once, produces a reference, records consent and does not expose private attachments.
7. Authentication works and unauthorized users cannot access CMS, RFQ administration or attachments.
8. Authorized operators can use the implemented CMS/RFQ functions.
9. Logs contain no missing-key, missing-manifest, permission or database exceptions.
10. `APP_DEBUG=false` is confirmed.
11. Backup and rollback points exist before migration or release replacement.

## Rollback

Before each deployment retain:

- the previously accepted release archive;
- the previous green commit SHA;
- a database backup taken before migrations;
- the deployed `.env` outside the release archive;
- uploaded/private storage required by the environment.

Rollback means restoring the previous release files and compatible database state, then repeating the smoke checks. Never overwrite the only known-good release.

## No-SSH hosting constraint

The deployment must not assume terminal or SSH access. Anything required at runtime must be one of:

- already present in the prepared release artifact;
- configurable through Hostinger's file/database/control-panel interfaces;
- executed through a deliberately designed, authenticated, single-use deployment mechanism that is removed immediately after use and separately security-reviewed.

No ad-hoc public PHP command runner is allowed.

## Current known incident captured

The first preview attempt copied application files but did not contain a real `.env`, so Laravel had no `APP_KEY` and returned HTTP 500. This pack prevents recurrence by making the runtime environment, compiled assets, dependencies, rewrite rules and acceptance checks explicit release inputs.

## Media Library Storage & Backup Governance

1. **Storage Directory Permissions**: Ensure `storage/app/public/media` directory has write permissions (`755`).
2. **Symlink Behavior**: Execute `php artisan storage:link` or verify Hostinger web server maps `public/storage` to `storage/app/public`.
3. **Media Backup Policy**:
   - `storage/app/public/media` contains customer and governed media uploads and must NEVER be committed to Git.
   - Backup `storage/app/public/` during routine backup procedures alongside database snapshots.
   - Restoring media requires restoring file paths to match `media_assets` table `disk` and `path` values.
