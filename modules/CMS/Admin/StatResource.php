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

class StatResource extends Resource
{
    public static string $model = Stat::class;
    public static string $slug = 'numbers';
    public static string $label = 'Headline numbers';
    public static string $singular = 'Number';
    public static string $icon = 'chart-column';
    public static int $navOrder = 80;

    public function fields(): array
    {
        return [
            Field::text('label')->required()->half()->onIndex(),
            Field::select('source')->options(StatSources::options())->required()->default('manual')->half()->onIndex()
                ->help('Live sources count only real (non-sample) records.'),
            Field::number('value')->label('Value (manual only)')->half()
                ->displayUsing(fn (Stat $s) => number_format($s->resolvedValue())),
            Field::text('suffix')->maxLength(10)->half()->help('e.g. “+”'),
            Field::boolean('is_verified')->label('Verified (manual numbers only)')->onIndex(),
            Field::text('verified_note')->label('How it was verified')->maxLength(500),
            Field::number('sort_order')->default(0)->half(),
            Field::boolean('is_published')->label('Published')->default(true)->half(),
            Field::text('shown')->label('Shown on site')->hideOnForm()->onIndex()
                ->displayUsing(fn (Stat $s) => new HtmlString($s->isDisplayable()
                    ? '<span class="text-river-700 font-medium">Yes · '.number_format($s->resolvedValue()).e($s->suffix).'</span>'
                    : '<span class="text-navy-400">Hidden</span>')),
        ];
    }

    public function notice(): ?string
    {
        return 'No invented numbers: a figure appears on the home page only if it is published, greater than zero, and either counted live from real records or marked verified. The section hides itself until at least one figure qualifies.';
    }
}
