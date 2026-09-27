<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Add the public URL slug and fill it for the existing shelters (soft-deleted
     * ones included, so their old links stay reserved): the name, then the name
     * and city, then a number on a clash.
     */
    public function up(): void
    {
        Schema::table('shelters', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('short_name');
        });

        $taken = [];

        foreach (DB::table('shelters')->orderBy('id')->get(['id', 'name', 'city']) as $shelter) {
            $base = Str::slug($shelter->name) ?: 'shelter';
            $candidates = [$base, Str::slug($shelter->name.' '.$shelter->city)];
            $slug = collect($candidates)->first(fn (string $candidate): bool => ! in_array($candidate, $taken, true));

            for ($suffix = 2; $slug === null; $suffix++) {
                $slug = in_array($candidates[1].'-'.$suffix, $taken, true) ? null : $candidates[1].'-'.$suffix;
            }

            $taken[] = $slug;
            DB::table('shelters')->where('id', $shelter->id)->update(['slug' => $slug]);
        }

        Schema::table('shelters', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shelters', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
