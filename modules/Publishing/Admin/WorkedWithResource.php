<?php

namespace Modules\Publishing\Admin;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Publishing\Models\WorkedWith;

class WorkedWithResource extends Resource
{
    public static string $model = WorkedWith::class;
    public static string $slug = 'worked-with';
    public static string $label = 'Worked with';
    public static string $singular = 'Project';
    public static ?string $description = 'Projects and clients on the Partners page. Only add a client name or logo when the client has confirmed it may be shown.';
    public static string $icon = 'briefcase';
    public static string $group = 'Content';
    public static string $permission = 'content.manage';
    public static int $navOrder = 63;

    public function fields(): array
    {
        return [
            Field::text('title')->required()->onIndex(),
            Field::text('client_name')->label('Client name (only if confirmed)')->onIndex(),
            Field::text('summary')->maxLength(500),
            Field::text('url')->label('Link')->help('Internal path like /initiatives/eon or a full URL.'),
            Field::image('logo_path')->label('Logo (only if confirmed)')->directory('worked-with'),
            Field::boolean('is_confirmed')->label('Client confirmed it may be shown')->onIndex(),
            Field::number('sort_order')->label('Order')->default(0),
            Field::boolean('is_published')->label('Published')->default(true)->onIndex(),
        ];
    }

    public function title(Model $record): string
    {
        return (string) $record->title;
    }
}
