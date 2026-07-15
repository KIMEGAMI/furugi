<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@shinji.work');
        $password = env('ADMIN_PASSWORD');

        if (! is_string($password) || $password === '') {
            $this->command?->warn('ADMIN_PASSWORD is not set. Admin user was not created.');

            return;
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'subscription_plan' => User::PLAN_PREMIUM,
                'subscription_status' => 'active',
                'premium_started_at' => now(),
                'premium_ends_at' => now()->addYears(10),
                'is_admin' => true,
            ]
        );
    }
}
