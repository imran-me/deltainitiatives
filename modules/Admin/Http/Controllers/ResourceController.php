<?php

namespace Modules\Admin\Http\Controllers;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Admin\Support\Field;
use Modules\Admin\Support\Resource;
use Modules\Admin\Support\ResourceRegistry;
use Modules\Core\Services\AuditLogger;
use Modules\Core\Support\Exporter;

class ResourceController extends Controller
{
    public function index(Request $request, string $resource): View
    {
        $res = $this->resolve($resource);
        $query = $res->query();

        if ($request->filled('q') && $columns = $res->searchable()) {
            $term = '%'.str_replace(['%', '_'], ['\%', '\_'], (string) $request->input('q')).'%';
            $query->where(function ($w) use ($columns, $term) {
                foreach ($columns as $column) {
                    $w->orWhere($column, 'like', $term);
                }
            });
        }

        foreach ($res->filters() as $column => $filter) {
            if ($request->filled($column) && array_key_exists($request->input($column), $filter['options'])) {
                $query->where($column, $request->input($column));
            }
        }

        $records = $res->applyDefaultSort($query)->paginate($res::$perPage)->withQueryString();

        return view('admin::resources.index', ['res' => $res, 'records' => $records]);
    }

    public function create(string $resource): View
    {
        $res = $this->resolve($resource);
        abort_unless($res::$creatable, 404);

        return view('admin::resources.form', ['res' => $res, 'record' => $res::newModel()]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $res = $this->resolve($resource);
        abort_unless($res::$creatable, 404);

        $record = $res::newModel();
        $this->validateAndFill($request, $res, $record);

        DB::transaction(function () use ($record, $res, $request) {
            $record->save();
            $res->afterSave($record, $request, true);
        });

        audit('resource.created', $record, "Created {$res::$singular}: {$res->title($record)}", ['resource' => $res::$slug]);

        return redirect()->route('admin.resources.index', $res::$slug)->with('success', "{$res::$singular} created.");
    }

    public function show(string $resource, string $id): View
    {
        $res = $this->resolve($resource);
        abort_unless($res::$hasDetail, 404);
        $record = $this->find($res, $id);

        $res->onShow($record);

        if ($res::$auditViews) {
            audit('resource.viewed', $record, "Viewed {$res::$singular}: {$res->title($record)}", ['resource' => $res::$slug]);
        }

        return view('admin::resources.show', ['res' => $res, 'record' => $record]);
    }

    public function edit(string $resource, string $id): View
    {
        $res = $this->resolve($resource);
        abort_unless($res::$editable, 404);

        return view('admin::resources.form', ['res' => $res, 'record' => $this->find($res, $id)]);
    }

    public function update(Request $request, string $resource, string $id): RedirectResponse
    {
        $res = $this->resolve($resource);
        abort_unless($res::$editable, 404);

        $record = $this->find($res, $id);
        $this->validateAndFill($request, $res, $record);

        DB::transaction(function () use ($record, $res, $request) {
            $record->save();
            $res->afterSave($record, $request, false);
        });

        audit('resource.updated', $record, "Updated {$res::$singular}: {$res->title($record)}", [
            'resource' => $res::$slug,
            'changes' => app(AuditLogger::class)->diff($record),
        ]);

        $redirect = $res::$hasDetail
            ? redirect()->route('admin.resources.show', [$res::$slug, $record->getKey()])
            : redirect()->route('admin.resources.index', $res::$slug);

        return $redirect->with('success', "{$res::$singular} saved.");
    }

    public function destroy(string $resource, string $id): RedirectResponse
    {
        $res = $this->resolve($resource);
        abort_unless($res::$deletable, 404);

        $record = $this->find($res, $id);
        $title = $res->title($record);

        $res->beforeDelete($record);
        foreach ($res->fields() as $field) {
            $field->deleteStored($record);
        }
        $record->delete();

        audit('resource.deleted', null, "Deleted {$res::$singular}: {$title}", ['resource' => $res::$slug, 'id' => $id]);

        return redirect()->route('admin.resources.index', $res::$slug)->with('success', "{$res::$singular} deleted.");
    }

    public function export(Request $request, string $resource)
    {
        $res = $this->resolve($resource);
        abort_unless($res::$exportable, 404);

        $columns = $res->exportColumns();
        $format = $request->input('format') === 'xlsx' ? 'xlsx' : 'csv';

        $rows = (function () use ($res, $request, $columns) {
            foreach ($res->exportQuery($request)->lazyById(500) as $record) {
                yield array_map(fn ($col) => $col instanceof Closure ? $col($record) : data_get($record, $col), array_values($columns));
            }
        })();

        audit('resource.exported', null, "Exported {$res::$label} ({$format})", ['resource' => $res::$slug]);

        return Exporter::download($res::$slug, array_keys($columns), $rows, $format);
    }

    protected function resolve(string $slug): Resource
    {
        $class = ResourceRegistry::find($slug) ?? abort(404);
        Gate::authorize($class::$permission);

        return app($class);
    }

    protected function find(Resource $res, string $id): Model
    {
        return $res->query()->whereKey($id)->firstOrFail();
    }

    protected function validateAndFill(Request $request, Resource $res, Model $record): void
    {
        $table = $record->getTable();
        $rules = [];
        $attributes = [];

        foreach ($res->formFields() as $field) {
            /** @var Field $field */
            $rules[$field->name] = $field->validationRules($record->exists ? $record : null, $table);
            $attributes[$field->name] = $field->label;
            if ($field->isUpload()) {
                $rules['remove_'.$field->name] = ['nullable', 'boolean'];
            }
        }

        $request->validate(array_filter($rules), [], $attributes);

        foreach ($res->formFields() as $field) {
            $field->fill($record, $request);
        }
    }
}
