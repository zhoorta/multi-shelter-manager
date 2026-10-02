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
     * pets.notes duplicated pets.internal_notes (the field the PZ import
     * fills), so anything typed into it moves to the internal notes first.
     */
    public function up(): void
    {
        DB::table('pets')
            ->whereNotNull('notes')
            ->where('notes', '!=', '')
            ->orderBy('id')
            ->each(function (object $pet): void {
                DB::table('pets')->where('id', $pet->id)->update([
                    'internal_notes' => trim(($pet->internal_notes ?? '')."\n\n".$pet->notes),
                ]);
            });

        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('status');
        });
    }
};
