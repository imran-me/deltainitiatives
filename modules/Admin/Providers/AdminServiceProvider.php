<?php

namespace Modules\Admin\Providers;

use Modules\Admin\Console\BackupCommand;
use Modules\Admin\Console\CreateAdminCommand;
use Modules\Admin\Console\PurgeDemoCommand;
use Modules\Admin\Services\BackupService;
use Modules\Admin\Services\DemoContent;
use Modules\Admin\Support\AdminNav;
use Modules\Core\Support\ModuleServiceProvider;

class AdminServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'Admin';

    protected string $alias = 'admin';

    public function register(): void
    {
        parent::register();

        $this->app->singleton(BackupService::class);
        $this->app->singleton(DemoContent::class);

        if ($this->app->runningInConsole()) {
            $this->commands([BackupCommand::class, PurgeDemoCommand::class, CreateAdminCommand::class]);
        }
    }

    public function boot(): void
    {
        parent::boot();

        AdminNav::add(['group' => 'Overview', 'label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'layout-dashboard', 'permission' => 'dashboard.view', 'order' => 0]);
        AdminNav::add(['group' => 'System', 'label' => 'Settings', 'route' => 'admin.settings.edit', 'icon' => 'settings', 'permission' => 'settings.manage', 'order' => 20]);
        AdminNav::add(['group' => 'System', 'label' => 'Audit log', 'route' => 'admin.audit.index', 'icon' => 'history', 'permission' => 'audit.view', 'order' => 30]);
        AdminNav::add(['group' => 'System', 'label' => 'Backups', 'route' => 'admin.backups.index', 'icon' => 'database', 'permission' => 'backups.manage', 'order' => 40]);
    }
}
