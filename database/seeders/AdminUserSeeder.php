<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Creates the first Super Admin account.
     * Credentials come from ADMIN_NAME / ADMIN_EMAIL / ADMIN_PASSWORD in .env.
     * Running it again will NOT overwrite an existing user's password.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@zanistv.com');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Zanis Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'ChangeMe123!')),
                'role' => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info("Admin login: {$user->email}" . ($user->wasRecentlyCreated ? ' (created)' : ' (already existed)'));
    }
}
