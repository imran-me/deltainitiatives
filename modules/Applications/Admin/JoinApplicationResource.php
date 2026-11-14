<?php

namespace Modules\Applications\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Applications\Models\JoinApplication;

/**
 * Admin inbox for Join Us applications (Member, Fellow, Campus Ambassador, Volunteer).
 */
class JoinApplicationResource extends Resource
{
    public static string $model = JoinApplication::class;
    public static string $slug = 'join-applications';
    public static string $label = 'Join Us applications';
    public static string $singular = 'Application';
    public static ?string $description = 'Applications from the Join Us page (English and Bangla). Opening an application is recorded in the audit log.';
    public static string $icon = 'user-plus';
    public static string $group = 'Inbox';
    public static string $permission = 'inbox.manage';
    public static int $navOrder = 30;
    public static bool $creatable = false;
    public static bool $hasDetail = true;
    public static bool $auditViews = true;
    public static bool $exportable = true;

    protected const TONES = [
        'new' => 'bg-river-50 text-river-800 ring-river-200',
        'reviewing' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'accepted' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
        'declined' => 'bg-navy-50 text-navy-600 ring-navy-100',
    ];

    /** Editable on the detail page. */
    public function fields(): array
    {
        return [
            Field::select('status')->options(JoinApplication::STATUSES)->required(),
            Field::textarea('admin_notes')->label('Internal notes')->rows(5)->maxLength(5000)->help('Visible to the team only.'),
        ];
    }

    public function indexFields(): array
    {
        return [
            Field::text('name'),
            Field::select('role')->options(JoinApplication::roleOptions()),
            Field::text('institution'),
            Field::select('status')->displayUsing(fn (JoinApplication $a) => static::statusBadge($a->status)),
            Field::datetime('created_at')->label('Received'),
        ];
    }

    public function detailFields(): array
    {
        return [
            Field::text('reference')->displayUsing(fn (JoinApplication $a) => new HtmlString('<span class="font-mono text-xs">'.e($a->reference()).'</span>')),
            Field::select('status')->displayUsing(fn (JoinApplication $a) => static::statusBadge($a->status)),
            Field::select('role')->options(JoinApplication::roleOptions()),
            Field::text('name')->label('Full name'),
            Field::email('email')->label('E-mail'),
            Field::text('phone')->displayUsing(fn (JoinApplication $a) => new HtmlString('<a href="tel:'.e(preg_replace('/[^0-9+]/', '', (string) $a->phone)).'" class="text-river-700 underline underline-offset-2">'.e($a->phone).'</a>')),
            Field::text('city')->label('City / district'),
            Field::text('institution'),
            Field::select('study_level')->label('Current stage')->options(JoinApplication::studyLevelOptions()),
            Field::text('department'),
            Field::url('linkedin_url')->label('LinkedIn'),
            Field::select('availability')->options(JoinApplication::availabilityOptions()),
            Field::textarea('skills')->label('Skills and experience'),
            Field::textarea('motivation')->label('Motivation'),
            Field::text('cv_path')->label('CV')->displayUsing(fn (JoinApplication $a) => $a->cv_path
                ? new HtmlString('<a href="'.e(route('admin.applications.cv', $a)).'" class="inline-flex items-center gap-1.5 rounded-full bg-navy-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-navy-700">Download CV (PDF)</a> <span class="ml-2 text-xs text-navy-400">Downloads are audited</span>')
                : new HtmlString('<span class="text-navy-400">Not provided</span>')),
            Field::text('locale')->label('Language')->displayUsing(fn (JoinApplication $a) => $a->locale === 'bn' ? 'Bangla (বাংলা)' : 'English'),
            Field::datetime('consent_at')->label('Privacy consent given'),
            Field::text('ip_address')->label('IP address'),
        ];
    }

    public function searchable(): array
    {
        return ['name', 'email', 'institution', 'city'];
    }

    public function filters(): array
    {
        return [
            'role' => ['label' => 'Role', 'options' => JoinApplication::roleOptions()],
            'status' => ['label' => 'Status', 'options' => JoinApplication::STATUSES],
        ];
    }

    public function applyDefaultSort(Builder $query): Builder
    {
        return $query->latest('id');
    }

    public function subtitle(Model $record): ?string
    {
        /** @var JoinApplication $record */
        return $record->reference().' · '.$record->roleLabel().' · received '.dhaka_time($record->created_at);
    }

    public function badgeCount(): ?int
    {
        return JoinApplication::query()->where('status', 'new')->count() ?: null;
    }

    public function exportColumns(): array
    {
        return [
            'Reference' => fn (JoinApplication $a) => $a->reference(),
            'Received (BST)' => fn (JoinApplication $a) => $a->created_at?->setTimezone('Asia/Dhaka')->format('Y-m-d H:i'),
            'Role' => fn (JoinApplication $a) => $a->roleLabel(),
            'Name' => 'name',
            'E-mail' => 'email',
            'Phone' => 'phone',
            'City' => 'city',
            'Institution' => 'institution',
            'Current stage' => fn (JoinApplication $a) => JoinApplication::studyLevelOptions()[$a->study_level] ?? $a->study_level,
            'Department' => 'department',
            'LinkedIn' => 'linkedin_url',
            'Availability' => fn (JoinApplication $a) => JoinApplication::availabilityOptions()[$a->availability] ?? $a->availability,
            'Skills' => 'skills',
            'Motivation' => 'motivation',
            'CV provided' => fn (JoinApplication $a) => (bool) $a->cv_path,
            'Language' => 'locale',
            'Status' => fn (JoinApplication $a) => JoinApplication::STATUSES[$a->status] ?? $a->status,
            'Internal notes' => 'admin_notes',
            'Sample' => fn (JoinApplication $a) => $a->is_demo,
        ];
    }

    public function exportQuery(Request $request): Builder
    {
        return $this->query()
            ->when(in_array($request->input('role'), JoinApplication::ROLES, true), fn ($q) => $q->where('role', $request->input('role')))
            ->when(array_key_exists((string) $request->input('status'), JoinApplication::STATUSES), fn ($q) => $q->where('status', $request->input('status')));
    }

    public static function statusBadge(?string $status): HtmlString
    {
        $tone = self::TONES[$status] ?? self::TONES['declined'];

        return new HtmlString('<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset '.$tone.'">'.e(JoinApplication::STATUSES[$status] ?? $status).'</span>');
    }
}
