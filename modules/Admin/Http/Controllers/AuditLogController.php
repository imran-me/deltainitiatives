<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Core\Models\AuditLog;
use Modules\Core\Support\Exporter;
use Modules\Users\Models\User;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = $this->filtered($request)->with('user')->paginate(50)->withQueryString();

        return view('admin::audit.index', [
            'logs' => $logs,
            'users' => User::query()->orderBy('name')->pluck('name', 'id'),
            'categories' => AuditLog::query()->selectRaw('DISTINCT action')->orderBy('action')->pluck('action')
                ->map(fn ($a) => explode('.', $a)[0])->unique()->values(),
        ]);
    }

    public function export(Request $request)
    {
        $rows = (function () use ($request) {
            foreach ($this->filtered($request)->with('user')->lazyById(500) as $log) {
                yield [
                    $log->id, $log->created_at?->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s'), $log->user->name,
                    $log->action, $log->description, $log->subject_type ? class_basename($log->subject_type).' #'.$log->subject_id : '',
                    $log->ip_address, $log->properties ? json_encode($log->properties, JSON_UNESCAPED_UNICODE) : '',
                ];
            }
        })();

        audit('audit.exported', null, 'Exported the audit log');

        return Exporter::download('audit-log', ['ID', 'Time (BST)', 'User', 'Action', 'Description', 'Subject', 'IP', 'Details'], $rows, $request->input('format', 'csv'));
    }

    protected function filtered(Request $request)
    {
        return AuditLog::query()
            ->when($request->filled('user'), fn ($q) => $q->where('user_id', $request->integer('user')))
            ->when($request->filled('category'), fn ($q) => $q->where('action', 'like', $request->input('category').'.%'))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('description', 'like', '%'.$request->input('q').'%')
                ->orWhere('action', 'like', '%'.$request->input('q').'%')
                ->orWhere('ip_address', 'like', '%'.$request->input('q').'%')))
            ->when($request->filled('from'), fn ($q) => $q->where('created_at', '>=', $request->date('from')->startOfDay()))
            ->when($request->filled('to'), fn ($q) => $q->where('created_at', '<=', $request->date('to')->endOfDay()))
            ->orderByDesc('id');
    }
}
