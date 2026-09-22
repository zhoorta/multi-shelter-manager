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
        Schema::create('volunteer_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_index');
            $table->enum('frequency', ['occasionally', 'biweekly', 'weekly'])->default('occasionally');
            $table->boolean('mornings')->default(false);
            $table->boolean('afternoons')->default(false);
            $table->timestamps();
            $table->unique(['volunteer_id', 'day_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_availabilities');
    }
};
