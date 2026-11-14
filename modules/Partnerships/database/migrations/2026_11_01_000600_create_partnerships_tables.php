<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partnership_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->text('benefits')->nullable();
            $table->string('icon', 60)->default('handshake');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('partner_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('organization');
            $table->string('contact_name');
            $table->string('job_title')->nullable();
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('country', 80)->default('Bangladesh');
            $table->foreignId('partnership_type_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message');
            $table->string('status', 20)->default('new')->index(); // new | in_conversation | closed
            $table->text('admin_notes')->nullable();
            $table->string('locale', 5)->default('en');
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_demo')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_inquiries');
        Schema::dropIfExists('partnership_types');
    }
};
