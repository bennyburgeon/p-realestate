<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('enquiry_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scheduled_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->string('status')->default('scheduled'); // scheduled|completed|cancelled|no_show
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['property_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
