<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Editable copy blocks: home.hero, home.story, about.vision, ...
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('eyebrow')->nullable();
            $table->string('title', 500)->nullable();
            $table->text('body')->nullable();
            $table->string('help')->nullable();
            $table->timestamps();
        });

        // Delta Labs, Delta Policy, Delta Challenge.
        Schema::create('arms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('summary');
            $table->text('body')->nullable();
            $table->text('highlights')->nullable();
            $table->string('icon', 60)->default('circle-dot');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Six-step Idea-to-Impact pipeline.
        Schema::create('pipeline_steps', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('summary');
            $table->string('icon', 60)->default('circle-dot');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('focus_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon', 60)->default('circle-dot');
            $table->text('summary');
            $table->text('examples')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('core_values', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('summary');
            $table->string('icon', 60)->default('circle-dot');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Team and Advisory Board.
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->string('group', 20)->default('team')->index();
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('linkedin_url', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->boolean('is_demo')->default(false)->index();
            $table->timestamps();
        });

        // "In collaboration with" — logos shown only with written permission.
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->string('website_url', 500)->nullable();
            $table->boolean('permission_confirmed')->default(false);
            $table->string('permission_note', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->boolean('is_demo')->default(false)->index();
            $table->timestamps();
        });

        // Headline numbers — shown only when verified and greater than zero.
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('source', 40)->default('manual');
            $table->unsignedInteger('value')->default(0);
            $table->string('suffix', 10)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('verified_note', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('legal_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('summary', 500)->nullable();
            $table->longText('body');
            $table->string('version', 20)->default('1.0');
            $table->date('effective_date')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['legal_pages', 'stats', 'partners', 'team_members', 'core_values', 'focus_areas', 'pipeline_steps', 'arms', 'content_blocks'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
