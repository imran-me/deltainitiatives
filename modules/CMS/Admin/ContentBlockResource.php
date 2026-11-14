<?php

namespace Modules\CMS\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\CMS\Models\Arm;
use Modules\CMS\Models\ContentBlock;
use Modules\CMS\Models\CoreValue;
use Modules\CMS\Models\FocusArea;
use Modules\CMS\Models\LegalPage;
use Modules\CMS\Models\Partner;
use Modules\CMS\Models\PipelineStep;
use Modules\CMS\Models\Stat;
use Modules\CMS\Models\TeamMember;
use Modules\CMS\Support\StatSources;

class ContentBlockResource extends Resource
{
    public static string $model = ContentBlock::class;
    public static string $slug = 'page-copy';
    public static string $label = 'Page copy';
    public static string $singular = 'Copy block';
    public static ?string $description = 'Headlines and paragraphs used across the public pages. Each block is used in one place on the site.';
    public static string $icon = 'file-text';
    public static int $navOrder = 10;
    public static bool $creatable = false;
    public static bool $deletable = false;
    public static int $perPage = 50;

    public function fields(): array
    {
        return [
            Field::text('label')->label('Where it appears')->readonly()->onIndex(),
            Field::text('key')->readonly()->hideOnForm()->onIndex()->displayUsing(fn ($r) => new HtmlString('<code class="text-xs text-navy-500">'.e($r->key).'</code>')),
            Field::text('eyebrow')->label('Eyebrow (small label above the title)')->maxLength(255),
            Field::textarea('title')->rows(2)->maxLength(500)->onIndex(),
            Field::markdown('body')->label('Body')->rows(8),
        ];
    }

    public function searchable(): array
    {
        return ['key', 'label', 'title', 'body'];
    }

    public function applyDefaultSort($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy('key');
    }

    public function title(Model $record): string
    {
        return $record->label;
    }

    public function afterSave(Model $record, Request $request, bool $created): void
    {
        Cache::forget('delta.blocks');
    }
}
