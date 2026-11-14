<?php

namespace Modules\Admin\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * Declarative admin CRUD. Subclass, define fields(), and register the class
 * in a module's service provider ($resources = [...]). The generic
 * ResourceController renders index / create / edit / show / export screens,
 * validates input, handles uploads and writes every change to the audit log.
 *
 * Inbox-style resources (contact messages, inquiries, applications) set
 * $creatable = false and $hasDetail = true, and expose only status/notes fields
 * for editing while detailFields() shows the full submission.
 */
abstract class Resource
{
    /** @var class-string<Model> */
    public static string $model;

    public static string $slug;

    public static string $label;

    public static string $singular;

    public static ?string $description = null;

    public static string $icon = 'list';

    public static string $group = 'Content';

    public static string $permission = 'content.manage';

    public static int $navOrder = 100;

    public static bool $showInNav = true;

    public static bool $creatable = true;

    public static bool $editable = true;

    public static bool $deletable = true;

    public static bool $exportable = false;

    public static bool $hasDetail = false;

    /** Log an audit entry whenever a record's detail page is opened (personal data). */
    public static bool $auditViews = false;

    public static int $perPage = 25;

    /** @return array<int, Field> */
    abstract public function fields(): array;

    /** Fields shown on the detail page (defaults to all fields). @return array<int, Field> */
    public function detailFields(): array
    {
        return array_values(array_filter($this->fields(), fn (Field $f) => ! $f->hideOnDetail));
    }

    /** @return array<int, Field> */
    public function formFields(): array
    {
        return array_values(array_filter($this->fields(), fn (Field $f) => ! $f->hideOnForm));
    }

    /** @return array<int, Field> */
    public function indexFields(): array
    {
        $all = collect(array_merge($this->fields(), $this->detailFields()))->unique('name')->values();
        $marked = $all->filter(fn (Field $f) => $f->onIndex)->values();

        return ($marked->isNotEmpty() ? $marked : $all->take(3))->all();
    }

    /** Columns searched by the index search box. @return array<int, string> */
    public function searchable(): array
    {
        return [];
    }

    /**
     * Index filters: ['status' => ['label' => 'Status', 'options' => ['new' => 'New']]].
     *
     * @return array<string, array{label: string, options: array<string, string>}>
     */
    public function filters(): array
    {
        return [];
    }

    public function query(): Builder
    {
        return static::$model::query();
    }

    public function applyDefaultSort(Builder $query): Builder
    {
        $table = $query->getModel()->getTable();

        if (Schema::hasColumn($table, 'sort_order')) {
            return $query->orderBy('sort_order')->orderBy('id');
        }

        return $query->latest($query->getModel()->getQualifiedKeyName());
    }

    public function title(Model $record): string
    {
        return (string) ($record->name ?? $record->title ?? $record->email ?? static::$singular.' #'.$record->getKey());
    }

    public function subtitle(Model $record): ?string
    {
        return null;
    }

    /** Sidebar badge (e.g. unread count). */
    public function badgeCount(): ?int
    {
        return null;
    }

    /** Called when a detail page is opened (e.g. mark message as read). */
    public function onShow(Model $record): void {}

    public function afterSave(Model $record, Request $request, bool $created): void {}

    public function beforeDelete(Model $record): void {}

    /**
     * Export columns: label => attribute name or closure(Model): mixed.
     *
     * @return array<string, string|\Closure>
     */
    public function exportColumns(): array
    {
        return collect($this->detailFields())->mapWithKeys(fn (Field $f) => [$f->label => $f->name])->all();
    }

    public function exportQuery(Request $request): Builder
    {
        return $this->query();
    }

    /** Optional Blade view rendered above the index table (help text, etc.). */
    public function notice(): ?string
    {
        return null;
    }

    public static function newModel(): Model
    {
        return new static::$model;
    }

    public function rowUrl(Model $record): ?string
    {
        if (static::$hasDetail) {
            return route('admin.resources.show', [static::$slug, $record->getKey()]);
        }

        return static::$editable ? route('admin.resources.edit', [static::$slug, $record->getKey()]) : null;
    }
}
