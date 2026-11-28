<?php

namespace Modules\Publishing\Support;

use Illuminate\Support\Facades\Storage;

/**
 * The curated photo library in public/images/library (Wikimedia Commons, CC0 / CC BY / public domain).
 * Each key has WebP files at several widths: {key}-{width}.webp. Metadata lives in config('publishing.library').
 */
class Library
{
    /** @return array<string, array<string, mixed>> */
    public static function all(): array
    {
        return (array) config('publishing.library', []);
    }

    public static function has(?string $key): bool
    {
        return $key !== null && $key !== '' && array_key_exists($key, static::all());
    }

    /** @return array<string, mixed>|null */
    public static function get(?string $key): ?array
    {
        return static::has($key) ? static::all()[$key] : null;
    }

    /** @return array<string, string> key => caption, for admin select fields */
    public static function options(): array
    {
        return collect(static::all())->mapWithKeys(fn (array $m, string $k) => [$k => $m['caption'].' ('.$k.')'])->all();
    }

    /**
     * Attributes for an <img>: src, srcset, width, height, alt.
     *
     * @return array<string, string|int>|null
     */
    public static function image(?string $key, ?string $uploadedPath = null, string $alt = ''): ?array
    {
        if ($meta = static::get($key)) {
            $widths = (array) ($meta['widths'] ?? []);
            $largest = (int) end($widths);

            return [
                'src' => asset('images/library/'.$key.'-'.$largest.'.webp'),
                'srcset' => collect($widths)->map(fn ($w) => asset('images/library/'.$key.'-'.$w.'.webp').' '.$w.'w')->implode(', '),
                'width' => (int) $meta['w'],
                'height' => (int) $meta['h'],
                'alt' => $alt !== '' ? $alt : (string) $meta['alt'],
            ];
        }

        if ($uploadedPath && str_starts_with($uploadedPath, 'images/') && is_file(public_path($uploadedPath))) {
            [$w, $h] = @getimagesize(public_path($uploadedPath)) ?: [1600, 1000];

            return ['src' => asset($uploadedPath), 'srcset' => '', 'width' => $w, 'height' => $h, 'alt' => $alt !== '' ? $alt : 'Photo from Delta Initiatives'];
        }

        if ($uploadedPath) {
            return ['src' => Storage::disk('public')->url($uploadedPath), 'srcset' => '', 'width' => 1600, 'height' => 1000, 'alt' => $alt];
        }

        return null;
    }

    /** Credit line for a library image, or null. */
    public static function credit(?string $key): ?array
    {
        $meta = static::get($key);

        return $meta ? ['author' => $meta['author'], 'licence' => $meta['licence'], 'licence_url' => $meta['licence_url'] ?? '', 'source' => $meta['source'], 'caption' => $meta['caption']] : null;
    }
}
