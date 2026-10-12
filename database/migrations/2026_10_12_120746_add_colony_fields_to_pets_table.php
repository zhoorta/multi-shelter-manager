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
        Schema::table('pets', function (Blueprint $table) {
            $table->foreignId('colony_id')->nullable()->after('cage_id')->constrained('colonies')->nullOnDelete();
            $table->date('captured_at')->nullable()->after('colony_id');
            $table->date('returned_at')->nullable()->after('captured_at');
            $table->boolean('is_ear_tipped')->default(false)->after('returned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('colony_id');
            $table->dropColumn(['captured_at', 'returned_at', 'is_ear_tipped']);
        });
    }
};
