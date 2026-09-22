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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cage_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('species_id')->constrained();
            $table->foreignId('breed_id')->constrained();
            $table->foreignId('primary_color_id')->nullable()->constrained('colors');
            $table->foreignId('secondary_color_id')->nullable()->constrained('colors');
            $table->foreignId('fur_type_id')->nullable()->constrained('fur_types');
            $table->foreignId('size_id')->nullable()->constrained('sizes');
            $table->boolean('is_pure_breed')->default(false);
            $table->string('ref');
            $table->string('name');
            $table->string('chip')->nullable();
            $table->boolean('is_neutered')->default(false);
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date')->nullable();
            $table->date('date_of_death')->nullable();
            $table->enum('status', ['available', 'not_available', 'adopted', 'deceased'])->default('available');
            $table->text('notes')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_adoptable')->default(true);
            $table->boolean('is_sponsorable')->default(true);
            $table->boolean('publish_to_portal')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->date('checkin_date')->nullable();
            $table->date('checkout_date')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->integer('view_count')->default(0);
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
        Schema::dropIfExists('pets');
    }
};
