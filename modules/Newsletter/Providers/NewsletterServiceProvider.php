<?php

namespace Modules\Newsletter\Providers;

use Modules\Admin\Support\AdminDashboard;
use Modules\Core\Support\ModuleServiceProvider;
use Modules\Newsletter\Admin\SubscriberResource;
use Modules\Newsletter\Models\Subscriber;

class NewsletterServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'Newsletter';

    protected string $alias = 'newsletter';

    protected array $resources = [SubscriberResource::class];

    public function boot(): void
    {
        parent::boot();

        AdminDashboard::stat([
            'label' => 'Newsletter subscribers',
            'value' => fn () => number_format(Subscriber::query()->subscribed()->count()),
            'hint' => fn () => Subscriber::query()->where('status', 'pending')->count().' awaiting confirmation',
            'icon' => 'newspaper',
            'route' => 'admin.resources.index',
            'route_params' => ['resource' => 'subscribers'],
            'permission' => 'newsletter.manage',
            'order' => 60,
        ]);
    }
}
