<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/* Honesty: every person seeded from the initial demo content is flagged is_demo so the site labels them "Sample". */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('people') || ! Schema::hasColumn('people', 'is_demo')) {
            return;
        }
        $data = require __DIR__.'/../data/content.php';
        $slugs = collect($data['people'] ?? [])->pluck('slug')->all();
        DB::table('people')->whereIn('slug', $slugs)->update(['is_demo' => true]);
    }

    public function down(): void
    {
    }
};
