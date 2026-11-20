<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Admin-managed content: articles (Insights), public ideas with supporter votes,
 * people (team, contributors, board) and "worked with" projects.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('name', 120);
            $table->string('kind', 20)->index(); // team | contributor | board
            $table->string('role', 120);
            $table->string('area', 30)->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true)->index();
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 160)->unique();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->string('category', 30)->index();
            $table->string('area', 30)->nullable()->index();
            $table->string('tags')->nullable();
            $table->text('summary');
            $table->longText('body');
            $table->string('cover_key', 80)->nullable();
            $table->string('cover_path')->nullable();
            $table->string('author_name', 120)->default('Delta Initiatives');
            $table->string('status_label', 60)->nullable();
            $table->unsignedSmallInteger('reading_minutes')->nullable();
            $table->string('sdgs', 60)->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        Schema::create('public_ideas', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 160)->unique();
            $table->string('code', 40)->index();
            $table->string('title');
            $table->string('area', 30)->index();
            $table->unsignedTinyInteger('stage')->default(0)->index();
            $table->text('problem');
            $table->text('solution');
            $table->text('how')->nullable();
            $table->string('sdgs', 60)->nullable();
            $table->string('fingerprint', 64);
            $table->timestamp('protected_at')->nullable();
            $table->string('cover_key', 80)->nullable();
            $table->string('cover_path')->nullable();
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        Schema::create('idea_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('public_idea_id')->constrained('public_ideas')->cascadeOnDelete();
            $table->string('visitor', 64);
            $table->timestamps();
            $table->unique(['public_idea_id', 'visitor']);
        });

        Schema::create('worked_with', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('client_name')->nullable();
            $table->string('summary', 500)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_confirmed')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worked_with');
        Schema::dropIfExists('idea_votes');
        Schema::dropIfExists('public_ideas');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('people');
    }
};
