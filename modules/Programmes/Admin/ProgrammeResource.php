<?php

namespace Modules\Programmes\Admin;

use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Programmes\Models\Programme;

class ProgrammeResource extends Resource
{
    public static string $model = Programme::class;
    public static string $slug = 'programmes';
    public static string $label = 'Programmes';
    public static string $singular = 'Programme';
    public static ?string $description = 'The programme types shown on Programmes & Events: Delta Challenge, Hackathon, Dialogue, Fellows, Talks and Demo Day.';
    public static string $icon = 'presentation';
    public static string $group = 'Programmes';
    public static string $permission = 'programmes.manage';
    public static int $navOrder = 10;

    public function fields(): array
    {
        return [
            Field::text('name')->required()->half()->onIndex(),
            Field::text('slug')->required()->unique()->half()->rules(['alpha_dash'])->help('Used as the anchor, e.g. /programmes#hackathon'),
            Field::text('tagline')->half()->onIndex(),
            Field::text('cadence')->maxLength(60)->half()->onIndex()->help('e.g. “Annual”, “Monthly”'),
            Field::icon('icon')->required()->half(),
            Field::number('sort_order')->default(0)->half(),
            Field::textarea('summary')->required()->rows(3)->maxLength(600),
            Field::markdown('body')->label('How it works (optional)')->rows(8),
            Field::boolean('is_published')->label('Published')->default(true)->onIndex(),
        ];
    }

    public function searchable(): array
    {
        return ['name', 'tagline'];
    }
}
