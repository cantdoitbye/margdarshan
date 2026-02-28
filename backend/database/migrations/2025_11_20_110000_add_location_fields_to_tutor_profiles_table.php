<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tutor_profiles', function (Blueprint $table) {
            $table->string('service_location')->nullable()->after('timezone');
            $table->decimal('service_latitude', 10, 8)->nullable()->after('service_location');
            $table->decimal('service_longitude', 11, 8)->nullable()->after('service_latitude');
            $table->integer('service_radius_km')->default(10)->after('service_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutor_profiles', function (Blueprint $table) {
            $table->dropColumn(['service_location', 'service_latitude', 'service_longitude', 'service_radius_km']);
        });
    }
};
