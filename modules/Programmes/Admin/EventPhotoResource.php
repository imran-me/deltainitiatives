<?php

namespace Modules\Programmes\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Programmes\Models\Event;
use Modules\Programmes\Models\EventPhoto;

class EventPhotoResource extends Resource
{
    public static string $model = EventPhoto::class;
    public static string $slug = 'event-photos';
    public static string $label = 'Event photos';
    public static string $singular = 'Photo';
    public static ?string $description = 'Real photos from past events for the gallery on Programmes & Events. Never use stock images.';
    public static string $icon = 'image';
    public static string $group = 'Programmes';
    public static string $permission = 'programmes.manage';
    public static int $navOrder = 40;

    /** @var array<int, string>|null */
    protected ?array $events = null;

    protected function eventOptions(): array
    {
        return $this->events ??= Event::options();
    }

    public function fields(): array
    {
        return [
            Field::image('path')->label('Photo')->required()->directory('events/gallery')->maxKb(6144)->onIndex()->help('Landscape works best (4:3).'),
            Field::select('event_id')->label('Event')->options($this->eventOptions())->required()->onIndex(),
            Field::text('caption')->maxLength(255)->onIndex()->help('Also used as the image description for screen readers.'),
            Field::number('sort_order')->default(0),
        ];
    }

    public function filters(): array
    {
        return ['event_id' => ['label' => 'Event', 'options' => $this->eventOptions()]];
    }

    public function applyDefaultSort(Builder $query): Builder
    {
        return $query->orderBy('event_id')->orderBy('sort_order')->orderBy('id');
    }

    public function title(Model $record): string
    {
        return $record->caption ?: 'Photo #'.$record->getKey();
    }
}
