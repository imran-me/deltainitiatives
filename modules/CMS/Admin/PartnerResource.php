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

class PartnerResource extends Resource
{
    public static string $model = Partner::class;
    public static string $slug = 'partners';
    public static string $label = 'Partner logos';
    public static string $singular = 'Partner';
    public static ?string $description = '“In collaboration with” logos on the home page.';
    public static string $icon = 'handshake';
    public static int $navOrder = 70;

    public function fields(): array
    {
        return [
            Field::text('name')->label('Organisation')->required()->half()->onIndex(),
            Field::select('category')->label('Partner category')->help('Decides which group the logo appears under on the Partners page.')->options(Partner::CATEGORIES)->half(),
            Field::url('website_url')->label('Website')->half(),
            Field::image('logo_path')->label('Logo')->directory('partners')->help('PNG or WebP with a transparent background works best.'),
            Field::boolean('permission_confirmed')->label('Written permission to show this logo has been received')->onIndex()
                ->help('The logo is hidden on the site until this is ticked.'),
            Field::text('permission_note')->label('Permission record')->maxLength(500)->help('Who approved it and when, e.g. “E-mail from Head of Comms, 3 Nov 2026”.'),
            Field::number('sort_order')->default(0)->half(),
            Field::boolean('is_published')->label('Published')->default(true)->half()->onIndex(),
        ];
    }

    public function notice(): ?string
    {
        return 'Only partners with confirmed written permission appear publicly. Until then the home page invites founding partners instead.';
    }
}
