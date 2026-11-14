<?php

namespace Modules\Programmes\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Programmes\Models\Event;
use Modules\Programmes\Models\Programme;

class EventResource extends Resource
{
    public static string $model = Event::class;
    public static string $slug = 'events';
    public static string $label = 'Events';
    public static string $singular = 'Event';
    public static ?string $description = 'Events appear on Home and Programmes & Events. Times are entered in Bangladesh time (BST).';
    public static string $icon = 'calendar-days';
    public static string $group = 'Programmes';
    public static string $permission = 'programmes.manage';
    public static int $navOrder = 20;

    /** @var array<int, string>|null */
    protected ?array $programmes = null;

    protected function programmeOptions(): array
    {
        return $this->programmes ??= Programme::options();
    }

    public function fields(): array
    {
        return [
            Field::text('title')->required()->onIndex()->section('Event'),
            Field::text('slug')->required()->unique()->half()->rules(['alpha_dash'])->help('Web address: /events/your-slug')->section('Event'),
            Field::select('programme_id')->label('Programme')->options($this->programmeOptions())->half()->onIndex()->section('Event'),
            Field::textarea('summary')->rows(2)->maxLength(400)->help('One or two sentences for cards and search results.')->section('Event'),
            Field::markdown('body')->label('Full description')->section('Event'),

            Field::datetime('starts_at')->label('Starts')->required()->half()->onIndex()->section('Date & place'),
            Field::datetime('ends_at')->label('Ends')->half()->rules(['after_or_equal:starts_at'])->section('Date & place'),
            Field::select('mode')->options(Event::MODES)->required()->default('in_person')->half()->onIndex()->section('Date & place'),
            Field::text('city')->default('Dhaka')->half()->section('Date & place'),
            Field::text('venue')->help('Leave empty to show “Venue to be confirmed”.')->section('Date & place'),
            Field::text('online_note')->label('Online joining note')->maxLength(500)->help('Shown in the confirmation e-mail for online/hybrid events, e.g. “The link is e-mailed the day before.”')->section('Date & place'),

            Field::boolean('registration_open')->label('Registration open')->default(true)->half()->section('Registration'),
            Field::number('capacity')->half()->rules(['integer', 'min:1'])->help('Leave empty for unlimited. When full, new sign-ups join a waitlist.')->section('Registration'),
            Field::datetime('registration_deadline')->label('Registration deadline')->half()->section('Registration'),
            Field::text('registered_count')->label('Registered')->readonly()->hideOnForm()->onIndex()
                ->displayUsing(fn (Event $e) => new HtmlString('<span class="tabular-nums">'.e($e->registeredCount()).($e->capacity ? '<span class="text-navy-400"> / '.e($e->capacity).'</span>' : '').'</span>')),

            Field::image('cover_path')->label('Cover photo')->directory('events')->help('A real photo from the venue or a past event, 16:9. Optional.')->section('Publishing'),
            Field::boolean('is_published')->label('Published')->default(true)->onIndex()->section('Publishing'),
        ];
    }

    public function query(): Builder
    {
        return Event::query()->withRegisteredCount();
    }

    public function applyDefaultSort(Builder $query): Builder
    {
        return $query->orderByDesc('starts_at');
    }

    public function searchable(): array
    {
        return ['title', 'venue', 'city'];
    }

    public function filters(): array
    {
        return [
            'programme_id' => ['label' => 'Programme', 'options' => $this->programmeOptions()],
            'mode' => ['label' => 'Mode', 'options' => Event::MODES],
        ];
    }

    public function subtitle(Model $record): ?string
    {
        return $record->dateLabel().' · '.$record->whereLabel();
    }

    public function afterSave(Model $record, Request $request, bool $created): void
    {
        // Editing a sample event's title or date turns it into real content.
        if (! $created && $record->is_demo && $record->wasChanged(['title', 'starts_at'])) {
            $record->forceFill(['is_demo' => false])->saveQuietly();
        }
    }
}
