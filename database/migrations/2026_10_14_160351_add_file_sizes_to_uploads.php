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
        Schema::table('pet_images', function (Blueprint $table) {
            $table->unsignedBigInteger('file_size')->nullable()->after('image_path');
        });

        Schema::table('volunteers', function (Blueprint $table) {
            $table->unsignedBigInteger('image_size')->nullable()->after('image_path');
        });

        Schema::table('shelters', function (Blueprint $table) {
            $table->unsignedBigInteger('logo_size')->nullable()->after('logo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pet_images', function (Blueprint $table) {
            $table->dropColumn('file_size');
        });

        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropColumn('image_size');
        });

        Schema::table('shelters', function (Blueprint $table) {
            $table->dropColumn('logo_size');
        });
    }
};
