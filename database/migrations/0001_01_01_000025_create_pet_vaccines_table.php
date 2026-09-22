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
        Schema::create('pet_vaccines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vaccine_id')->constrained()->cascadeOnDelete();
            $table->date('administered_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('lot_number')->nullable();
            $table->string('veterinarian_name')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'administered', 'canceled'])->default('scheduled');
            $table->timestamp('notification_date')->nullable();
            $table->text('notification_recipients')->nullable();
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
        Schema::dropIfExists('pet_vaccines');
    }
};
