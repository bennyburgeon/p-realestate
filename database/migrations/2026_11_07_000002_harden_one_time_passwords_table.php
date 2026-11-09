<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Turns one_time_passwords from one plaintext row per phone into a per-user history
     * of hashed, single-use codes that can be individually invalidated.
     */
    public function up(): void
    {
        // Existing rows hold plaintext codes valid for at most a few minutes; drop them.
        DB::table('one_time_passwords')->delete();

        Schema::table('one_time_passwords', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->dropColumn('code');
        });

        Schema::table('one_time_passwords', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            $table->string('code_hash')->after('phone');
            $table->timestamp('invalidated_at')->nullable()->after('consumed_at');
            $table->string('ip_address', 45)->nullable()->after('invalidated_at');

            $table->index(['user_id', 'consumed_at', 'invalidated_at']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        DB::table('one_time_passwords')->delete();

        Schema::table('one_time_passwords', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'consumed_at', 'invalidated_at']);
            $table->dropIndex(['phone']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['code_hash', 'invalidated_at', 'ip_address']);
        });

        Schema::table('one_time_passwords', function (Blueprint $table) {
            $table->string('code')->after('phone');
            $table->unique('phone');
        });
    }
};
