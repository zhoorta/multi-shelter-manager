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
        Schema::table('shelters', function (Blueprint $table) {
            $table->json('modules')->nullable()->after('membership_fee_frequency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shelters', function (Blueprint $table) {
            $table->dropColumn('modules');
        });
    }
};
