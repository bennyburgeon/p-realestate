<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requirement_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('property_requirement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('responder_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('property_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('status_id')->constrained();
            $table->text('message')->nullable();
            $table->timestamps();

            $table->unique(['property_requirement_id', 'responder_id', 'property_id'], 'requirement_response_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requirement_responses');
    }
};
