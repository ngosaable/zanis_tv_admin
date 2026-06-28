<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-admin-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset or create admin user password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for existing users...');
        
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->info('No users found. Creating admin user...');
            
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
            
            $this->info('Admin user created successfully!');
            $this->info('Email: admin@example.com');
            $this->info('Password: admin123');
        } else {
            $this->info('Found existing users:');
            foreach ($users as $user) {
                $this->line('- ' . $user->email . ' (ID: ' . $user->id . ')');
            }
            
            $firstUser = $users->first();
            $firstUser->password = Hash::make('admin123');
            $firstUser->save();
            
            $this->info('Password reset for: ' . $firstUser->email);
            $this->info('New password: admin123');
        }
        
        $this->info('You can now login with these credentials.');
        $this->info('Don\'t forget to change the password after login!');
    }
}
