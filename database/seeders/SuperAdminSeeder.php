<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 'super admin' role if it doesn't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'super admin']);

        // Create super admin user
        $user = User::firstOrCreate(
            ['email' => 'superadmin@mediconnect.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // You should change this password in production
                'email_verified_at' => now(),
            ]
        );

        // Assign 'super admin' role to the user
        $user->assignRole($superAdminRole->name);

        $this->command->info('Super Admin user created/updated successfully!');
    }
}
