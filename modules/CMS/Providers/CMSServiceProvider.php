<?php

namespace Modules\CMS\Providers;

use Modules\Admin\Services\DemoContent;
use Modules\CMS\Admin\ArmResource;
use Modules\CMS\Admin\ContentBlockResource;
use Modules\CMS\Admin\CoreValueResource;
use Modules\CMS\Admin\FocusAreaResource;
use Modules\CMS\Admin\LegalPageResource;
use Modules\CMS\Admin\PartnerResource;
use Modules\CMS\Admin\PipelineStepResource;
use Modules\CMS\Admin\StatResource;
use Modules\CMS\Admin\TeamMemberResource;
use Modules\CMS\Models\LegalPage;
use Modules\CMS\Models\Partner;
use Modules\CMS\Models\TeamMember;
use Modules\CMS\Support\StatSources;
use Modules\Core\Support\ModuleServiceProvider;
use Modules\Core\Support\Sitemap;

class CMSServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'CMS';

    protected string $alias = 'cms';

    protected array $resources = [
        ContentBlockResource::class,
        ArmResource::class,
        PipelineStepResource::class,
        FocusAreaResource::class,
        CoreValueResource::class,
        TeamMemberResource::class,
        PartnerResource::class,
        StatResource::class,
        LegalPageResource::class,
    ];

    public function boot(): void
    {
        parent::boot();

        DemoContent::register(TeamMember::class);
        DemoContent::register(Partner::class);

        StatSources::register('partners', 'Partners (live count of confirmed partners)', fn () => Partner::query()->displayable()->real()->count());

        $this->app->make(Sitemap::class)->add(function () {
            $entries = [
                ['loc' => route('home'), 'changefreq' => 'weekly', 'priority' => 1.0],
                ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => 0.8],
                ['loc' => route('what-we-do'), 'changefreq' => 'monthly', 'priority' => 0.8],
            ];

            foreach (LegalPage::query()->published()->get(['slug', 'updated_at']) as $page) {
                $entries[] = ['loc' => route('legal.show', $page->slug), 'lastmod' => $page->updated_at?->toAtomString(), 'changefreq' => 'yearly', 'priority' => 0.3];
            }

            return $entries;
        });
    }
}
