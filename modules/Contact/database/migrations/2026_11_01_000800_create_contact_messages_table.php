<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email')->index();
            $table->string('phone', 30)->nullable();
            $table->string('organization', 160)->nullable();
            $table->string('topic', 20)->default('general')->index(); // general | ideas | partnership | media | events | other
            $table->string('subject', 160);
            $table->text('message');
            $table->string('status', 20)->default('new')->index(); // new | read | replied | archived
            $table->text('admin_notes')->nullable();
            $table->string('locale', 5)->default('en');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->boolean('is_demo')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
