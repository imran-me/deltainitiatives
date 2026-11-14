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

class FocusAreaResource extends Resource
{
    public static string $model = FocusArea::class;
    public static string $slug = 'focus-areas';
    public static string $label = 'Focus areas';
    public static string $singular = 'Focus area';
    public static ?string $description = 'The eight focus areas. Also used as the theme list on the Idea Portal form.';
    public static string $icon = 'target';
    public static int $navOrder = 40;

    public function fields(): array
    {
        return [
            Field::text('name')->required()->half()->onIndex(),
            Field::text('slug')->required()->unique()->half()->rules(['alpha_dash']),
            Field::icon('icon')->required()->half(),
            Field::number('sort_order')->default(0)->half(),
            Field::textarea('summary')->required()->rows(2)->maxLength(400)->onIndex(),
            Field::textarea('examples')->rows(4)->help('Example problems, one per line.'),
            Field::boolean('is_published')->label('Published')->default(true)->onIndex(),
        ];
    }
}
