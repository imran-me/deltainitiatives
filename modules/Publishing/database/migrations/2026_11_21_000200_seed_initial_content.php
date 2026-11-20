<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Moves the content of the former static pages (7 articles, 11 ideas, people)
 * into the database. Runs once; each table is filled only when empty.
 */
return new class extends Migration
{
    public function up(): void
    {
        $data = require __DIR__.'/../data/content.php';
        $now = now();

        if (Schema::hasTable('people') && ! DB::table('people')->exists()) {
            foreach ($data['people'] as $p) {
                DB::table('people')->insert([
                    'slug' => $p['slug'], 'name' => $p['name'], 'kind' => $p['kind'], 'role' => $p['role'], 'area' => $p['area'],
                    'bio' => $p['bio'], 'sort_order' => $p['sort_order'], 'is_published' => true, 'is_demo' => $p['is_demo'],
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        if (Schema::hasTable('articles') && ! DB::table('articles')->exists()) {
            foreach ($data['articles'] as $i => $a) {
                DB::table('articles')->insert([
                    'slug' => $a['slug'], 'title' => $a['title'], 'title_bn' => $a['title_bn'], 'category' => $a['category'], 'area' => $a['area'],
                    'tags' => $a['tags'], 'summary' => $a['summary'], 'body' => $a['body'], 'cover_key' => $a['cover_key'],
                    'status_label' => $a['status_label'], 'reading_minutes' => $a['reading_minutes'], 'sdgs' => $a['sdgs'],
                    'published_at' => $now->copy()->subDays($i), 'is_published' => true, 'is_demo' => true,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        if (Schema::hasTable('public_ideas') && ! DB::table('public_ideas')->exists()) {
            $people = DB::table('people')->pluck('id', 'name');
            foreach ($data['ideas'] as $idea) {
                DB::table('public_ideas')->insert([
                    'slug' => $idea['slug'], 'code' => $idea['code'], 'title' => $idea['title'], 'area' => $idea['area'], 'stage' => $idea['stage'],
                    'problem' => $idea['problem'], 'solution' => $idea['solution'], 'how' => $idea['how'], 'sdgs' => $idea['sdgs'],
                    'fingerprint' => $idea['fingerprint'], 'protected_at' => $idea['protected_at'], 'cover_key' => $idea['cover_key'],
                    'person_id' => $idea['initiator'] ? ($people[$idea['initiator']] ?? null) : null,
                    'is_featured' => $idea['is_featured'], 'is_published' => true, 'is_demo' => $idea['is_demo'],
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        if (Schema::hasTable('worked_with') && ! DB::table('worked_with')->exists()) {
            DB::table('worked_with')->insert([
                'title' => 'EON', 'client_name' => null, 'summary' => 'An executive AI layer built on top of a running multi-company ERP.',
                'url' => '/initiatives/eon', 'is_confirmed' => true, 'sort_order' => 0, 'is_published' => true,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Content tables are dropped by the create migration's down().
    }
};
