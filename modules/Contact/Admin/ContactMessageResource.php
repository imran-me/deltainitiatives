<?php

namespace Modules\Contact\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Contact\Models\ContactMessage;

/**
 * Admin inbox for the contact form. Opening a new message marks it as read.
 */
class ContactMessageResource extends Resource
{
    public static string $model = ContactMessage::class;
    public static string $slug = 'messages';
    public static string $label = 'Messages';
    public static string $singular = 'Message';
    public static ?string $description = 'Messages from the contact form. Opening a message marks it as read and is recorded in the audit log.';
    public static string $icon = 'inbox';
    public static string $group = 'Inbox';
    public static string $permission = 'inbox.manage';
    public static int $navOrder = 10;
    public static bool $creatable = false;
    public static bool $hasDetail = true;
    public static bool $auditViews = true;
    public static bool $exportable = true;

    protected const TONES = [
        'new' => 'bg-river-50 text-river-800 ring-river-200',
        'read' => 'bg-navy-50 text-navy-600 ring-navy-100',
        'replied' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
        'archived' => 'bg-white text-navy-400 ring-navy-100',
    ];

    public function fields(): array
    {
        return [
            Field::select('status')->options(ContactMessage::STATUSES)->required(),
            Field::textarea('admin_notes')->label('Internal notes')->rows(5)->maxLength(5000)->help('Visible to the team only.'),
        ];
    }

    public function indexFields(): array
    {
        return [
            Field::text('name')->label('From'),
            Field::text('subject'),
            Field::select('topic')->options(ContactMessage::topicOptions()),
            Field::select('status')->displayUsing(fn (ContactMessage $m) => static::statusBadge($m->status)),
            Field::datetime('created_at')->label('Received'),
        ];
    }

    public function detailFields(): array
    {
        return [
            Field::text('reference')->displayUsing(fn (ContactMessage $m) => new HtmlString('<span class="font-mono text-xs">'.e($m->reference()).'</span>')),
            Field::select('status')->displayUsing(fn (ContactMessage $m) => static::statusBadge($m->status)),
            Field::select('topic')->options(ContactMessage::topicOptions()),
            Field::text('name')->label('From'),
            Field::email('email')->label('E-mail'),
            Field::text('phone'),
            Field::text('organization')->label('Organisation'),
            Field::text('subject'),
            Field::textarea('message'),
            Field::text('ip_address')->label('IP address'),
        ];
    }

    public function searchable(): array
    {
        return ['name', 'email', 'subject', 'organization', 'message'];
    }

    public function filters(): array
    {
        return [
            'topic' => ['label' => 'Topic', 'options' => ContactMessage::topicOptions()],
            'status' => ['label' => 'Status', 'options' => ContactMessage::STATUSES],
        ];
    }

    public function applyDefaultSort(Builder $query): Builder
    {
        return $query->latest('id');
    }

    public function title(Model $record): string
    {
        /** @var ContactMessage $record */
        return $record->subject;
    }

    public function subtitle(Model $record): ?string
    {
        /** @var ContactMessage $record */
        return $record->reference().' · '.$record->topicLabel().' · from '.$record->name.' · '.dhaka_time($record->created_at);
    }

    public function badgeCount(): ?int
    {
        return ContactMessage::query()->unread()->count() ?: null;
    }

    public function onShow(Model $record): void
    {
        if ($record->status === 'new') {
            $record->forceFill(['status' => 'read'])->save();
        }
    }

    public function exportColumns(): array
    {
        return [
            'Reference' => fn (ContactMessage $m) => $m->reference(),
            'Received (BST)' => fn (ContactMessage $m) => $m->created_at?->setTimezone('Asia/Dhaka')->format('Y-m-d H:i'),
            'Topic' => fn (ContactMessage $m) => $m->topicLabel(),
            'Name' => 'name',
            'E-mail' => 'email',
            'Phone' => 'phone',
            'Organisation' => 'organization',
            'Subject' => 'subject',
            'Message' => 'message',
            'Status' => fn (ContactMessage $m) => ContactMessage::STATUSES[$m->status] ?? $m->status,
            'Internal notes' => 'admin_notes',
            'Sample' => fn (ContactMessage $m) => $m->is_demo,
        ];
    }

    public function exportQuery(Request $request): Builder
    {
        return $this->query()
            ->when(in_array($request->input('topic'), ContactMessage::TOPICS, true), fn ($q) => $q->where('topic', $request->input('topic')))
            ->when(array_key_exists((string) $request->input('status'), ContactMessage::STATUSES), fn ($q) => $q->where('status', $request->input('status')));
    }

    public static function statusBadge(?string $status): HtmlString
    {
        $tone = self::TONES[$status] ?? self::TONES['read'];

        return new HtmlString('<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset '.$tone.'">'.e(ContactMessage::STATUSES[$status] ?? $status).'</span>');
    }
}
