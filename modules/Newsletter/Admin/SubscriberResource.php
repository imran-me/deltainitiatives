<?php

namespace Modules\Newsletter\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Newsletter\Models\Subscriber;

class SubscriberResource extends Resource
{
    public static string $model = Subscriber::class;
    public static string $slug = 'subscribers';
    public static string $label = 'Newsletter';
    public static string $singular = 'Subscriber';
    public static ?string $description = 'Double opt-in: people appear as “Subscribed” only after clicking the link in the confirmation e-mail.';
    public static string $icon = 'newspaper';
    public static string $group = 'Community';
    public static string $permission = 'newsletter.manage';
    public static int $navOrder = 40;
    public static bool $creatable = false;
    public static bool $exportable = true;

    public function fields(): array
    {
        return [
            Field::email('email')->readonly()->onIndex(),
            Field::select('status')->options(Subscriber::STATUSES)->required()->onIndex(),
            Field::text('source')->readonly()->onIndex(),
            Field::datetime('confirmed_at')->label('Confirmed')->readonly()->onIndex(),
            Field::datetime('unsubscribed_at')->label('Unsubscribed')->readonly(),
        ];
    }

    public function searchable(): array
    {
        return ['email', 'name'];
    }

    public function filters(): array
    {
        return ['status' => ['label' => 'Status', 'options' => Subscriber::STATUSES]];
    }

    public function exportColumns(): array
    {
        return [
            'E-mail' => 'email',
            'Status' => fn (Subscriber $s) => Subscriber::STATUSES[$s->status] ?? $s->status,
            'Source' => 'source',
            'Language' => 'locale',
            'Subscribed at' => fn (Subscriber $s) => $s->created_at?->setTimezone('Asia/Dhaka')->format('Y-m-d H:i'),
            'Confirmed at' => fn (Subscriber $s) => $s->confirmed_at?->setTimezone('Asia/Dhaka')->format('Y-m-d H:i'),
        ];
    }

    public function exportQuery(Request $request): Builder
    {
        return $this->query()->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')));
    }
}
