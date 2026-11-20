<?php

namespace Modules\Publishing\Admin;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Publishing\Models\ServiceRequest;

class ServiceRequestResource extends Resource
{
    public static string $model = ServiceRequest::class;
    public static string $slug = 'service-requests';
    public static string $label = 'Service requests';
    public static string $singular = 'Service request';
    public static ?string $description = 'Proposal requests from the Services pages.';
    public static string $icon = 'briefcase';
    public static string $group = 'Community';
    public static string $permission = 'inbox.manage';
    public static int $navOrder = 46;
    public static bool $creatable = false;

    public function fields(): array
    {
        return [
            Field::select('service')->options(ServiceRequest::SERVICES)->readonly()->onIndex(),
            Field::text('organization')->readonly()->onIndex(),
            Field::text('name')->readonly()->onIndex(),
            Field::email('email')->readonly(),
            Field::text('phone')->readonly(),
            Field::text('budget')->readonly(),
            Field::textarea('message')->readonly(),
            Field::select('status')->options(ServiceRequest::STATUSES)->required()->onIndex(),
            Field::datetime('created_at')->label('Received')->readonly()->onIndex(),
        ];
    }

    public function searchable(): array
    {
        return ['organization', 'name', 'email', 'message'];
    }

    public function filters(): array
    {
        return ['status' => ['label' => 'Status', 'options' => ServiceRequest::STATUSES]];
    }

    public function badgeCount(): ?int
    {
        return ServiceRequest::query()->where('status', 'new')->count() ?: null;
    }

    public function title(Model $record): string
    {
        return (string) $record->organization;
    }
}
