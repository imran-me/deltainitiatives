<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Admin\Support\AdminDashboard;
use Modules\Admin\Services\DemoContent;
use Modules\Core\Models\AuditLog;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DemoContent $demo): View
    {
        $user = $request->user();

        return view('admin::dashboard', [
            'stats' => AdminDashboard::statsFor($user),
            'panels' => AdminDashboard::panelsFor($user),
            'demoCount' => $user->isSuperAdmin() ? $demo->count() : 0,
            'recentActivity' => $user->can('audit.view')
                ? AuditLog::query()->with('user')->latest('id')->limit(8)->get()
                : collect(),
        ]);
    }
}
