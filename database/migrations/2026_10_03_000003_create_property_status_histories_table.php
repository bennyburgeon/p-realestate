<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_status_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('subject');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('from_status_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->foreignId('to_status_id')->constrained('statuses')->restrictOnDelete();
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_status_histories');
    }
};
