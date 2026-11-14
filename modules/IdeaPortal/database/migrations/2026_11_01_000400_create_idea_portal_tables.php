<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per year; locked while the next Idea ID is allocated.
        Schema::create('idea_sequences', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedInteger('last_number')->default(0);
        });

        Schema::create('idea_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->uuid('uuid')->unique();
            $table->string('status', 30)->default('received')->index();

            $table->string('title');
            $table->string('summary', 500);
            $table->string('focus_area_slug')->nullable()->index();
            $table->string('focus_area_name')->nullable();

            $table->string('lead_name');
            $table->string('lead_email')->index();
            $table->string('lead_phone', 40)->nullable();
            $table->string('institution');
            $table->string('team_name')->nullable();
            $table->unsignedTinyInteger('team_size')->default(1);
            $table->text('team_members')->nullable();

            $table->json('answers');
            $table->string('locale', 5)->default('en');

            // Proof of submission. DATETIME (not TIMESTAMP) so the value never auto-updates.
            $table->string('agreement_version', 20);
            $table->dateTime('consented_at', 6);
            $table->dateTime('submitted_at', 6)->index();
            $table->char('fingerprint', 64)->index();
            $table->longText('fingerprint_payload');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->decimal('average_score', 4, 2)->nullable();
            $table->unsignedSmallInteger('scores_count')->default(0);

            $table->dateTime('receipt_sent_at', 6)->nullable();
            $table->dateTime('team_notified_at', 6)->nullable();

            $table->boolean('is_demo')->default(false)->index();
            $table->timestamps(6);
        });

        Schema::create('idea_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idea_submission_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('stored_path');
            $table->string('mime', 120)->nullable();
            $table->unsignedBigInteger('size');
            $table->char('sha256', 64);
            $table->timestamps();
        });

        Schema::create('idea_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idea_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('scores');
            $table->decimal('average', 4, 2);
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['idea_submission_id', 'user_id']);
        });

        Schema::create('idea_status_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idea_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->string('note', 1000)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idea_status_changes');
        Schema::dropIfExists('idea_scores');
        Schema::dropIfExists('idea_files');
        Schema::dropIfExists('idea_submissions');
        Schema::dropIfExists('idea_sequences');
    }
};
