<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_matches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('property_requirement_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('property_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->string('match_band')->index(); // excellent | good | possible
            $table->boolean('is_hidden')->default(false);
            $table->timestamp('computed_at');
            $table->timestamps();

            $table->unique(['property_requirement_id', 'property_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_matches');
    }
};
