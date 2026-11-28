<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Round 14 content facts (Nov 2026): positioning, contact and socials in settings,
 * the Delta Build arm, and the EON summit news entry. Idempotent; deploy runs migrations only.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        if (Schema::hasTable('settings')) {
            $values = [
                'site_descriptor' => 'A Youth-led Think Tank, Research & Venture Studio',
                'site_description' => 'Delta Initiatives is a youth-led think tank, research and venture studio from Bangladesh. We research problems, then engineer the solutions.',
                'contact_email' => 'deltainitiativesbd@gmail.com',
                'ideas_email' => 'deltainitiativesbd@gmail.com',
                'partners_email' => 'deltainitiativesbd@gmail.com',
                'contact_phone' => '+8801641606561',
                'contact_address' => 'Uttara Sector 10, Dhaka, Bangladesh',
                'social_facebook' => 'https://www.facebook.com/deltainitiatives',
                'social_linkedin' => 'https://www.linkedin.com/company/delta_initiatives',
            ];
            foreach ($values as $key => $value) {
                DB::table('settings')->updateOrInsert(['key' => $key], ['value' => $value, 'updated_at' => $now, 'created_at' => $now]);
            }
            Cache::forget('delta.settings');
        }

        foreach (['legal_pages' => 'body', 'content_blocks' => 'body'] as $table => $col) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $col)) {
                foreach (['hello', 'ideas', 'partners'] as $box) {
                    DB::table($table)->where($col, 'like', '%'.$box.'@deltainitiatives.com%')
                        ->update([$col => DB::raw("REPLACE($col, '$box@deltainitiatives.com', 'deltainitiativesbd@gmail.com')")]);
                }
            }
        }

        if (Schema::hasTable('arms') && ! DB::table('arms')->where('slug', 'delta-build')->exists()) {
            DB::table('arms')->where('sort_order', '>=', 2)->increment('sort_order');
            DB::table('arms')->insert([
                'name' => 'Delta Build', 'slug' => 'delta-build', 'tagline' => 'Research to working product', 'icon' => 'cpu',
                'summary' => 'We engineer AI-first, research-backed solutions with organisations, from first idea to working product.',
                'body' => "Delta Build is where research becomes a working product. We work with organisations on AI-first, research-backed solutions: we study the problem first, then engineer the solution, from first idea to working product.\n\nEON, our offline AI-powered BI assistant, is built this way.",
                'highlights' => "Research before code\nAI-first, offline-capable design\nFrom first idea to working product",
                'sort_order' => 2, 'is_published' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('articles') && ! DB::table('articles')->where('slug', 'eon-top-20-bangladesh-ai-business-summit-2026')->exists()) {
            DB::table('articles')->insert([
                'slug' => 'eon-top-20-bangladesh-ai-business-summit-2026',
                'title' => 'EON named a Top 20 finalist at the Bangladesh AI Business Summit 2026',
                'title_bn' => 'বাংলাদেশ এআই বিজনেস সামিট ২০২৬-এ সেরা ২০ ফাইনালিস্ট ইওন',
                'category' => 'explainer', 'area' => 'ai', 'tags' => 'EON, AI, news',
                'summary' => 'Team EON was selected among the Top 20 finalists and showcased EON, our offline AI-powered BI assistant, at the Bangladesh AI Business Summit 2026 in Dhaka.',
                'body' => "In November 2026, Team EON was selected among the **Top 20 finalists** at the Bangladesh AI Business Summit 2026 and showcased EON to visitors at the summit in Dhaka.\n\n## What EON is\n\nEON is an offline, AI-powered BI assistant that sits on top of a business's ERP. Owners ask questions in Bangla, English or Banglish, and EON answers with the numbers, the reason and a recommended action.\n\n## Why offline matters\n\nEON has its own independent reasoning layer and makes **no outbound API calls**. Business data stays on site, and the assistant keeps working without an internet connection.\n\n## What comes next\n\nEON is in the validation stage with real businesses. Read more on the [EON page](/eon) or ask to join the pilot.",
                'author_name' => 'Delta Initiatives', 'status_label' => 'News', 'reading_minutes' => 2,
                'published_at' => $now, 'is_published' => true, 'is_demo' => false,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('articles')) {
            DB::table('articles')->where('slug', 'eon-top-20-bangladesh-ai-business-summit-2026')->delete();
        }
        if (Schema::hasTable('arms')) {
            DB::table('arms')->where('slug', 'delta-build')->delete();
        }
    }
};
