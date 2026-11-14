<?php

use Illuminate\Support\Facades\Cache;
use Modules\CMS\Models\ContentBlock;

if (! function_exists('block')) {
    /**
     * An editable copy block (Admin → Content → Page copy). Always returns a
     * ContentBlock so views can safely read ->eyebrow, ->title and ->body.
     */
    function block(string $key): ContentBlock
    {
        try {
            $blocks = Cache::rememberForever('delta.blocks', fn () => ContentBlock::query()->get()->keyBy('key'));
        } catch (Throwable) {
            $blocks = collect();
        }

        return $blocks->get($key) ?? new ContentBlock(['key' => $key, 'label' => $key]);
    }
}
