<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Admin\Services\BackupService;
use Modules\Admin\Services\DemoContent;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class BackupController extends Controller
{
    public function index(BackupService $backups): View
    {
        return view('admin::backups', ['backups' => $backups->list()]);
    }

    public function store(BackupService $backups): RedirectResponse
    {
        try {
            $name = $backups->create();
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Backup failed: '.$e->getMessage());
        }

        audit('backup.created', null, "Created backup {$name}");

        return back()->with('success', "Backup {$name} created.");
    }

    public function download(BackupService $backups, string $name): BinaryFileResponse
    {
        $path = $backups->path($name);
        audit('backup.downloaded', null, "Downloaded backup {$name}");

        return response()->download($path, $name, ['Content-Type' => 'application/zip']);
    }

    public function purgeDemo(DemoContent $demo): RedirectResponse
    {
        $count = $demo->purge();
        audit('demo.purged', null, "Removed {$count} sample (demo) records");

        return redirect()->route('admin.dashboard')->with('success', "Removed {$count} sample records. The site now shows only real content.");
    }
}
