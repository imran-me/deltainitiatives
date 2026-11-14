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

class LegalPageResource extends Resource
{
    public static string $model = LegalPage::class;
    public static string $slug = 'legal-pages';
    public static string $label = 'Legal pages';
    public static string $singular = 'Legal page';
    public static ?string $description = 'Privacy Policy, Terms of Use and the Contributor Agreement. Publish before launch.';
    public static string $icon = 'scroll-text';
    public static int $navOrder = 90;
    public static bool $creatable = false;
    public static bool $deletable = false;

    public function fields(): array
    {
        return [
            Field::text('title')->required()->half()->onIndex(),
            Field::text('version')->required()->maxLength(20)->half()->onIndex()->help('Bump when the text changes. For the Contributor Agreement, also update Settings → Idea Portal.'),
            Field::date('effective_date')->half()->onIndex(),
            Field::boolean('is_published')->label('Published')->default(true)->half(),
            Field::textarea('summary')->rows(2)->maxLength(500),
            Field::markdown('body')->required()->rows(24),
        ];
    }

    public function title(Model $record): string
    {
        return $record->title;
    }

    public function subtitle(Model $record): ?string
    {
        return '/'.Str::of($record->slug);
    }
}
