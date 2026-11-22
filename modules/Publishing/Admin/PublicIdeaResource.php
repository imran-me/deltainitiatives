<?php

namespace Modules\Publishing\Admin;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Publishing\Models\Person;
use Modules\Publishing\Models\PublicIdea;
use Modules\Publishing\Support\Library;

class PublicIdeaResource extends Resource
{
    public static string $model = PublicIdea::class;
    public static string $slug = 'public-ideas';
    public static string $label = 'Idea Bank';
    public static string $singular = 'Public idea';
    public static ?string $description = 'Ideas shown in the public Idea Bank. Only publish ideas whose owners agreed; private portal submissions are never shown automatically.';
    public static string $icon = 'lightbulb';
    public static string $group = 'Content';
    public static string $permission = 'content.manage';
    public static int $navOrder = 61;

    public function fields(): array
    {
        return [
            Field::text('title')->required()->onIndex(),
            Field::text('slug')->required()->unique()->help('Web address: /ideas/{slug}')->rules(['regex:/^[a-z0-9-]+$/']),
            Field::text('code')->label('Idea ID')->required()->onIndex(),
            Field::select('area')->options(PublicIdea::AREAS)->required()->onIndex(),
            Field::select('stage')->label('Idea stage')->help('How far the idea has progressed; it sets the initiator title.')->options(PublicIdea::STAGES)->required()->onIndex(),
            Field::textarea('problem')->required()->rows(3),
            Field::textarea('solution')->required()->rows(3),
            Field::textarea('how')->label('How it works (optional)')->rows(5)->help('One step per line: "Step: explanation".'),
            Field::text('sdgs')->label('SDGs')->help('Comma-separated numbers'),
            Field::text('fingerprint')->label('SHA-256 fingerprint')->required()->rules(['regex:/^[a-f0-9]{64}$/']),
            Field::datetime('protected_at')->label('Protected at'),
            Field::select('cover_key')->label('Library photo')->options(Library::options()),
            Field::image('cover_path')->label('Or upload a cover')->directory('ideas'),
            Field::select('person_id')->label('Initiator')->options(Person::query()->orderBy('name')->pluck('name', 'id')->all()),
            Field::boolean('is_featured')->label('Featured idea (pin to the top of the Ideas page)')->onIndex(),
            Field::boolean('is_published')->label('Published')->onIndex(),
        ];
    }

    public function searchable(): array
    {
        return ['title', 'code'];
    }

    public function filters(): array
    {
        return ['area' => ['label' => 'Area', 'options' => PublicIdea::AREAS]];
    }

    public function title(Model $record): string
    {
        return (string) $record->title;
    }
}
