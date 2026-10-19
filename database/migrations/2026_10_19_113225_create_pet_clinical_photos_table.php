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
        Schema::create('pet_clinical_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->date('taken_at');
            $table->text('notes')->nullable();
            $table->string('image_path');
            $table->string('thumb_path')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();

            $table->index(['pet_id', 'taken_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_clinical_photos');
    }
};
