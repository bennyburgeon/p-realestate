<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('property_category_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('location_id')->constrained()->restrictOnDelete();
            $table->foreignId('status_id')->constrained();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('listing_type')->index(); // sale | rent | lease
            $table->longText('description')->nullable();

            $table->decimal('price', 14, 2)->nullable();
            $table->decimal('rent_amount', 14, 2)->nullable();
            $table->decimal('security_deposit', 14, 2)->nullable();
            $table->decimal('maintenance_amount', 14, 2)->nullable();
            $table->boolean('is_negotiable')->default(false);

            $table->decimal('built_up_area', 12, 2)->nullable();
            $table->decimal('carpet_area', 12, 2)->nullable();
            $table->decimal('plot_area', 12, 2)->nullable();
            $table->string('area_unit')->default('sqft');

            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->unsignedTinyInteger('balconies')->nullable();
            $table->unsignedSmallInteger('floor_number')->nullable();
            $table->unsignedSmallInteger('total_floors')->nullable();

            $table->string('furnishing_status')->nullable(); // unfurnished | semi_furnished | furnished
            $table->string('parking_details')->nullable();
            $table->string('facing')->nullable();
            $table->unsignedSmallInteger('construction_year')->nullable();
            $table->string('possession_status')->nullable(); // ready_to_move | under_construction
            $table->date('availability_date')->nullable();

            $table->text('address')->nullable();
            $table->string('landmark')->nullable();
            $table->string('pin_code')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('contact_preference')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();

            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['listing_type', 'status_id']);
            $table->index(['price']);
            $table->index(['bedrooms']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
