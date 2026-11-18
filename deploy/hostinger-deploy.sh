#!/usr/bin/env bash
# Hostinger cron (every minute):
#   /bin/bash -c 'D=$HOME/delta-deploy; mkdir -p $D && curl -fsSL https://raw.githubusercontent.com/imran-me/deltainitiatives/main/deploy/hostinger-deploy.sh -o $D/.new && bash -n $D/.new && mv $D/.new $D/deploy.sh; bash $D/deploy.sh'
#
# Layout (docs/DEPLOYMENT.md):  domains/deltainitiatives.com/laravel  = git checkout of `main`
#                               domains/deltainitiatives.com/public_html -> laravel/public (symlink)
# Works only inside domains/deltainitiatives.com. Never deletes: the old public_html is renamed.
# Never touches .env, storage/ or vendor/ contents it did not create.
set -uo pipefail
REPO="https://github.com/imran-me/deltainitiatives.git"
BRANCH="main"
SITE="$HOME/domains/deltainitiatives.com"
APP="$SITE/laravel"
WEB="$SITE/public_html"
STATE="$HOME/delta-deploy"
LOG="$STATE/deploy.log"
mkdir -p "$STATE"
log() { echo "$(date '+%F %T') $*" >> "$LOG"; }

[ -d "$SITE" ] || { log "site folder missing: $SITE (add the website in hPanel)"; exit 0; }
exec 9>"$STATE/.lock"; flock -n 9 || exit 0

PHP=php
for c in /opt/alt/php83/usr/bin/php /opt/alt/php82/usr/bin/php /usr/bin/php8.3 /usr/bin/php8.2; do [ -x "$c" ] && { PHP="$c"; break; }; done
COMPOSER="$(command -v composer || true)"
if [ -z "$COMPOSER" ]; then
  [ -f "$STATE/composer.phar" ] || curl -fsSL https://getcomposer.org/download/latest-stable/composer.phar -o "$STATE/composer.phar"
  COMPOSER="$PHP $STATE/composer.phar"
else
  COMPOSER="$PHP $COMPOSER"
fi

# 1. checkout
if [ ! -d "$APP/.git" ]; then
  git clone -q --branch "$BRANCH" "$REPO" "$APP" || { log "clone failed"; exit 1; }
  log "cloned"
fi
cd "$APP" || exit 1

# 2. first-time .env (DB password is added by a person, never by this script)
if [ ! -f .env ]; then
  cp .env.example .env
  sed -i -e 's/^APP_ENV=.*/APP_ENV=production/' -e 's/^APP_DEBUG=.*/APP_DEBUG=false/' \
         -e 's#^APP_URL=.*#APP_URL=https://deltainitiatives.com#' -e 's/^DB_HOST=.*/DB_HOST=localhost/' .env
  chmod 600 .env
  $COMPOSER install -q --no-dev --optimize-autoloader --no-interaction >> "$LOG" 2>&1
  $PHP artisan key:generate --force >> "$LOG" 2>&1
  log "created .env — fill DB_DATABASE / DB_USERNAME / DB_PASSWORD in $APP/.env"
fi

# 3. web root -> laravel/public (rename, never delete)
if [ -d "$WEB" ] && [ ! -L "$WEB" ]; then
  mv "$WEB" "$WEB.hostinger-default.$(date +%Y%m%d%H%M%S)" && ln -s "$APP/public" "$WEB" && log "public_html -> laravel/public"
elif [ ! -e "$WEB" ]; then
  ln -s "$APP/public" "$WEB"
fi

# 4. wait for database credentials
if ! grep -qE '^DB_PASSWORD=.+' .env; then
  [ -f "$STATE/.waiting" ] || { log "waiting for DB credentials in .env"; touch "$STATE/.waiting"; }
  exit 0
fi
rm -f "$STATE/.waiting"

# 5. deploy when GitHub moved (or first run with a database)
remote=$(git ls-remote "$REPO" "refs/heads/$BRANCH" | cut -f1)
local_=$(git rev-parse HEAD)
if [ -n "$remote" ] && { [ "$remote" != "$local_" ] || [ ! -f "$STATE/.migrated" ]; }; then
  git fetch -q origin "$BRANCH" && git reset -q --hard "origin/$BRANCH"
  $COMPOSER install -q --no-dev --optimize-autoloader --no-interaction >> "$LOG" 2>&1 || { log "composer failed"; exit 1; }
  if [ ! -f "$STATE/.migrated" ]; then
    $PHP artisan migrate --force --seed > "$STATE/first-run.txt" 2>&1 && chmod 600 "$STATE/first-run.txt" && touch "$STATE/.migrated" \
      && log "first migrate+seed done — Super Admin one-time password is in $STATE/first-run.txt" || { log "migrate failed, see first-run.txt"; exit 1; }
  else
    $PHP artisan migrate --force >> "$LOG" 2>&1 || { log "migrate failed"; exit 1; }
  fi
  [ -e public/storage ] || $PHP artisan storage:link >> "$LOG" 2>&1
  $PHP artisan optimize >> "$LOG" 2>&1
  log "deployed $(git rev-parse --short HEAD)"
fi

# 6. Laravel scheduler (queue mails, retries, nightly backup)
[ -f "$STATE/.migrated" ] && $PHP artisan schedule:run >> /dev/null 2>&1
exit 0
