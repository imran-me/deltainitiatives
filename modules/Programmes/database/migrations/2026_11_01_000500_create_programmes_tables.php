<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Programme types: Delta Challenge, Hackathon, Dialogue, Fellows, Talks, Demo Day.
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('summary');
            $table->text('body')->nullable();
            $table->string('icon', 60)->default('circle-dot');
            $table->string('cadence', 60)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->text('body')->nullable();
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->nullable();
            $table->string('venue')->nullable();
            $table->string('city', 120)->nullable();
            $table->string('mode', 20)->default('in_person'); // in_person | online | hybrid
            $table->string('online_note', 500)->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->boolean('registration_open')->default(true);
            $table->dateTime('registration_deadline')->nullable();
            $table->string('cover_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_demo')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('institution')->nullable();
            $table->string('occupation', 30)->default('student'); // student | researcher | professional | other
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('registered')->index(); // registered | waitlisted | cancelled | attended
            $table->string('locale', 5)->default('en');
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_demo')->default(false)->index();
            $table->timestamps();

            $table->unique(['event_id', 'email']);
        });

        // Past-events gallery (real photos only).
        Schema::create('event_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_photos');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('events');
        Schema::dropIfExists('programmes');
    }
};
