<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (['shelters', 'members'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->enum('membership_fee_frequency', ['monthly', 'quarterly', 'semiannual', 'yearly'])->default('yearly')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['shelters', 'members'] as $tableName) {
            DB::table($tableName)->where('membership_fee_frequency', 'semiannual')->update(['membership_fee_frequency' => 'yearly']);

            Schema::table($tableName, function (Blueprint $table) {
                $table->enum('membership_fee_frequency', ['monthly', 'quarterly', 'yearly'])->default('yearly')->change();
            });
        }
    }
};
