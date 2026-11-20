<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Publishing: the institute feed (posts, reactions, moderated comments) and the
 * Services forms (proposal requests, Delta Expert Network applications).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_posts', function (Blueprint $table) {
            $table->id();
            $table->text('body');
            $table->string('image_key', 80)->nullable();
            $table->string('image_path')->nullable();
            $table->string('link_url', 500)->nullable();
            $table->string('link_title')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        Schema::create('feed_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_post_id')->constrained('feed_posts')->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('visitor', 64);
            $table->timestamps();
            $table->unique(['feed_post_id', 'type', 'visitor']);
        });

        Schema::create('feed_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_post_id')->constrained('feed_posts')->cascadeOnDelete();
            $table->string('name', 80);
            $table->text('body');
            $table->string('status', 20)->default('pending')->index();
            $table->string('visitor', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('service', 40)->index();
            $table->string('name', 120);
            $table->string('organization', 160);
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('budget', 60)->nullable();
            $table->text('message');
            $table->string('status', 20)->default('new')->index();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('expert_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email');
            $table->string('field', 80);
            $table->string('institution', 160)->nullable();
            $table->text('expertise');
            $table->string('profile_url', 500)->nullable();
            $table->string('status', 20)->default('new')->index();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expert_applications');
        Schema::dropIfExists('service_requests');
        Schema::dropIfExists('feed_comments');
        Schema::dropIfExists('feed_reactions');
        Schema::dropIfExists('feed_posts');
    }
};
