<?php

namespace Modules\Publishing\Admin;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Publishing\Models\FeedComment;

class FeedCommentResource extends Resource
{
    public static string $model = FeedComment::class;
    public static string $slug = 'feed-comments';
    public static string $label = 'Feed comments';
    public static string $singular = 'Comment';
    public static ?string $description = 'Comments on feed posts. Nothing is shown publicly until you set it to Approved.';
    public static string $icon = 'inbox';
    public static string $group = 'Community';
    public static string $permission = 'inbox.manage';
    public static int $navOrder = 45;
    public static bool $creatable = false;

    public function fields(): array
    {
        return [
            Field::text('name')->readonly()->onIndex(),
            Field::textarea('body')->label('Comment')->readonly()->onIndex(),
            Field::select('status')->options(FeedComment::STATUSES)->required()->onIndex(),
            Field::datetime('created_at')->label('Received')->readonly()->onIndex(),
        ];
    }

    public function searchable(): array
    {
        return ['name', 'body'];
    }

    public function filters(): array
    {
        return ['status' => ['label' => 'Status', 'options' => FeedComment::STATUSES]];
    }

    public function badgeCount(): ?int
    {
        return FeedComment::query()->where('status', 'pending')->count() ?: null;
    }

    public function title(Model $record): string
    {
        return (string) $record->name;
    }
}
