<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;

class UsersWithRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin User',
                'email' => 'mo.mostafa@pikyhost.com',
                'password' => Hash::make('password'),
                'role' => UserRole::SuperAdmin,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'], // Ensure this is a plain string
                    'password' => $userData['password'],
                    'email_verified_at' => true,
                ]
            );


            // Assign role
            $role = Role::where('name', $userData['role']->value)->first();
            if ($role) {
                $user->assignRole($role);
            }
        }


        $this->command->info('Users with roles seeded successfully.');
    }
}
