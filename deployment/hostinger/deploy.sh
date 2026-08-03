#!/usr/bin/env bash

set -Eeuo pipefail

RELEASE_DIR="${1:?release directory is required}"
RELEASE_SHA="${2:?release SHA is required}"
RUN_MIGRATIONS="${3:-false}"

BASE_PATH="${HOSTINGER_BASE_PATH:?HOSTINGER_BASE_PATH is required}"
APP_PATH="$BASE_PATH/iprofixer_app"
WEB_PATH="$BASE_PATH/public_html"
DEPLOY_PATH="$BASE_PATH/.deploy"
PHP_BIN="${HOSTINGER_PHP_BIN:-/opt/alt/php84/usr/bin/php}"
APP_URL="${APP_URL:-https://iprofixer.com}"
LOCK_FILE="$DEPLOY_PATH/deploy.lock"

log() {
    printf '[%s] %s\n' "$(date -u +%Y-%m-%dT%H:%M:%SZ)" "$*"
}

require_file() {
    [[ -f "$1" ]] || { echo "Required file missing: $1" >&2; exit 1; }
}

recover_service() {
    if [[ -x "$PHP_BIN" && -f "$APP_PATH/artisan" ]]; then
        "$PHP_BIN" "$APP_PATH/artisan" up >/dev/null 2>&1 || true
    fi
}

# Never leave production in Laravel maintenance mode, including on interruption.
trap recover_service EXIT INT TERM

mkdir -p "$DEPLOY_PATH"
recover_service

exec 9>"$LOCK_FILE"
flock -n 9 || {
    log "Another deployment is active; ensuring the current site remains online."
    recover_service
    exit 1
}

log "Running emergency-safe deployment preflight."
command -v flock >/dev/null
command -v rsync >/dev/null
command -v curl >/dev/null
[[ -x "$PHP_BIN" ]]
[[ -d "$APP_PATH" ]]
[[ -d "$WEB_PATH" ]]
require_file "$APP_PATH/.env"
require_file "$APP_PATH/artisan"
require_file "$WEB_PATH/index.php"
require_file "$RELEASE_DIR/artisan"
require_file "$RELEASE_DIR/vendor/autoload.php"
require_file "$RELEASE_DIR/public/build/manifest.json"

log "Synchronizing application code without taking the website offline."
rsync -a --delete \
    --exclude='.env' \
    --exclude='storage/' \
    --exclude='public/' \
    --exclude='bootstrap/cache/' \
    "$RELEASE_DIR/" "$APP_PATH/"

mkdir -p \
    "$APP_PATH/bootstrap/cache" \
    "$APP_PATH/storage/framework/cache" \
    "$APP_PATH/storage/framework/sessions" \
    "$APP_PATH/storage/framework/views" \
    "$APP_PATH/storage/logs" \
    "$APP_PATH/public"
chmod -R ug+rwX "$APP_PATH/bootstrap/cache" "$APP_PATH/storage"

log "Synchronizing public files."
rsync -a --delete \
    --exclude='index.php' \
    --exclude='.htaccess' \
    --exclude='storage' \
    --exclude='build/' \
    "$RELEASE_DIR/public/" "$WEB_PATH/"

log "Preparing matching Vite builds."
rm -rf "$WEB_PATH/build.next" "$APP_PATH/public/build.next"
cp -a "$RELEASE_DIR/public/build" "$WEB_PATH/build.next"
cp -a "$RELEASE_DIR/public/build" "$APP_PATH/public/build.next"
require_file "$WEB_PATH/build.next/manifest.json"
require_file "$APP_PATH/public/build.next/manifest.json"
cmp -s "$WEB_PATH/build.next/manifest.json" "$APP_PATH/public/build.next/manifest.json"

if [[ "$RUN_MIGRATIONS" == true ]]; then
    log "Running production migrations."
    "$PHP_BIN" "$APP_PATH/artisan" migrate --force
fi

log "Switching compiled assets."
rm -rf "$WEB_PATH/build.previous" "$APP_PATH/public/build.previous"
[[ ! -d "$WEB_PATH/build" ]] || mv "$WEB_PATH/build" "$WEB_PATH/build.previous"
[[ ! -d "$APP_PATH/public/build" ]] || mv "$APP_PATH/public/build" "$APP_PATH/public/build.previous"
mv "$WEB_PATH/build.next" "$WEB_PATH/build"
mv "$APP_PATH/public/build.next" "$APP_PATH/public/build"

log "Refreshing Laravel caches."
rm -f "$APP_PATH/storage/framework/views"/*.php || true
"$PHP_BIN" "$APP_PATH/artisan" optimize:clear
"$PHP_BIN" "$APP_PATH/artisan" optimize
"$PHP_BIN" "$APP_PATH/artisan" up

log "Verifying production endpoints."
curl --fail --silent --show-error --connect-timeout 10 --max-time 30 --retry 3 --retry-delay 2 "$APP_URL/health" >/dev/null
curl --fail --silent --show-error --connect-timeout 10 --max-time 30 --retry 3 --retry-delay 2 "$APP_URL/ready" >/dev/null
curl --fail --silent --show-error --connect-timeout 10 --max-time 30 --retry 3 --retry-delay 2 "$APP_URL/" >/dev/null

app_css=$("$PHP_BIN" -r '$m=json_decode(file_get_contents($argv[1]), true); echo $m["resources/css/app.css"]["file"] ?? "";' "$APP_PATH/public/build/manifest.json")
app_js=$("$PHP_BIN" -r '$m=json_decode(file_get_contents($argv[1]), true); echo $m["resources/js/app.js"]["file"] ?? "";' "$APP_PATH/public/build/manifest.json")
[[ -n "$app_css" && -n "$app_js" ]]
curl --fail --silent --show-error --head --connect-timeout 10 --max-time 30 "$APP_URL/build/$app_css" >/dev/null
curl --fail --silent --show-error --head --connect-timeout 10 --max-time 30 "$APP_URL/build/$app_js" >/dev/null

log "Emergency-safe deployment succeeded for $RELEASE_SHA."
