<?php

use App\Models\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the user account lifecycle status (Inactive until the phone OTP is verified).
     * The two user statuses are inserted here with their stable IDs so the foreign key
     * is always satisfiable; StatusSeeder keeps them in sync afterwards.
     */
    public function up(): void
    {
        $now = now();

        DB::table('statuses')->insertOrIgnore([
            ['id' => Status::USER_INACTIVE, 'group' => 'user', 'slug' => 'inactive', 'label' => 'Inactive', 'color' => 'gray', 'sort_order' => 27, 'created_at' => $now, 'updated_at' => $now],
            ['id' => Status::USER_ACTIVE, 'group' => 'user', 'slug' => 'active', 'label' => 'Active', 'color' => 'green', 'sort_order' => 28, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Existing users have all logged in via OTP or password already, so they start Active.
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('status_id')
                ->after('is_active')
                ->default(Status::USER_ACTIVE)
                ->constrained('statuses');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_id');
        });

        DB::table('statuses')->whereIn('id', [Status::USER_INACTIVE, Status::USER_ACTIVE])->delete();
    }
};
