<?php

namespace Modules\Admin\Console;

use Illuminate\Console\Command;
use Modules\Admin\Services\DemoContent;

class PurgeDemoCommand extends Command
{
    protected $signature = 'delta:purge-demo {--force : Skip the confirmation prompt}';

    protected $description = 'Delete all seeded sample (demo) content before launch';

    public function handle(DemoContent $demo): int
    {
        $count = $demo->count();

        if ($count === 0) {
            $this->components->info('No demo content found.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Delete {$count} demo records?")) {
            return self::FAILURE;
        }

        $this->components->info('Deleted '.$demo->purge().' demo records.');

        return self::SUCCESS;
    }
}
