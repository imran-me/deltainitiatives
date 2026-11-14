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

class CoreValueResource extends Resource
{
    public static string $model = CoreValue::class;
    public static string $slug = 'values';
    public static string $label = 'Values';
    public static string $singular = 'Value';
    public static string $icon = 'badge-check';
    public static int $navOrder = 50;

    public function fields(): array
    {
        return [
            Field::text('title')->required()->half()->onIndex(),
            Field::icon('icon')->required()->half(),
            Field::textarea('summary')->required()->rows(3)->maxLength(400)->onIndex(),
            Field::number('sort_order')->default(0)->half(),
            Field::boolean('is_published')->label('Published')->default(true)->half(),
        ];
    }
}
