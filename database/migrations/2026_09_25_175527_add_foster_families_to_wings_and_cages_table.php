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
        Schema::table('wings', function (Blueprint $table) {
            $table->boolean('is_foster')->default(false)->after('description');
        });

        Schema::table('cages', function (Blueprint $table) {
            $table->foreignId('volunteer_id')->nullable()->after('species_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('volunteer_id');
        });

        Schema::table('wings', function (Blueprint $table) {
            $table->dropColumn('is_foster');
        });
    }
};
