<?php

namespace Modules\Publishing\Admin;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Publishing\Models\Person;
use Modules\Publishing\Models\PublicIdea;

class PersonResource extends Resource
{
    public static string $model = Person::class;
    public static string $slug = 'people';
    public static string $label = 'People';
    public static string $singular = 'Person';
    public static ?string $description = 'Team, contributors and board on the People page. Profiles use initials; points and Initiator titles come from their published ideas.';
    public static string $icon = 'users';
    public static string $group = 'Content';
    public static string $permission = 'content.manage';
    public static int $navOrder = 62;

    public function fields(): array
    {
        return [
            Field::text('name')->required()->onIndex(),
            Field::text('slug')->required()->unique()->help('Web address: /people/{slug}')->rules(['regex:/^[a-z0-9-]+$/']),
            Field::select('kind')->options(Person::KINDS)->required()->onIndex(),
            Field::text('role')->required()->onIndex(),
            Field::select('area')->options(PublicIdea::AREAS),
            Field::textarea('bio')->rows(3),
            Field::number('sort_order')->label('Order')->default(0),
            Field::boolean('is_published')->label('Published')->default(true)->onIndex(),
        ];
    }

    public function searchable(): array
    {
        return ['name', 'role'];
    }

    public function filters(): array
    {
        return ['kind' => ['label' => 'Kind', 'options' => Person::KINDS]];
    }

    public function title(Model $record): string
    {
        return (string) $record->name;
    }
}
