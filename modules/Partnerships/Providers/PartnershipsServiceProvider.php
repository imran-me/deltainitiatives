<?php

namespace Modules\Partnerships\Providers;

use Modules\Admin\Services\DemoContent;
use Modules\Admin\Support\AdminDashboard;
use Modules\Core\Support\ModuleServiceProvider;
use Modules\Core\Support\Sitemap;
use Modules\Partnerships\Admin\PartnerInquiryResource;
use Modules\Partnerships\Admin\PartnershipTypeResource;
use Modules\Partnerships\Models\PartnerInquiry;

class PartnershipsServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'Partnerships';

    protected string $alias = 'partnerships';

    protected array $resources = [
        PartnershipTypeResource::class,
        PartnerInquiryResource::class,
    ];

    public function boot(): void
    {
        parent::boot();

        DemoContent::register(PartnerInquiry::class);

        AdminDashboard::stat([
            'label' => 'New partner inquiries',
            'value' => fn () => PartnerInquiry::query()->where('status', 'new')->count(),
            'hint' => fn () => PartnerInquiry::query()->where('status', 'in_conversation')->count().' in conversation',
            'icon' => 'handshake',
            'route' => 'admin.resources.index',
            'route_params' => ['resource' => 'partner-inquiries', 'status' => 'new'],
            'permission' => 'inbox.manage',
            'order' => 30,
        ]);

        $this->app->make(Sitemap::class)->add(fn () => [
        ]);
    }
}
