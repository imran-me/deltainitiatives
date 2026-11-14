<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Core\Services\SettingsService;

class SettingsController extends Controller
{
    public function edit(SettingsService $settings): View
    {
        return view('admin::settings', [
            'groups' => config('admin.settings'),
            'values' => $settings->all(),
        ]);
    }

    public function update(Request $request, SettingsService $settings): RedirectResponse
    {
        $definitions = collect(config('admin.settings'))->collapse();

        $rules = $definitions->map(fn ($def) => match ($def['type']) {
            'email' => ['nullable', 'email', 'max:255'],
            'url' => ['nullable', 'url:http,https', 'max:500'],
            'boolean' => ['nullable', 'boolean'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'textarea' => ['nullable', 'string', 'max:2000'],
            default => ['nullable', 'string', 'max:500'],
        })->all();

        $request->validate($rules, [], $definitions->map(fn ($d) => $d['label'])->all());

        $before = $settings->all();
        $values = [];

        foreach ($definitions as $key => $def) {
            if ($def['type'] === 'file') {
                if ($request->hasFile($key)) {
                    if (! empty($before[$key])) {
                        Storage::disk('local')->delete($before[$key]);
                    }
                    $values[$key] = $request->file($key)->storeAs('brochures', 'delta-initiatives-partnership-brochure-'.now()->format('Ymd-His').'.pdf', 'local');
                } elseif ($request->boolean('remove_'.$key) && ! empty($before[$key])) {
                    Storage::disk('local')->delete($before[$key]);
                    $values[$key] = null;
                }

                continue;
            }

            $values[$key] = $def['type'] === 'boolean' ? ($request->boolean($key) ? '1' : '0') : $request->input($key);
        }

        $settings->set($values);

        $changed = collect($values)->filter(fn ($v, $k) => ($before[$k] ?? null) !== $v)->keys()->all();
        audit('settings.updated', null, 'Updated site settings', ['keys' => $changed]);

        return back()->with('success', 'Settings saved.');
    }
}
