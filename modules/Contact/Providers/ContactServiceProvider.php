<?php

namespace Modules\Contact\Providers;

use Modules\Admin\Services\DemoContent;
use Modules\Admin\Support\AdminDashboard;
use Modules\Contact\Admin\ContactMessageResource;
use Modules\Contact\Models\ContactMessage;
use Modules\Core\Support\ModuleServiceProvider;
use Modules\Core\Support\Sitemap;

class ContactServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'Contact';

    protected string $alias = 'contact';

    protected array $resources = [ContactMessageResource::class];

    public function boot(): void
    {
        parent::boot();

        DemoContent::register(ContactMessage::class);

        AdminDashboard::stat([
            'label' => 'Pending contact messages',
            'value' => fn () => number_format(ContactMessage::query()->unread()->count()),
            'hint' => fn () => ContactMessage::query()->where('created_at', '>=', now()->subDays(7))->count().' received this week',
            'icon' => 'inbox',
            'route' => 'admin.resources.index',
            'route_params' => ['resource' => 'messages', 'status' => 'new'],
            'permission' => 'inbox.manage',
            'order' => 30,
        ]);

        $this->app->make(Sitemap::class)->add(fn () => [
            ['loc' => route('contact.create'), 'changefreq' => 'yearly', 'priority' => 0.6],
        ]);
    }
}
