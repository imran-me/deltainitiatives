<?php

namespace Modules\Admin\Console;

use Illuminate\Console\Command;
use Modules\Admin\Services\BackupService;

class BackupCommand extends Command
{
    protected $signature = 'delta:backup';

    protected $description = 'Create a database + uploads backup in storage/app/private/backups (scheduled daily)';

    public function handle(BackupService $backups): int
    {
        $name = $backups->create();
        $this->components->info("Backup created: {$name}");

        return self::SUCCESS;
    }
}
