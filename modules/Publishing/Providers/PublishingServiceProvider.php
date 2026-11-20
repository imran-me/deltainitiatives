<?php

namespace Modules\Publishing\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Admin\Services\DemoContent;
use Modules\Core\Support\ModuleServiceProvider;
use Modules\Publishing\Models\FeedPost;
use Modules\Core\Support\Sitemap;
use Modules\Publishing\Admin\ExpertApplicationResource;
use Modules\Publishing\Admin\FeedCommentResource;
use Modules\Publishing\Admin\FeedPostResource;
use Modules\Publishing\Admin\ServiceRequestResource;

class PublishingServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'Publishing';

    protected string $alias = 'publishing';

    protected array $resources = [
        FeedPostResource::class,
        FeedCommentResource::class,
        ServiceRequestResource::class,
        ExpertApplicationResource::class,
    ];

    public function boot(): void
    {
        parent::boot();

        DemoContent::register(FeedPost::class);

        RateLimiter::for('feed-reactions', fn (Request $request) => [
            Limit::perMinute(30)->by('fr-min|'.$request->ip()),
            Limit::perHour(300)->by('fr-hour|'.$request->ip()),
        ]);

        $this->app->make(Sitemap::class)->add(fn () => [
            ['loc' => route('feed.index'), 'changefreq' => 'daily', 'priority' => 0.6],
        ]);
    }
}
