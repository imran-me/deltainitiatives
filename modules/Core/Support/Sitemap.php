<?php

namespace Modules\Core\Support;

use Closure;
use Throwable;

/**
 * Modules contribute URLs to /sitemap.xml from their service providers:
 *   app(Sitemap::class)->add(fn () => [['loc' => route('about'), 'priority' => 0.8]]);
 */
class Sitemap
{
    /** @var array<int, Closure> */
    protected array $sources = [];

    public function add(Closure $source): void
    {
        $this->sources[] = $source;
    }

    /** @return array<int, array{loc: string, lastmod?: string|null, changefreq?: string, priority?: float}> */
    public function entries(): array
    {
        $entries = [];

        foreach ($this->sources as $source) {
            try {
                foreach ($source() as $entry) {
                    $entries[$entry['loc']] = $entry;
                }
            } catch (Throwable $e) {
                report($e);
            }
        }

        return array_values($entries);
    }
}
