<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('address_visibility')->default(true)->after('longitude');
            $table->decimal('price_per_sqft', 12, 2)->nullable()->after('is_negotiable');
            $table->decimal('min_acceptable_price', 14, 2)->nullable()->after('price_per_sqft');

            $table->string('agent_name')->nullable()->after('contact_email');
            $table->string('agent_phone')->nullable()->after('agent_name');
            $table->string('agent_email')->nullable()->after('agent_phone');
            $table->string('agency_name')->nullable()->after('agent_email');
            $table->string('agent_license_number')->nullable()->after('agency_name');

            $table->timestamp('sold_at')->nullable()->after('published_at');
            $table->decimal('sold_price', 14, 2)->nullable()->after('sold_at');
            $table->string('buyer_source')->nullable()->after('sold_price');
            $table->text('sold_notes')->nullable()->after('buyer_source');

            $table->index(['sold_at']);
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['sold_at']);
            $table->dropColumn([
                'address_visibility', 'price_per_sqft', 'min_acceptable_price',
                'agent_name', 'agent_phone', 'agent_email', 'agency_name', 'agent_license_number',
                'sold_at', 'sold_price', 'buyer_source', 'sold_notes',
            ]);
        });
    }
};
