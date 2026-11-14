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

class TeamMemberResource extends Resource
{
    public static string $model = TeamMember::class;
    public static string $slug = 'team';
    public static string $label = 'Team & advisors';
    public static string $singular = 'Person';
    public static ?string $description = 'People shown on the About page. Use real photos of the team only — never stock images. Without a photo a Δ monogram is shown.';
    public static string $icon = 'users';
    public static int $navOrder = 60;

    public function fields(): array
    {
        return [
            Field::text('name')->required()->half()->onIndex(),
            Field::text('role')->label('Role / title')->required()->half()->onIndex(),
            Field::select('group')->options(TeamMember::GROUPS)->required()->default('team')->half()->onIndex(),
            Field::url('linkedin_url')->label('LinkedIn URL')->half(),
            Field::textarea('bio')->rows(3)->maxLength(600)->help('One or two sentences.'),
            Field::image('photo_path')->label('Photo')->directory('team')->help('Square, at least 600×600 px.'),
            Field::number('sort_order')->default(0)->half(),
            Field::boolean('is_published')->label('Published')->default(true)->half()->onIndex(),
        ];
    }

    public function searchable(): array
    {
        return ['name', 'role'];
    }

    public function filters(): array
    {
        return ['group' => ['label' => 'Group', 'options' => TeamMember::GROUPS]];
    }

    public function afterSave(Model $record, Request $request, bool $created): void
    {
        // Editing a sample entry turns it into real content.
        if (! $created && $record->is_demo && $record->wasChanged(['name', 'role', 'photo_path'])) {
            $record->forceFill(['is_demo' => false])->saveQuietly();
        }
    }
}
