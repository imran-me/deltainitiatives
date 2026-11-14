# Deployment — Hostinger Cloud Startup + Cloudflare

This follows the Build Plan (p.7, *Connecting the domain*) and adds the Laravel-specific steps.
Placeholders in `UPPER_CASE` are values only the team has.

## 0. Before you start

- [ ] `deltainitiatives.com` registered in **Cloudflare** (Build Plan step 1).
- [ ] Google Workspace set up with `hello@`, `ideas@`, `partners@`.
- [ ] Code pushed to a private GitHub repository (the built `public/build` folder is committed, so the server needs no Node.js).

## 1. Create the site in Hostinger

1. hPanel → **Cloud Startup** → **Websites → Create site** → **PHP/HTML** → *Use an existing domain* → `deltainitiatives.com`.
2. hPanel → **Advanced → PHP Configuration**: PHP **8.2 or 8.3**; enable `zip`, `gd`, `intl`, `fileinfo`, `pdo_mysql`, `mbstring`, `openssl`.
3. hPanel → **Databases → MySQL Databases**: create database + user (note `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
4. hPanel → **Advanced → SSH Access**: enable and note the SSH command.

## 2. Put the code on the server

Recommended layout (application *outside* the web root):

```
/home/USER/domains/deltainitiatives.com/
├── laravel/            ← the repository (git clone here)
└── public_html  →  symlink to laravel/public
```

```bash
ssh USER@SERVER -p PORT
cd ~/domains/deltainitiatives.com
git clone git@github.com:ORG/delta-initiatives.git laravel     # or hPanel → Git → deploy to /laravel
cd laravel
composer install --no-dev --optimize-autoloader
cp .env.example .env && nano .env        # see section 3
php artisan key:generate
php artisan migrate --force --seed       # prints the Super Admin's one-time password — save it
php artisan storage:link

# point the web root at /public
cd .. && mv public_html public_html.bak && ln -s laravel/public public_html
```

> If symlinks are not possible, upload the whole project into `public_html`: the root `.htaccess`
> forwards requests to `/public` and blocks `.env`, `storage`, `vendor`, etc. The symlink layout is safer.

Then cache everything:

```bash
cd ~/domains/deltainitiatives.com/laravel
php artisan optimize          # config, routes, views, events
```

## 3. Production `.env`

```dotenv
APP_NAME="Delta Initiatives"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://deltainitiatives.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=DB_DATABASE
DB_USERNAME=DB_USERNAME
DB_PASSWORD=DB_PASSWORD

SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true
QUEUE_CONNECTION=database
CACHE_STORE=database
LOG_STACK=daily
LOG_LEVEL=warning

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=ideas@deltainitiatives.com
MAIL_PASSWORD=GOOGLE_APP_PASSWORD
MAIL_FROM_ADDRESS=ideas@deltainitiatives.com
MAIL_FROM_NAME="Delta Initiatives"

TURNSTILE_ENABLED=true
TURNSTILE_SITE_KEY=CLOUDFLARE_TURNSTILE_SITE_KEY
TURNSTILE_SECRET_KEY=CLOUDFLARE_TURNSTILE_SECRET_KEY

ADMIN_NAME="FULL NAME"
ADMIN_EMAIL=FIRST_ADMIN@deltainitiatives.com
ADMIN_PASSWORD=                       # leave empty → a one-time password is printed
SHOW_DEMO_BADGES=true
```

**Google Workspace SMTP:** turn on 2-Step Verification for the sending mailbox, then create an
*App password* (Google Account → Security → App passwords) and use it as `MAIL_PASSWORD`.
Alternative: Workspace SMTP relay (`smtp-relay.gmail.com`, allow the server IP in Admin console).

## 4. Cron (scheduler + queue)

hPanel → **Advanced → Cron Jobs** → *Custom*, every minute:

```
cd /home/USER/domains/deltainitiatives.com/laravel && php artisan schedule:run >> /dev/null 2>&1
```

This runs: queued e-mails every minute, idea-receipt retries every 5 minutes, the nightly backup
(02:30 BST) and housekeeping. Idea receipts are sent immediately after submission, so they arrive
within a minute even without the queue.

## 5. Cloudflare DNS & SSL (Build Plan steps 3–5)

1. Cloudflare → DNS: two **A records**, `@` and `www` → the IP shown in hPanel. Proxy: **on** (orange cloud).
2. After Hostinger has issued SSL (hPanel → Security → SSL): Cloudflare → **SSL/TLS → Full (strict)**.
3. SSL/TLS → Edge Certificates: **Always Use HTTPS** on, minimum TLS 1.2, HSTS (after confirming HTTPS works).
4. Rules → Redirect: `www.deltainitiatives.com/*` → `https://deltainitiatives.com/$1` (301).
5. Caching → Cache Rules: **bypass cache** for `/admin*`, `/newsletter/*` and any request with a cookie `delta-initiatives-session`; cache `/build/*` aggressively.
6. **Google Workspace e-mail records:** MX (from the Workspace setup wizard), SPF `TXT @ "v=spf1 include:_spf.google.com ~all"`,
   DKIM (Admin console → Apps → Gmail → Authenticate email → generate, then add the `google._domainkey` TXT), and
   DMARC `TXT _dmarc "v=DMARC1; p=quarantine; rua=mailto:hello@deltainitiatives.com"`.

## 6. Cloudflare Turnstile & Web Analytics

- Cloudflare → **Turnstile** → *Add widget* → domain `deltainitiatives.com`, mode *Managed* → copy site and secret keys into `.env`, then `php artisan optimize`.
- Cloudflare → **Web Analytics** → add the site → copy the token into *Admin → Settings → Analytics & SEO* (cookie-free; no banner needed).

## 7. Go-live checklist (Build Plan p.6, *Definition of done*)

- [ ] Sign in at `/admin` with the one-time password and choose a new one; create personal accounts for every reviewer (*Users & roles*).
- [ ] Replace sample content, then *Dashboard → Remove samples*.
- [ ] Review and publish the **Privacy Policy, Terms and Contributor Agreement** (*Content → Legal pages*).
- [ ] *Settings*: contact details, social links, brochure PDF, Search Console code.
- [ ] Submit a test idea from a phone: receipt arrives within one minute; it appears in *Idea Portal → Submissions*; scoring works.
- [ ] Bangla renders correctly on `/bn/submit-an-idea` and `/bn/join-us` on Android and iOS.
- [ ] Google PageSpeed (mobile) 90+; no broken links (`/sitemap.xml` lists every page).
- [ ] Google Search Console: verify the domain, submit `https://deltainitiatives.com/sitemap.xml`.
- [ ] Download a backup from *System → Backups* and store it off the server.

## 8. Updating the site

```bash
cd ~/domains/deltainitiatives.com/laravel
php artisan down --render="errors::503"
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan up
```

Front-end changes: run `npm run build` locally and commit `public/build` before pushing.

## 9. Moving away from Hostinger later

Because the domain is on Cloudflare, moving to Google Cloud or any PHP host is: deploy the same
repository, restore a backup (`database.sql` + `private/` + `public/` from the ZIP), and change one
DNS record. No rebuild needed.
