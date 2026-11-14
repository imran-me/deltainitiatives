<?php

namespace Modules\IdeaPortal\Services;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Allocates sequential Idea IDs: DI-2026-0001, DI-2026-0002, …
 * The yearly counter row is locked (SELECT … FOR UPDATE) inside a transaction,
 * so two simultaneous submissions can never receive the same number.
 * The year follows Bangladesh time; the counter restarts each year.
 */
class IdeaIdGenerator
{
    public function next(?DateTimeInterface $at = null): string
    {
        $year = (int) Carbon::instance($at ?? now())
            ->setTimezone(config('app.display_timezone', 'Asia/Dhaka'))
            ->format('Y');

        $number = DB::transaction(function () use ($year) {
            DB::table('idea_sequences')->insertOrIgnore(['year' => $year, 'last_number' => 0]);

            $row = DB::table('idea_sequences')->where('year', $year)->lockForUpdate()->first();
            $next = (int) $row->last_number + 1;

            DB::table('idea_sequences')->where('year', $year)->update(['last_number' => $next]);

            return $next;
        });

        return $this->format($year, $number);
    }

    public function format(int $year, int $number): string
    {
        return sprintf('%s-%d-%04d', config('ideaportal.id_prefix', 'DI'), $year, $number);
    }

    /**
     * IDs for seeded sample submissions (DI-SAMPLE-0001). They never consume the
     * real sequence, so the first real idea is always DI-{year}-0001.
     */
    public function sample(int $number): string
    {
        return sprintf('%s-SAMPLE-%04d', config('ideaportal.id_prefix', 'DI'), $number);
    }
}
