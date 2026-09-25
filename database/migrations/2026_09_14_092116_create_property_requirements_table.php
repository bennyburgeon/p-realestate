<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_requirements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('property_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('city_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('status_id')->constrained();

            $table->string('reference_number')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('intent')->index(); // buy | rent | lease
            $table->string('property_nature')->index(); // residential | commercial

            $table->json('preferred_locality_ids')->nullable();
            $table->decimal('budget_min', 14, 2)->nullable();
            $table->decimal('budget_max', 14, 2)->nullable();
            $table->decimal('area_min', 12, 2)->nullable();
            $table->decimal('area_max', 12, 2)->nullable();
            $table->string('area_unit')->default('sqft');

            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->string('furnishing_status')->nullable();
            $table->boolean('parking_required')->nullable();
            $table->string('floor_preference')->nullable();
            $table->string('facing_preference')->nullable();
            $table->string('property_age')->nullable();
            $table->string('possession_requirement')->nullable();
            $table->date('move_in_date')->nullable();
            $table->json('amenity_ids')->nullable();
            $table->text('special_requirements')->nullable();
            $table->text('additional_notes')->nullable();

            $table->string('contact_name');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone');
            $table->string('preferred_contact_method')->nullable();
            $table->string('preferred_contact_time')->nullable();

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['intent', 'property_nature', 'status_id']);
            $table->index(['budget_min', 'budget_max']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_requirements');
    }
};
