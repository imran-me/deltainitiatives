<?php

namespace Modules\CMS\Support;

use Closure;
use Throwable;

/**
 * Live counters for home-page numbers. Modules register sources that count
 * only real (non-demo) records, e.g. in IdeaPortalServiceProvider:
 *   StatSources::register('ideas', 'Ideas received (live count)', fn () => IdeaSubmission::real()->count());
 */
class StatSources
{
    /** @var array<string, array{label: string, resolver: Closure}> */
    protected static array $sources = [];

    public static function register(string $key, string $label, Closure $resolver): void
    {
        static::$sources[$key] = ['label' => $label, 'resolver' => $resolver];
    }

    public static function resolve(string $key): ?int
    {
        if (! isset(static::$sources[$key])) {
            return null;
        }

        try {
            return (int) (static::$sources[$key]['resolver'])();
        } catch (Throwable) {
            return null;
        }
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return ['manual' => 'Entered by hand (must be verified)']
            + collect(static::$sources)->map(fn ($s) => $s['label'])->all();
    }
}
