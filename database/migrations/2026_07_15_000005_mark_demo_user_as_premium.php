<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const DEMO_EMAIL = 'user@shinji.work';

    public function up(): void
    {
        DB::table('users')
            ->where('email', self::DEMO_EMAIL)
            ->update([
                'subscription_plan' => User::PLAN_PREMIUM,
                'subscription_status' => 'active',
                'premium_started_at' => now(),
                'premium_ends_at' => now()->addYears(10),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        //
    }
};
