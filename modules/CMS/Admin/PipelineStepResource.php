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

class PipelineStepResource extends Resource
{
    public static string $model = PipelineStep::class;
    public static string $slug = 'pipeline';
    public static string $label = 'Pipeline steps';
    public static string $singular = 'Pipeline step';
    public static ?string $description = 'The six-step Idea-to-Impact pipeline, in order.';
    public static string $icon = 'route';
    public static int $navOrder = 30;

    public function fields(): array
    {
        return [
            Field::number('sort_order')->label('Step order')->required()->half()->onIndex(),
            Field::icon('icon')->required()->half(),
            Field::text('title')->required()->onIndex(),
            Field::textarea('summary')->required()->rows(3)->maxLength(400)->onIndex(),
            Field::boolean('is_published')->label('Published')->default(true),
        ];
    }

    public function title(Model $record): string
    {
        return 'Step '.$record->sort_order.' · '.$record->title;
    }
}
