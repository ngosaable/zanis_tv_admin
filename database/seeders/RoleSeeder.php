<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full access to all systems and settings',
            ],
            [
                'name' => 'content_manager',
                'display_name' => 'Content Manager',
                'description' => 'Can manage videos, live channels, sliders, and ads',
            ],
            [
                'name' => 'subscriber',
                'display_name' => 'Subscriber',
                'description' => 'Basic subscription tier',
            ],
            [
                'name' => 'premium_subscriber',
                'display_name' => 'Premium Subscriber',
                'description' => 'Premium subscription tier with additional features',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                [
                    'display_name' => $role['display_name'],
                    'description' => $role['description'],
                ]
            );
        }
    }
}