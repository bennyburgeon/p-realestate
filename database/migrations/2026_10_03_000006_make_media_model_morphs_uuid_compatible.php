<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The stock Spatie Media Library migration uses morphs('model'), which
     * stores model_id as unsignedBigInteger — incompatible with Property's
     * UUID primary key (the only model using media today). No media rows
     * exist yet (upload was unwired until this feature), so it's safe to
     * drop and recreate as uuid-compatible.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropMorphs('model');
        });

        Schema::table('media', function (Blueprint $table) {
            $table->uuidMorphs('model');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropMorphs('model');
        });

        Schema::table('media', function (Blueprint $table) {
            $table->morphs('model');
        });
    }
};
