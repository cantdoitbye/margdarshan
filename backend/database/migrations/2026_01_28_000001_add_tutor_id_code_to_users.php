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
        Schema::table('users', function (Blueprint $table) {
            $table->string('tutor_id_code', 20)->unique()->nullable()->after('status');
            $table->timestamp('qr_code_generated_at')->nullable()->after('tutor_id_code');
            $table->integer('qr_access_count')->default(0)->after('qr_code_generated_at');
            $table->timestamp('qr_last_accessed_at')->nullable()->after('qr_access_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tutor_id_code',
                'qr_code_generated_at',
                'qr_access_count',
                'qr_last_accessed_at'
            ]);
        });
    }
};
