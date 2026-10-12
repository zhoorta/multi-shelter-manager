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
        Schema::create('colonies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('species_id')->constrained('species');
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('city')->nullable();
            $table->string('caretaker_name')->nullable();
            $table->string('caretaker_phone')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colonies');
    }
};
