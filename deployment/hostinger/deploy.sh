#!/usr/bin/env bash

set -Eeuo pipefail

RELEASE_DIR="${1:?release directory is required}"
RELEASE_SHA="${2:?release SHA is required}"
RUN_MIGRATIONS="${3:-false}"

BASE_PATH="${HOSTINGER_BASE_PATH:?HOSTINGER_BASE_PATH is required}"
APP_PATH="$BASE_PATH/iprofixer_app"
WEB_PATH="$BASE_PATH/public_html"
DEPLOY_PATH="$BASE_PATH/.deploy"
BACKUP_PATH="$DEPLOY_PATH/backups/$RELEASE_SHA-$(date +%Y%m%d_%H%M%S)"
PHP_BIN="${HOSTINGER_PHP_BIN:-/opt/alt/php84/usr/bin/php}"
APP_URL="${APP_URL:-https://iprofixer.com}"
LOCK_FILE="$DEPLOY_PATH/deploy.lock"
ROLLBACK_ACTIVE=false

mkdir -p "$DEPLOY_PATH/backups"
exec 9>"$LOCK_FILE"
flock -n 9 || { echo "Another deployment is already running." >&2; exit 1; }

log() {
    printf '[%s] %s\n' "$(date -u +%Y-%m-%dT%H:%M:%SZ)" "$*"
}

require_file() {
    [[ -f "$1" ]] || { echo "Required file missing: $1" >&2; exit 1; }
}

restore_previous_release() {
    [[ "$ROLLBACK_ACTIVE" == false ]] || return 1
    ROLLBACK_ACTIVE=true

    log "Deployment failed. Restoring the previous application and public assets."

    "$PHP_BIN" "$APP_PATH/artisan" down --retry=30 >/dev/null 2>&1 || true

    if [[ -f "$BACKUP_PATH/application.tar.gz" ]]; then
        rollback_dir="$BACKUP_PATH/application"
        mkdir -p "$rollback_dir"
        tar -xzf "$BACKUP_PATH/application.tar.gz" -C "$rollback_dir"
        rsync -a --delete \
            --exclude='.env' \
            --exclude='storage/' \
            --exclude='public/' \
            --exclude='bootstrap/cache/' \
            "$rollback_dir/" "$APP_PATH/"
    fi

    if [[ -d "$BACKUP_PATH/public-build" ]]; then
        rm -rf "$WEB_PATH/build.rollback"
        cp -a "$BACKUP_PATH/public-build" "$WEB_PATH/build.rollback"
        rm -rf "$WEB_PATH/build"
        mv "$WEB_PATH/build.rollback" "$WEB_PATH/build"
    fi

    mkdir -p "$APP_PATH/bootstrap/cache" "$APP_PATH/storage/framework/cache" "$APP_PATH/storage/framework/sessions" "$APP_PATH/storage/framework/views" "$APP_PATH/storage/logs"
    "$PHP_BIN" "$APP_PATH/artisan" optimize:clear >/dev/null 2>&1 || true
    "$PHP_BIN" "$APP_PATH/artisan" optimize >/dev/null 2>&1 || true
    "$PHP_BIN" "$APP_PATH/artisan" up >/dev/null 2>&1 || true

    log "Rollback completed. Backup retained at $BACKUP_PATH"
}

trap restore_previous_release ERR

log "Running deployment preflight checks."
command -v rsync >/dev/null
command -v tar >/dev/null
command -v curl >/dev/null
[[ -x "$PHP_BIN" ]]
[[ -d "$APP_PATH" ]]
[[ -d "$WEB_PATH" ]]
require_file "$APP_PATH/.env"
require_file "$APP_PATH/artisan"
require_file "$RELEASE_DIR/artisan"
require_file "$RELEASE_DIR/vendor/autoload.php"
require_file "$RELEASE_DIR/public/build/manifest.json"
require_file "$RELEASE_DIR/deployment/hostinger/deploy.sh"

log "Creating rollback backup."
mkdir -p "$BACKUP_PATH"
tar -czf "$BACKUP_PATH/application.tar.gz" \
    --exclude='./.env' \
    --exclude='./storage' \
    --exclude='./public' \
    --exclude='./bootstrap/cache' \
    -C "$APP_PATH" .

if [[ -d "$WEB_PATH/build" ]]; then
    cp -a "$WEB_PATH/build" "$BACKUP_PATH/public-build"
fi

log "Putting the application into maintenance mode."
"$PHP_BIN" "$APP_PATH/artisan" down --retry=30 || true

log "Synchronizing application code while preserving production state."
rsync -a --delete \
    --exclude='.env' \
    --exclude='storage/' \
    --exclude='public/' \
    --exclude='bootstrap/cache/' \
    "$RELEASE_DIR/" "$APP_PATH/"

mkdir -p "$APP_PATH/bootstrap/cache" "$APP_PATH/storage/framework/cache" "$APP_PATH/storage/framework/sessions" "$APP_PATH/storage/framework/views" "$APP_PATH/storage/logs"
chmod -R ug+rwX "$APP_PATH/bootstrap/cache" "$APP_PATH/storage"

log "Preparing compiled public assets."
rm -rf "$WEB_PATH/build.next"
cp -a "$RELEASE_DIR/public/build" "$WEB_PATH/build.next"
require_file "$WEB_PATH/build.next/manifest.json"

log "Clearing stale Laravel caches."
"$PHP_BIN" "$APP_PATH/artisan" optimize:clear

if [[ "$RUN_MIGRATIONS" == true ]]; then
    log "Running production database migrations."
    "$PHP_BIN" "$APP_PATH/artisan" migrate --force
else
    log "Database migrations were not requested for this release."
fi

log "Ensuring governed production permissions."
"$PHP_BIN" "$APP_PATH/artisan" db:seed --class=ProductionAccessSeeder --force

log "Building production caches."
"$PHP_BIN" "$APP_PATH/artisan" optimize

log "Atomically switching compiled public assets."
rm -rf "$WEB_PATH/build.previous"
if [[ -d "$WEB_PATH/build" ]]; then
    mv "$WEB_PATH/build" "$WEB_PATH/build.previous"
fi
mv "$WEB_PATH/build.next" "$WEB_PATH/build"

log "Returning the application to service."
"$PHP_BIN" "$APP_PATH/artisan" up

log "Running live health checks."
curl --fail --silent --show-error --retry 5 --retry-delay 3 "$APP_URL/health" >/dev/null
curl --fail --silent --show-error --retry 5 --retry-delay 3 "$APP_URL/ready" >/dev/null
curl --fail --silent --show-error --retry 5 --retry-delay 3 "$APP_URL/login" >/dev/null

trap - ERR
log "Deployment succeeded for $RELEASE_SHA."
log "Rollback backup: $BACKUP_PATH"

find "$DEPLOY_PATH/backups" -mindepth 1 -maxdepth 1 -type d -printf '%T@ %p\n' \
    | sort -nr \
    | tail -n +6 \
    | cut -d' ' -f2- \
    | xargs -r rm -rf
