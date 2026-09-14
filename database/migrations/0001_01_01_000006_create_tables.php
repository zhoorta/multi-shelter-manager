<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        // 1. Facilities (Alas do Abrigo)
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelter_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });

        // 3. WINGS (Alas do Abrigo)
        Schema::create('wings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });

        // 4. CAGES (Gaiolas / Boxes)
        Schema::create('cages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wing_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->integer('capacity')->default(1);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });

        // 5. SPECIES (Espécies - Global lookup)
        Schema::create('species', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('name_plural')->unique();
            $table->boolean('has_pure_breed_field')->default(false);
            $table->timestamps();
        });

        // 6. BREEDS (Raças - Global lookup)
        Schema::create('breeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 7. COLORS (Cores - Global lookup)
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 8. FUR TYPES (Tipos de Pelo - Global lookup)
        Schema::create('fur_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 9. PETS (Animais)
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
            $table->enum('status', ['available', 'quarantine', 'adopted', 'medical', 'deceased'])->default('available');
            $table->text('notes')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_adoptable')->default(true);
            $table->boolean('is_sponsorable')->default(true);
            $table->boolean('publish_to_portal')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->date('checkin_date')->nullable();
            $table->date('checkout_date')->nullable();
            $table->text('internal_notes')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });

        // 10. PET IMAGES (Fotografias dos Animais)
        Schema::create('pet_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->boolean('is_main')->default(false);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });

        // 11. VACCINES (Vacinas)
        Schema::create('vaccines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });

        // 12. PIVOT: VACCINE_SPECIES
        Schema::create('vaccine_species', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vaccine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('species_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 13. SICKNESSES (Doenças)
        Schema::create('sicknesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });

        // 14. PIVOT: SICKNESS_SPECIES
        Schema::create('sickness_species', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sickness_id')->constrained()->cascadeOnDelete();
            $table->foreignId('species_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 15. PIVOT: PET_VACCINE
        Schema::create('pet_vaccines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vaccine_id')->constrained()->cascadeOnDelete();
            $table->date('administered_at');
            $table->date('expires_at')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });

        // 16. PIVOT: PET_SICKNESS
        Schema::create('pet_sicknesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sickness_id')->constrained()->cascadeOnDelete();
            $table->date('diagnosed_at');
            $table->enum('status', ['active', 'treated', 'chronic'])->default('active');
            $table->text('treatment_notes')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('adoptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->date('adoption_date');
            $table->date('return_date')->nullable();
            $table->decimal('adoption_fee', 6, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->enum('application_status', ['Pending', 'Approved', 'Rejected'])->default('Approved');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });

        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->boolean('send_feedback')->default(true);
            $table->boolean('send_newsletter')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });

        Schema::create('sponsorship_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsorship_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('payment_date');
            $table->decimal('payment_value', 6, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });

        Schema::create('shelter_species', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('species_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

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

        Schema::create('volunteer_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

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

    public function down(): void
    {
        Schema::dropIfExists('volunteer_availabilities');
        Schema::dropIfExists('volunteer_activities');
        Schema::dropIfExists('volunteers');
        Schema::dropIfExists('shelter_species');
        Schema::dropIfExists('sponsorship_payments');
        Schema::dropIfExists('sponsorships');
        Schema::dropIfExists('pet_sicknesses');
        Schema::dropIfExists('pet_vaccines');
        Schema::dropIfExists('sickness_species');
        Schema::dropIfExists('sicknesses');
        Schema::dropIfExists('vaccine_species');
        Schema::dropIfExists('vaccines');
        Schema::dropIfExists('pet_images');
        Schema::dropIfExists('pets');
        Schema::dropIfExists('fur_types');
        Schema::dropIfExists('colors');
        Schema::dropIfExists('breeds');
        Schema::dropIfExists('species');
        Schema::dropIfExists('cages');
        Schema::dropIfExists('wings');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('users');
        Schema::dropIfExists('shelters');
        Schema::dropIfExists('adoptions');

    }
};
