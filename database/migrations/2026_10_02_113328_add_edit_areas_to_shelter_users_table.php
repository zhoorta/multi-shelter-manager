<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Null keeps today's behaviour: a staff member edits every area.
     */
    public function up(): void
    {
        Schema::table('shelter_users', function (Blueprint $table) {
            $table->json('edit_areas')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shelter_users', function (Blueprint $table) {
            $table->dropColumn('edit_areas');
        });
    }
};
