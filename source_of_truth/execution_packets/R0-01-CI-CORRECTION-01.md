# R0-01 CI Correction 01

## Classification

`CI_FAILURE_CORRECTED_PENDING_RERUN`

## Observed failure

GitHub Actions run `30461637886` failed during `actions/setup-node` before application dependencies or tests executed.

Exact cause:

- npm caching was enabled.
- No `package-lock.json`, `npm-shrinkwrap.json`, or `yarn.lock` existed.
- `actions/setup-node` therefore terminated the job before dependency installation.

## Correction

- Removed premature npm cache configuration.
- Temporarily changed frontend installation from `npm ci` to `npm install --no-audit --no-fund` until the canonical lockfile is generated and committed.
- Added `agent/**` to push-trigger branches.
- Corrected the PostgreSQL health command to use the configured user and database.
- Added SQLite extensions needed by the current PHPUnit configuration.
- Added required Laravel runtime directories before `artisan key:generate`.

## Non-claims

- CI is not yet classified as green.
- Dependency lockfiles are not yet canonical.
- This correction does not accept or merge PR #2.

## Next gate

The next GitHub Actions run must proceed beyond Node setup and expose the next actual dependency, bootstrap, migration, formatting, build, or test result.
