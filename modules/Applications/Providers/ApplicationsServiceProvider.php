<?php

namespace Modules\Applications\Providers;

use Modules\Admin\Services\DemoContent;
use Modules\Admin\Support\AdminDashboard;
use Modules\Applications\Admin\JoinApplicationResource;
use Modules\Applications\Models\JoinApplication;
use Modules\Core\Support\ModuleServiceProvider;
use Modules\Core\Support\Sitemap;

class ApplicationsServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'Applications';

    protected string $alias = 'applications';

    protected array $resources = [JoinApplicationResource::class];

    public function boot(): void
    {
        parent::boot();

        DemoContent::register(JoinApplication::class);

        AdminDashboard::stat([
            'label' => 'New Join Us applications',
            'value' => fn () => number_format(JoinApplication::query()->where('status', 'new')->count()),
            'hint' => fn () => JoinApplication::query()->where('created_at', '>=', now()->subDays(7))->count().' received this week',
            'icon' => 'user-plus',
            'route' => 'admin.resources.index',
            'route_params' => ['resource' => 'join-applications', 'status' => 'new'],
            'permission' => 'inbox.manage',
            'order' => 40,
        ]);

        $this->app->make(Sitemap::class)->add(fn () => [
            ['loc' => route('join.create'), 'changefreq' => 'monthly', 'priority' => 0.7],
            ['loc' => route('bn.join.create'), 'changefreq' => 'monthly', 'priority' => 0.6],
        ]);
    }
}
