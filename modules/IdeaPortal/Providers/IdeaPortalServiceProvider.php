<?php

namespace Modules\IdeaPortal\Providers;

use Illuminate\Support\Facades\Gate;
use Modules\Admin\Services\DemoContent;
use Modules\Admin\Support\AdminDashboard;
use Modules\Admin\Support\AdminNav;
use Modules\CMS\Support\StatSources;
use Modules\Core\Support\ModuleServiceProvider;
use Modules\Core\Support\Sitemap;
use Modules\IdeaPortal\Console\RetryNotificationsCommand;
use Modules\IdeaPortal\Models\IdeaSubmission;
use Modules\IdeaPortal\Policies\IdeaSubmissionPolicy;
use Modules\IdeaPortal\Services\FingerprintService;
use Modules\IdeaPortal\Services\IdeaIdGenerator;
use Modules\IdeaPortal\Services\IdeaNotifier;

class IdeaPortalServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'IdeaPortal';

    protected string $alias = 'ideaportal';

    public function register(): void
    {
        parent::register();

        $this->app->singleton(IdeaIdGenerator::class);
        $this->app->singleton(FingerprintService::class);
        $this->app->singleton(IdeaNotifier::class);

        if ($this->app->runningInConsole()) {
            $this->commands([RetryNotificationsCommand::class]);
        }
    }

    public function boot(): void
    {
        parent::boot();

        Gate::policy(IdeaSubmission::class, IdeaSubmissionPolicy::class);

        DemoContent::register(IdeaSubmission::class);

        StatSources::register('ideas', 'Ideas received (live count)', fn () => IdeaSubmission::query()->real()->count());

        $this->registerAdmin();

        $this->app->make(Sitemap::class)->add(fn () => [
            ['loc' => route('ideas.create'), 'changefreq' => 'monthly', 'priority' => 0.9],
            ['loc' => route('bn.ideas.create'), 'changefreq' => 'monthly', 'priority' => 0.7],
            ['loc' => route('ideas.verify'), 'changefreq' => 'yearly', 'priority' => 0.3],
        ]);
    }

    protected function registerAdmin(): void
    {
        AdminNav::add([
            'group' => 'Idea Portal',
            'label' => 'Submissions',
            'route' => 'admin.ideas.index',
            'active' => 'admin.ideas.*',
            'icon' => 'lightbulb',
            'permission' => 'ideas.view',
            'order' => 10,
            'badge' => fn () => IdeaSubmission::query()->where('status', 'received')->count(),
        ]);

        AdminDashboard::stat([
            'label' => 'New ideas',
            'value' => fn () => number_format(IdeaSubmission::query()->where('status', 'received')->count()),
            'hint' => fn () => IdeaSubmission::query()->where('submitted_at', '>=', now()->subDays(7))->count().' submitted in the last 7 days',
            'icon' => 'lightbulb',
            'route' => 'admin.ideas.index',
            'route_params' => ['status' => 'received'],
            'permission' => 'ideas.view',
            'order' => 10,
        ]);

        AdminDashboard::stat([
            'label' => 'Awaiting your score',
            'value' => fn () => number_format(IdeaSubmission::query()
                ->whereIn('status', ['received', 'screening', 'shortlisted'])
                ->whereDoesntHave('scores', fn ($q) => $q->where('user_id', auth()->id()))
                ->count()),
            'hint' => 'Received, screening or shortlisted',
            'icon' => 'star',
            'route' => 'admin.ideas.index',
            'route_params' => ['scored' => 'mine'],
            'permission' => 'ideas.score',
            'order' => 15,
        ]);

        AdminDashboard::stat([
            'label' => 'In screening',
            'value' => fn () => number_format(IdeaSubmission::query()->where('status', 'screening')->count()),
            'hint' => fn () => IdeaSubmission::query()->where('status', 'shortlisted')->count().' shortlisted',
            'icon' => 'clipboard-list',
            'route' => 'admin.ideas.index',
            'route_params' => ['status' => 'screening'],
            'permission' => 'ideas.view',
            'order' => 20,
        ]);

        AdminDashboard::stat([
            'label' => 'In Delta Labs',
            'value' => fn () => number_format(IdeaSubmission::query()->where('status', 'in_delta_labs')->count()),
            'hint' => fn () => number_format(IdeaSubmission::query()->count()).' ideas in total',
            'icon' => 'flask-conical',
            'route' => 'admin.ideas.index',
            'route_params' => ['status' => 'in_delta_labs'],
            'permission' => 'ideas.view',
            'order' => 30,
        ]);

        AdminDashboard::panel([
            'view' => 'ideaportal::admin.dashboard-panel',
            'data' => fn () => [
                'latestIdeas' => IdeaSubmission::query()
                    ->orderByDesc('submitted_at')
                    ->limit(6)
                    ->get(['id', 'code', 'title', 'status', 'lead_name', 'institution', 'submitted_at', 'average_score', 'scores_count', 'is_demo']),
            ],
            'permission' => 'ideas.view',
            'order' => 10,
            'span' => 2,
        ]);
    }
}
