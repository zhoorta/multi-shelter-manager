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
            $table->decimal('joining_fee', 8, 2)->default(0.00)->after('description');
            $table->decimal('membership_fee', 8, 2)->default(0.00)->after('joining_fee');
            $table->enum('membership_fee_frequency', ['monthly', 'quarterly', 'yearly'])->default('yearly')->after('membership_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shelters', function (Blueprint $table) {
            $table->dropColumn(['joining_fee', 'membership_fee', 'membership_fee_frequency']);
        });
    }
};
