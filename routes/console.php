<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
| On Hostinger add ONE cron job (hPanel → Advanced → Cron Jobs), every minute:
|   cd /home/USER/domains/deltainitiatives.com/laravel && php artisan schedule:run >> /dev/null 2>&1
*/

// Nightly backup of the database + uploads (pruned to the newest BACKUP_KEEP).
Schedule::command('delta:backup')->dailyAt('02:30')->timezone('Asia/Dhaka')->withoutOverlapping()->onOneServer();

// Process queued e-mails (receipts are normally sent instantly; this is the safety net).
Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=50')->everyMinute()->withoutOverlapping();

// Retry any idea receipts / team alerts whose delivery failed.
Schedule::command('ideas:retry-notifications')->everyFiveMinutes()->withoutOverlapping();

// Housekeeping.
Schedule::command('queue:prune-failed --hours=720')->weekly();
Schedule::command('auth:clear-resets')->daily();
