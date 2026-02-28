<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tutor_profiles', function (Blueprint $table) {
            // Make subjects and classes nullable for activity tutors
            $table->json('subjects')->nullable()->change();
            $table->json('classes')->nullable()->change();
            
            // Add new fields
            $table->string('demo_video_path')->nullable()->after('is_profile_complete');
            $table->json('activity_skills')->nullable()->after('demo_video_path');
        });
        
        // Add tutor_type enum using raw SQL
        DB::statement("ALTER TABLE tutor_profiles ADD COLUMN tutor_type ENUM('academic', 'activity') DEFAULT 'academic' AFTER user_id");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutor_profiles', function (Blueprint $table) {
            $table->dropColumn(['tutor_type', 'demo_video_path', 'activity_skills']);
        });
    }
};
