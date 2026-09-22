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
        Schema::create('volunteers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelter_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('gender', ['male', 'female']);
            $table->string('id_card')->nullable();
            $table->string('tin')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('image_path')->nullable();
            $table->string('professional_activity')->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->enum('transport_mode', ['foot', 'bycicle', 'hitchhike', 'public transportation', 'own vehicule'])->nullable();
            $table->enum('attendance_evaluation', ['very low', 'low', 'regular', 'high', 'very high', 'excellent'])->nullable();
            $table->enum('performance_evaluation', ['very low', 'low', 'regular', 'high', 'very high', 'excellent'])->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('send_newsletter')->default(false);
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('volunteers');
    }
};
