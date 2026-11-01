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
        Schema::table('pet_treatments', function (Blueprint $table) {
            $table->foreignId('treatment_id')->nullable()->change();
            $table->string('custom_name')->nullable()->after('treatment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pet_treatments', function (Blueprint $table) {
            $table->dropColumn('custom_name');
        });
    }
};
