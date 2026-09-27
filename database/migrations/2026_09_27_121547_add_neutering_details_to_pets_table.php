<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Existing neutered pets keep empty details (unknown). Pets still in the
     * shelter that aren't neutered start as pending.
     */
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->date('neutered_at')->nullable()->after('is_neutered');
            $table->boolean('neutered_by_shelter')->nullable()->after('neutered_at');
            $table->enum('neutering_status', ['pending', 'scheduled', 'not_recommended'])->nullable()->after('neutered_by_shelter');
            $table->date('neutering_scheduled_at')->nullable()->after('neutering_status');
            $table->string('neutering_notes')->nullable()->after('neutering_scheduled_at');
        });

        DB::table('pets')
            ->where('is_neutered', false)
            ->whereNotIn('status', ['adopted', 'deceased'])
            ->whereNull('date_of_death')
            ->update(['neutering_status' => 'pending']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn(['neutered_at', 'neutered_by_shelter', 'neutering_status', 'neutering_scheduled_at', 'neutering_notes']);
        });
    }
};
