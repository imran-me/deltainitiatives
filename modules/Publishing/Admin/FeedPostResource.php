<?php

namespace Modules\Publishing\Admin;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Publishing\Models\FeedPost;
use Modules\Publishing\Support\Library;

class FeedPostResource extends Resource
{
    public static string $model = FeedPost::class;
    public static string $slug = 'feed-posts';
    public static string $label = 'Feed posts';
    public static string $singular = 'Feed post';
    public static ?string $description = 'Short updates shown on the public Feed. Posts appear once published and the publish time has passed.';
    public static string $icon = 'newspaper';
    public static string $group = 'Content';
    public static string $permission = 'content.manage';
    public static int $navOrder = 70;

    public function fields(): array
    {
        return [
            Field::textarea('body')->label('Post text')->required()->maxLength(2000)->rows(5)->onIndex(),
            Field::select('image_key')->label('Library photo')->options(Library::options())->help('Optional: a credited photo from the image library.'),
            Field::image('image_path')->label('Or upload an image')->directory('feed'),
            Field::url('link_url')->label('Link (optional)'),
            Field::text('link_title')->label('Link title'),
            Field::datetime('published_at')->label('Publish at')->required()->onIndex(),
            Field::boolean('is_published')->label('Published')->default(true)->onIndex(),
        ];
    }

    public function searchable(): array
    {
        return ['body', 'link_title'];
    }

    public function title(Model $record): string
    {
        return \Illuminate\Support\Str::limit((string) $record->body, 60);
    }
}
