<?php

namespace Modules\Programmes\Providers;

use Modules\Admin\Services\DemoContent;
use Modules\Admin\Support\AdminDashboard;
use Modules\CMS\Support\StatSources;
use Modules\Core\Support\ModuleServiceProvider;
use Modules\Core\Support\Sitemap;
use Modules\Programmes\Admin\EventPhotoResource;
use Modules\Programmes\Admin\EventRegistrationResource;
use Modules\Programmes\Admin\EventResource;
use Modules\Programmes\Admin\ProgrammeResource;
use Modules\Programmes\Models\Event;
use Modules\Programmes\Models\EventRegistration;

class ProgrammesServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'Programmes';

    protected string $alias = 'programmes';

    protected array $resources = [
        ProgrammeResource::class,
        EventResource::class,
        EventRegistrationResource::class,
        EventPhotoResource::class,
    ];

    public function boot(): void
    {
        parent::boot();

        DemoContent::register(Event::class);
        DemoContent::register(EventRegistration::class);

        StatSources::register('events', 'Events held (live count of past events)',
            fn () => Event::query()->published()->real()->past()->count());

        AdminDashboard::stat([
            'label' => 'Upcoming events',
            'value' => fn () => Event::query()->published()->upcoming()->count(),
            'hint' => fn () => ($next = Event::query()->published()->upcoming()->orderBy('starts_at')->first())
                ? 'Next: '.$next->localStart()->format('j M')
                : 'None scheduled',
            'icon' => 'calendar-days',
            'route' => 'admin.resources.index',
            'route_params' => ['resource' => 'events'],
            'permission' => 'programmes.manage',
            'order' => 40,
        ]);

        AdminDashboard::stat([
            'label' => 'Event registrations',
            'value' => fn () => number_format(EventRegistration::query()->where('created_at', '>=', now()->subDays(30))->count()),
            'hint' => 'Last 30 days',
            'icon' => 'clipboard-list',
            'route' => 'admin.resources.index',
            'route_params' => ['resource' => 'event-registrations'],
            'permission' => 'programmes.manage',
            'order' => 45,
        ]);

        $this->app->make(Sitemap::class)->add(function () {
            $entries = [['loc' => route('programmes.index'), 'changefreq' => 'weekly', 'priority' => 0.8]];

            foreach (Event::query()->published()->orderByDesc('starts_at')->get(['id', 'slug', 'updated_at']) as $event) {
                $entries[] = [
                    'loc' => route('events.show', $event),
                    'lastmod' => $event->updated_at?->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => 0.6,
                ];
            }

            return $entries;
        });
    }
}
