<?php

namespace Modules\Publishing\Admin;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Publishing\Models\ExpertApplication;

class ExpertApplicationResource extends Resource
{
    public static string $model = ExpertApplication::class;
    public static string $slug = 'expert-applications';
    public static string $label = 'Expert applications';
    public static string $singular = 'Expert application';
    public static ?string $description = 'Applications to join the Delta Expert Network. Profiles are listed on the site only after review.';
    public static string $icon = 'users';
    public static string $group = 'Community';
    public static string $permission = 'inbox.manage';
    public static int $navOrder = 47;
    public static bool $creatable = false;

    public function fields(): array
    {
        return [
            Field::text('name')->readonly()->onIndex(),
            Field::text('field')->readonly()->onIndex(),
            Field::email('email')->readonly(),
            Field::text('institution')->readonly(),
            Field::textarea('expertise')->readonly(),
            Field::url('profile_url')->label('Profile link')->readonly(),
            Field::select('status')->options(ExpertApplication::STATUSES)->required()->onIndex(),
            Field::datetime('created_at')->label('Received')->readonly()->onIndex(),
        ];
    }

    public function searchable(): array
    {
        return ['name', 'email', 'field'];
    }

    public function filters(): array
    {
        return ['status' => ['label' => 'Status', 'options' => ExpertApplication::STATUSES]];
    }

    public function badgeCount(): ?int
    {
        return ExpertApplication::query()->where('status', 'new')->count() ?: null;
    }

    public function title(Model $record): string
    {
        return (string) $record->name;
    }
}
