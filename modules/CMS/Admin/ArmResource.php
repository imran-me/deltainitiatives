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

class ArmResource extends Resource
{
    public static string $model = Arm::class;
    public static string $slug = 'arms';
    public static string $label = 'Institutional arms';
    public static string $singular = 'Arm';
    public static ?string $description = 'Delta Labs, Delta Policy and Delta Challenge — shown on Home and What We Do.';
    public static string $icon = 'layers';
    public static int $navOrder = 20;

    public function fields(): array
    {
        return [
            Field::text('name')->required()->half()->onIndex(),
            Field::text('slug')->required()->unique()->half()->rules(['alpha_dash'])->help('Used in links, e.g. /what-we-do#delta-labs'),
            Field::text('tagline')->half()->onIndex(),
            Field::icon('icon')->required()->half(),
            Field::textarea('summary')->required()->rows(3)->maxLength(600),
            Field::markdown('body')->label('Full description (What We Do page)'),
            Field::textarea('highlights')->rows(5)->help('One highlight per line.'),
            Field::number('sort_order')->default(0)->half(),
            Field::boolean('is_published')->label('Published')->default(true)->half()->onIndex(),
        ];
    }
}
