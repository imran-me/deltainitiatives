# Delta Initiatives — website, Idea Portal & admin

**A Research, Innovation & Policy Institute · _Engineering Change._**

The full website for **deltainitiatives.com**, built from *Document 10 — Website Build Plan*:
public pages, the **Idea Portal** (IDs, timestamps, SHA-256 fingerprints and e-mailed receipts),
and a role-based **admin & review panel** — fully custom, no WordPress.

| | |
|---|---|
| Back end | Laravel 12 (PHP 8.2+), MySQL / MariaDB, modular architecture |
| Front end | Blade, Tailwind CSS 4, vanilla JS (no inline scripts — strict CSP) |
| Security | CSRF, validation, rate limiting, Cloudflare Turnstile, honeypot + time-trap, private file storage, CSP & security headers, RBAC, audit log |
| E-mail | SMTP (Google Workspace) |
| Hosting | Hostinger Cloud Startup behind Cloudflare DNS — see [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) |

---

## Quick start (local)

Requirements: PHP 8.2+ with `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `zip`, `gd`; Composer; Node 20+; MySQL/MariaDB.

```bash
cp .env.example .env              # set DB_* (database must exist)
composer install
php artisan key:generate
php artisan migrate --seed        # prints the Super Admin's one-time password
php artisan storage:link
npm install && npm run build      # or: npm run dev
php artisan serve                 # http://127.0.0.1:8000  ·  admin: /admin
```

`composer dev` runs the web server, queue worker and Vite together.

**Local demo accounts** (created only when `APP_ENV=local`): `admin@delta.test`, `reviewer@delta.test`,
`editor@delta.test`, `community@delta.test` — password `Delta-Demo-2026` (override with `DEMO_PASSWORD`).

Tests: `php artisan test` (SQLite in memory; Turnstile disabled in `phpunit.xml`).

---

## Architecture

Every feature lives in a self-contained module under `modules/` (namespace `Modules\…`), loaded by
`Modules\Core\Support\ModuleServiceProvider`. A module may contain:

```
modules/<Module>/
  Providers/<Module>ServiceProvider.php   registers resources, nav, dashboard widgets, sitemap…
  Models/ Services/ Mail/ Admin/ Seeders/ Http/{Controllers,Requests}
  config/config.php        → config('<alias>.…')
  database/migrations/     → php artisan migrate
  resources/views/         → view('<alias>::…'),  components → <x-<alias>::…>
  lang/{en,bn}/            → __('<alias>::file.key')
  routes/web.php           → public routes
  routes/admin.php         → /admin/* (auth + "admin" middleware, names "admin.*")
```

| Module | Responsibility |
|---|---|
| **Core** | Design system (Blade components, layout, header/footer), settings, audit log, Turnstile, anti-spam, security headers/CSP, sitemap & robots, CSV/XLSX exporter, e-mail layout |
| **Admin** | Admin shell, dashboard, declarative **Resource/Field CRUD framework**, settings, audit-log viewer, backups, demo-content purge |
| **Users** | Admin accounts, roles & permissions (Gates), sign-in, password reset, profile, user management |
| **CMS** | Home, About, What We Do, legal pages; editable copy blocks, arms, pipeline, focus areas, values, team, partner logos, verified numbers |
| **IdeaPortal** | Submit an Idea (EN + বাংলা), Idea IDs, fingerprints, receipts, verification, review dashboard, rubric scoring, status workflow, exports |
| **Programmes** | Programmes & Events, event pages, registrations, past-event gallery |
| **Partnerships** | Partner with Us, partnership types, brochure download, inquiries |
| **Applications** | Join Us (EN + বাংলা): members, fellows, campus ambassadors, volunteers |
| **Contact** | Contact page and inbox |
| **Newsletter** | Double opt-in sign-up, confirmation, one-click unsubscribe |

### The admin Resource framework
Most admin screens are declared, not hand-built. A resource lists its fields and the generic
controller provides list / search / filters / create / edit / detail / delete / CSV & Excel export,
validation, uploads and audit logging:

```php
class TeamMemberResource extends Resource {
    public static string $model = TeamMember::class;
    public static string $slug = 'team';
    public static string $permission = 'content.manage';
    public function fields(): array {
        return [
            Field::text('name')->required()->onIndex(),
            Field::select('group')->options(TeamMember::GROUPS)->onIndex(),
            Field::image('photo_path')->directory('team'),
            Field::boolean('is_published')->default(true),
        ];
    }
}
```

### Roles & permissions
Defined in `modules/Users/config/config.php`. Every reviewer has a personal account — passwords are never shared.

| Role | Can |
|---|---|
| Super Admin | Everything, incl. users, settings and backups |
| Administrator | Ideas (view, score, status, export, alerts), content, programmes, inboxes, newsletter, audit log, settings |
| Reviewer | View and score ideas, move them through the workflow |
| Content Editor | Website content, programmes and events |
| Community & Partnerships | Messages, partner inquiries, Join Us applications, registrations, newsletter |

---

## The Idea Portal (Build Plan p.5)

1. **Submit** — 10-question Programme Plan form + up to 3 files, Contributor Agreement consent.
2. **Check** — Turnstile, honeypot, time-trap, rate limit, full validation.
3. **Store** — MySQL; files on the private disk (`storage/app/private/ideas/{ID}/`), never web-accessible.
4. **ID** — sequential per year: `DI-2026-0001` (row-locked, no duplicates under load).
5. **Fingerprint** — SHA-256 of a canonical record of exactly what was submitted (answers + file hashes + time).
6. **Receipt** — e-mailed immediately after the response is sent (ID, timestamp, fingerprint); failed sends are retried every 5 minutes.
7. **Notify** — alert to everyone with *Receive new idea alerts* (title and link only — never the idea itself).
8. **Manage** — dashboard: search, filters, six-criterion rubric scoring, Received → Screening → Shortlisted → In Delta Labs, CSV/Excel export, integrity check, audit trail.

Anyone can confirm a receipt at **/verify** (ID + fingerprint → match / no match; content is never shown).

> The 10 questions and the 6 rubric criteria live in `modules/IdeaPortal/config/config.php` and are
> placeholders to align with the Programme Plan document.

---

## Content & demo data

`php artisan db:seed` loads:

* **Draft institutional copy** (hero, story, vision, mission, arms, pipeline, 8 focus areas, values, legal drafts) — editable under *Admin → Content*. Please review it.
* **Sample records** (`is_demo = 1`) — team placeholders, events, inquiries, applications, messages, ideas. They show a small **“Sample”** badge on the site and are removed in one click: *Admin → Dashboard → Remove samples* (or `php artisan delta:purge-demo`).

No invented statistics: the home-page numbers appear only when a figure is counted live from real records or marked *verified*. Partner logos appear only once written permission is recorded.

---

## Useful commands

| Command | Purpose |
|---|---|
| `php artisan delta:create-admin you@deltainitiatives.com --role=super_admin` | Create an account with a one-time password |
| `php artisan delta:backup` | Database + uploads backup (also runs nightly) |
| `php artisan delta:purge-demo` | Remove all sample content |
| `php artisan ideas:retry-notifications` | Re-send failed idea receipts / alerts (runs every 5 min) |
| `php artisan schedule:run` | Run scheduled tasks (cron, every minute in production) |

See **[docs/DEPLOYMENT.md](docs/DEPLOYMENT.md)** for going live and **[docs/TEAM-CHECKLIST.md](docs/TEAM-CHECKLIST.md)** for what the team still needs to provide.
