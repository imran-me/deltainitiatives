<?php

namespace Modules\Programmes\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Programmes\Models\Event;
use Modules\Programmes\Models\EventRegistration;

class EventRegistrationResource extends Resource
{
    public static string $model = EventRegistration::class;
    public static string $slug = 'event-registrations';
    public static string $label = 'Registrations';
    public static string $singular = 'Registration';
    public static ?string $description = 'Sign-ups for events. Filter by event, then export a CSV or Excel list for check-in.';
    public static string $icon = 'clipboard-list';
    public static string $group = 'Programmes';
    public static string $permission = 'programmes.manage';
    public static int $navOrder = 30;
    public static bool $creatable = false;
    public static bool $hasDetail = true;
    public static bool $auditViews = true;
    public static bool $exportable = true;

    /** @var array<int, string>|null */
    protected ?array $events = null;

    protected function eventOptions(): array
    {
        return $this->events ??= Event::options();
    }

    public function fields(): array
    {
        return [
            Field::text('name')->hideOnForm()->onIndex(),
            Field::email('email')->hideOnForm()->onIndex(),
            Field::select('event_id')->label('Event')->options($this->eventOptions())->hideOnForm()->onIndex(),
            Field::text('phone')->hideOnForm(),
            Field::text('institution')->label('University / organisation')->hideOnForm(),
            Field::select('occupation')->options(EventRegistration::OCCUPATIONS)->hideOnForm(),
            Field::textarea('notes')->hideOnForm(),
            Field::select('status')->options(EventRegistration::STATUSES)->required()->onIndex(),
            Field::datetime('created_at')->label('Registered')->readonly()->hideOnForm()->hideOnDetail()->onIndex(),
        ];
    }

    public function searchable(): array
    {
        return ['name', 'email', 'institution'];
    }

    public function filters(): array
    {
        return [
            'event_id' => ['label' => 'Event', 'options' => $this->eventOptions()],
            'status' => ['label' => 'Status', 'options' => EventRegistration::STATUSES],
        ];
    }

    public function subtitle(Model $record): ?string
    {
        return $record->email.' · '.($this->eventOptions()[$record->event_id] ?? 'Event #'.$record->event_id);
    }

    public function exportColumns(): array
    {
        return [
            'Event' => fn (EventRegistration $r) => $r->event?->title,
            'Event date (BST)' => fn (EventRegistration $r) => $r->event?->localStart()->format('Y-m-d H:i'),
            'Name' => 'name',
            'E-mail' => 'email',
            'Phone' => 'phone',
            'University / organisation' => 'institution',
            'Occupation' => fn (EventRegistration $r) => EventRegistration::OCCUPATIONS[$r->occupation] ?? $r->occupation,
            'Status' => fn (EventRegistration $r) => EventRegistration::STATUSES[$r->status] ?? $r->status,
            'Notes' => 'notes',
            'Registered at (BST)' => fn (EventRegistration $r) => $r->created_at?->setTimezone('Asia/Dhaka')->format('Y-m-d H:i'),
            'Sample record' => fn (EventRegistration $r) => $r->is_demo ? 'Yes' : 'No',
        ];
    }

    public function exportQuery(Request $request): Builder
    {
        return $this->query()
            ->with('event')
            ->when($request->filled('event_id'), fn ($q) => $q->where('event_id', $request->integer('event_id')))
            ->when($request->filled('status') && array_key_exists($request->input('status'), EventRegistration::STATUSES),
                fn ($q) => $q->where('status', $request->input('status')));
    }
}
