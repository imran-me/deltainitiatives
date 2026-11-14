<?php

namespace Modules\Partnerships\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Partnerships\Models\PartnerInquiry;
use Modules\Partnerships\Models\PartnershipType;

class PartnerInquiryResource extends Resource
{
    public static string $model = PartnerInquiry::class;
    public static string $slug = 'partner-inquiries';
    public static string $label = 'Partner inquiries';
    public static string $singular = 'Partner inquiry';
    public static ?string $description = 'Messages sent through Partner with Us. Each one has already been acknowledged by e-mail; aim to reply within three working days.';
    public static string $icon = 'handshake';
    public static string $group = 'Inbox';
    public static string $permission = 'inbox.manage';
    public static int $navOrder = 20;
    public static bool $creatable = false;
    public static bool $hasDetail = true;
    public static bool $auditViews = true;
    public static bool $exportable = true;

    /** @var array<int, string>|null */
    protected ?array $types = null;

    protected function typeOptions(): array
    {
        return $this->types ??= PartnershipType::options();
    }

    public function fields(): array
    {
        return [
            Field::text('organization')->label('Organisation')->hideOnForm()->onIndex(),
            Field::text('contact_name')->label('Contact')->hideOnForm()->onIndex(),
            Field::text('job_title')->hideOnForm(),
            Field::email('email')->hideOnForm(),
            Field::text('phone')->hideOnForm(),
            Field::url('website')->hideOnForm(),
            Field::text('country')->hideOnForm(),
            Field::select('partnership_type_id')->label('Interest')->options($this->typeOptions())->hideOnForm()->onIndex(),
            Field::textarea('message')->hideOnForm(),
            Field::select('status')->options(PartnerInquiry::STATUSES)->required()->onIndex(),
            Field::textarea('admin_notes')->label('Internal notes')->rows(4)->maxLength(5000)->help('Only visible to the team.'),
            Field::datetime('created_at')->label('Received')->readonly()->hideOnForm()->hideOnDetail()->onIndex(),
        ];
    }

    public function searchable(): array
    {
        return ['organization', 'contact_name', 'email'];
    }

    public function filters(): array
    {
        return [
            'status' => ['label' => 'Status', 'options' => PartnerInquiry::STATUSES],
            'partnership_type_id' => ['label' => 'Interest', 'options' => $this->typeOptions()],
        ];
    }

    public function badgeCount(): ?int
    {
        return PartnerInquiry::query()->where('status', 'new')->count() ?: null;
    }

    public function title(Model $record): string
    {
        return $record->organization;
    }

    public function subtitle(Model $record): ?string
    {
        return $record->contact_name.' · '.$record->email;
    }

    public function exportColumns(): array
    {
        return [
            'Organisation' => 'organization',
            'Contact' => 'contact_name',
            'Job title' => 'job_title',
            'E-mail' => 'email',
            'Phone' => 'phone',
            'Website' => 'website',
            'Country' => 'country',
            'Interest' => fn (PartnerInquiry $i) => $i->partnershipType?->name,
            'Message' => 'message',
            'Status' => fn (PartnerInquiry $i) => PartnerInquiry::STATUSES[$i->status] ?? $i->status,
            'Internal notes' => 'admin_notes',
            'Received (BST)' => fn (PartnerInquiry $i) => $i->created_at?->setTimezone('Asia/Dhaka')->format('Y-m-d H:i'),
            'Sample record' => fn (PartnerInquiry $i) => $i->is_demo ? 'Yes' : 'No',
        ];
    }

    public function exportQuery(Request $request): Builder
    {
        return $this->query()
            ->with('partnershipType')
            ->when($request->filled('status') && array_key_exists($request->input('status'), PartnerInquiry::STATUSES),
                fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('partnership_type_id'), fn ($q) => $q->where('partnership_type_id', $request->integer('partnership_type_id')));
    }
}
