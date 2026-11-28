<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Round 15: the founder in both people systems, and the EON summit post at the top of the feed.
 * Idempotent; deploy runs migrations only.
 */
return new class extends Migration
{
    private const BIO = 'Founder of Delta Initiatives. Head of AI, Strategy & Research at Epal Group. Works across AI & IoT, technology and business strategy, research and venture development, with a long record of civic and social work in Bangladesh.';

    private const PHOTO = 'images/people/md-imran-hossain.webp';

    public function up(): void
    {
        $now = now();

        if (Schema::hasTable('people') && ! DB::table('people')->where('slug', 'md-imran-hossain')->exists()) {
            DB::table('people')->where('kind', 'team')->increment('sort_order');
            DB::table('people')->insert([
                'slug' => 'md-imran-hossain', 'name' => 'Md Imran Hossain', 'kind' => 'team', 'role' => 'Founder', 'area' => null,
                'bio' => self::BIO, 'photo_path' => self::PHOTO, 'sort_order' => 0, 'is_published' => true, 'is_demo' => false,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('team_members') && ! DB::table('team_members')->where('name', 'Md Imran Hossain')->exists()) {
            DB::table('team_members')->where('group', 'team')->increment('sort_order');
            DB::table('team_members')->insert([
                'name' => 'Md Imran Hossain', 'role' => 'Founder', 'group' => 'team', 'bio' => self::BIO, 'photo_path' => self::PHOTO,
                'linkedin_url' => 'https://www.linkedin.com/in/msgimran/', 'sort_order' => 0, 'is_published' => true, 'is_demo' => false,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('feed_posts') && ! DB::table('feed_posts')->where('link_url', '/eon')->where('is_demo', false)->exists()) {
            DB::table('feed_posts')->insert([
                'body' => "EON, Delta Initiatives' first project, is a Top 20 finalist at the Bangladesh AI Business Summit 2026: an offline, AI-powered business intelligence assistant with its own independent reasoning layer (no outbound API calls), speaking Bangla, English and Banglish.\n\nইওন, ডেল্টা ইনিশিয়েটিভসের প্রথম প্রকল্প, বাংলাদেশ এআই বিজনেস সামিট ২০২৬-এর সেরা ২০ ফাইনালিস্ট: একটি অফলাইন, এআই-চালিত বিজনেস ইন্টেলিজেন্স সহকারী, যার নিজস্ব স্বাধীন যুক্তি-স্তর আছে (বাইরের কোনো API কল নেই), আর কথা বলে বাংলা, ইংরেজি ও বাংলিশে।",
                'image_path' => 'images/eon/eon-summit-booth.webp',
                'link_url' => '/eon', 'link_title' => 'EON: Top 20 finalist, Bangladesh AI Business Summit 2026',
                'published_at' => $now, 'is_published' => true, 'is_demo' => false,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('people')) {
            DB::table('people')->where('slug', 'md-imran-hossain')->delete();
        }
        if (Schema::hasTable('team_members')) {
            DB::table('team_members')->where('name', 'Md Imran Hossain')->delete();
        }
        if (Schema::hasTable('feed_posts')) {
            DB::table('feed_posts')->where('link_url', '/eon')->where('is_demo', false)->delete();
        }
    }
};
