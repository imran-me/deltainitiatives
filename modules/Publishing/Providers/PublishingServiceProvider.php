<?php

namespace Modules\Publishing\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Admin\Services\DemoContent;
use Modules\Core\Support\ModuleServiceProvider;
use Modules\Publishing\Models\Article;
use Modules\Publishing\Models\FeedPost;
use Modules\Publishing\Models\Person;
use Modules\Publishing\Models\PublicIdea;
use Modules\Core\Support\Sitemap;
use Modules\Publishing\Admin\ArticleResource;
use Modules\Publishing\Admin\ExpertApplicationResource;
use Modules\Publishing\Admin\PersonResource;
use Modules\Publishing\Admin\PublicIdeaResource;
use Modules\Publishing\Admin\WorkedWithResource;
use Modules\Publishing\Admin\FeedCommentResource;
use Modules\Publishing\Admin\FeedPostResource;
use Modules\Publishing\Admin\ServiceRequestResource;

class PublishingServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'Publishing';

    protected string $alias = 'publishing';

    protected array $resources = [
        ArticleResource::class,
        PublicIdeaResource::class,
        PersonResource::class,
        WorkedWithResource::class,
        FeedPostResource::class,
        FeedCommentResource::class,
        ServiceRequestResource::class,
        ExpertApplicationResource::class,
    ];

    public function boot(): void
    {
        parent::boot();

        DemoContent::register(FeedPost::class);
        DemoContent::register(Article::class);
        DemoContent::register(PublicIdea::class);
        DemoContent::register(Person::class);

        RateLimiter::for('feed-reactions', fn (Request $request) => [
            Limit::perMinute(30)->by('fr-min|'.$request->ip()),
            Limit::perHour(300)->by('fr-hour|'.$request->ip()),
        ]);

        $this->app->make(Sitemap::class)->add(function () {
            $entries = [
                ['loc' => route('feed.index'), 'changefreq' => 'daily', 'priority' => 0.6],
                ['loc' => route('insights.index'), 'changefreq' => 'weekly', 'priority' => 0.8],
                ['loc' => route('public-ideas.index'), 'changefreq' => 'weekly', 'priority' => 0.8],
                ['loc' => route('gallery'), 'changefreq' => 'monthly', 'priority' => 0.5],
                ['loc' => route('bn.feed.index'), 'changefreq' => 'daily', 'priority' => 0.4],
                ['loc' => route('people'), 'changefreq' => 'monthly', 'priority' => 0.6],
                ['loc' => route('partners'), 'changefreq' => 'monthly', 'priority' => 0.7],
            ];
            foreach (Article::query()->visible()->get(['slug', 'updated_at']) as $a) {
                $entries[] = ['loc' => route('insights.show', $a->slug), 'lastmod' => $a->updated_at?->toAtomString(), 'changefreq' => 'monthly', 'priority' => 0.7];
            }
            foreach (PublicIdea::query()->visible()->get(['slug', 'updated_at']) as $i) {
                $entries[] = ['loc' => route('public-ideas.show', $i->slug), 'lastmod' => $i->updated_at?->toAtomString(), 'changefreq' => 'monthly', 'priority' => 0.6];
            }
            foreach (Person::query()->published()->get(['slug']) as $p) {
                $entries[] = ['loc' => route('people.show', $p->slug), 'changefreq' => 'monthly', 'priority' => 0.4];
            }

            return $entries;
        });
    }
}
