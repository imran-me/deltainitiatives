<?php

namespace Modules\Applications\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Applications\Models\JoinApplication;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams an applicant's CV from private storage. Permission: inbox.manage.
 * Every download is written to the audit log.
 */
class CvController extends Controller
{
    public function __invoke(JoinApplication $application): StreamedResponse
    {
        abort_unless($application->cv_path && Storage::disk('local')->exists($application->cv_path), 404);

        audit('application.cv_downloaded', $application, "Downloaded CV for {$application->reference()} ({$application->name})");

        return Storage::disk('local')->download(
            $application->cv_path,
            Str::slug($application->name ?: 'applicant').'-'.$application->reference().'-cv.pdf',
            ['Content-Type' => 'application/pdf', 'X-Content-Type-Options' => 'nosniff']
        );
    }
}
