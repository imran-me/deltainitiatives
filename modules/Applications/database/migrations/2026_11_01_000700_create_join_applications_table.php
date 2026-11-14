<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('join_applications', function (Blueprint $table) {
            $table->id();
            $table->string('role', 20)->index();             // member | fellow | ambassador | volunteer
            $table->string('name', 120);
            $table->string('email')->index();
            $table->string('phone', 30);
            $table->string('institution', 160);
            $table->string('study_level', 30);
            $table->string('department', 120)->nullable();
            $table->string('city', 80);
            $table->string('linkedin_url')->nullable();
            $table->text('skills');
            $table->text('motivation');
            $table->string('availability', 10);
            $table->string('cv_path')->nullable();           // private "local" disk: applications/{uuid}.pdf
            $table->string('locale', 5)->default('en');
            $table->timestamp('consent_at')->nullable();
            $table->string('status', 20)->default('new')->index(); // new | reviewing | accepted | declined
            $table->text('admin_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_demo')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('join_applications');
    }
};
