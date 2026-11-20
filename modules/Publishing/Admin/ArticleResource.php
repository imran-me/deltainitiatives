<?php

namespace Modules\Publishing\Admin;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Publishing\Models\Article;
use Modules\Publishing\Models\PublicIdea;
use Modules\Publishing\Support\Library;

class ArticleResource extends Resource
{
    public static string $model = Article::class;
    public static string $slug = 'articles';
    public static string $label = 'Insights';
    public static string $singular = 'Article';
    public static ?string $description = 'Articles on the Insights page. Body is Markdown: use ## for section headings (they become the table of contents) and > for pull quotes.';
    public static string $icon = 'newspaper';
    public static string $group = 'Content';
    public static string $permission = 'content.manage';
    public static int $navOrder = 60;

    public function fields(): array
    {
        return [
            Field::text('title')->required()->onIndex(),
            Field::text('slug')->required()->unique()->help('Web address: /insights/{slug}. Lower-case letters, numbers and hyphens.')->rules(['regex:/^[a-z0-9-]+$/']),
            Field::text('title_bn')->label('Title in Bangla'),
            Field::select('category')->options(Article::CATEGORIES)->required()->onIndex(),
            Field::select('area')->options(PublicIdea::AREAS),
            Field::text('tags')->help('Comma-separated, e.g. flood, sensors'),
            Field::textarea('summary')->label('Three-line summary')->required()->rows(3)->help('One line per point.'),
            Field::markdown('body')->required(),
            Field::select('cover_key')->label('Library photo')->options(Library::options()),
            Field::image('cover_path')->label('Or upload a cover')->directory('articles'),
            Field::text('author_name')->label('Author')->default('Delta Initiatives'),
            Field::text('status_label')->label('Status label')->help('e.g. Published or Forthcoming · Q2 2027'),
            Field::number('reading_minutes')->label('Reading time (minutes)'),
            Field::text('sdgs')->label('SDGs')->help('Comma-separated numbers, e.g. 13, 11'),
            Field::datetime('published_at')->label('Publish at')->required()->onIndex(),
            Field::boolean('is_published')->label('Published')->default(true)->onIndex(),
        ];
    }

    public function searchable(): array
    {
        return ['title', 'summary', 'tags'];
    }

    public function filters(): array
    {
        return ['category' => ['label' => 'Category', 'options' => Article::CATEGORIES]];
    }

    public function title(Model $record): string
    {
        return (string) $record->title;
    }
}
