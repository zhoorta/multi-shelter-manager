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
        Schema::table('shelter_users', function (Blueprint $table) {
            $table->boolean('adoption_application_notifications')->default(false)->after('vaccination_notifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shelter_users', function (Blueprint $table) {
            $table->dropColumn('adoption_application_notifications');
        });
    }
};
