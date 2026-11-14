<?php

namespace Modules\Partnerships\Admin;

use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Partnerships\Models\PartnershipType;

class PartnershipTypeResource extends Resource
{
    public static string $model = PartnershipType::class;
    public static string $slug = 'partnership-types';
    public static string $label = 'Partnership types';
    public static string $singular = 'Partnership type';
    public static ?string $description = 'The ways to partner shown on Partner with Us, and the options in the inquiry form.';
    public static string $icon = 'handshake';
    public static string $group = 'Content';
    public static string $permission = 'content.manage';
    public static int $navOrder = 75;

    public function fields(): array
    {
        return [
            Field::text('name')->required()->half()->onIndex(),
            Field::text('slug')->required()->unique()->half()->rules(['alpha_dash'])->help('Used in links, e.g. /partner-with-us?type=your-slug'),
            Field::icon('icon')->required()->half(),
            Field::number('sort_order')->default(0)->half(),
            Field::textarea('summary')->required()->rows(3)->maxLength(500)->onIndex(),
            Field::textarea('benefits')->rows(5)->help('What partners get — one benefit per line.'),
            Field::boolean('is_published')->label('Published')->default(true)->onIndex(),
        ];
    }

    public function searchable(): array
    {
        return ['name', 'summary'];
    }
}
