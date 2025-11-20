<?php

namespace Database\Seeders;

use App\Domain\Users\Models\Role;
use App\Domain\Users\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrator', 'slug' => 'admin'],
            ['name' => 'Veterinarian', 'slug' => 'vet'],
            ['name' => 'Receptionist', 'slug' => 'receptionist'],
            ['name' => 'Inventory Manager', 'slug' => 'inventory_manager'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name']]
            );
        }

        $users = [
            [
                'name' => 'Dr. Ana Silva',
                'email' => 'ana.silva@example.com',
                'phone' => '555-2001',
                'password' => Hash::make('password'),
                'roles' => ['admin', 'vet'],
            ],
            [
                'name' => 'Dr. Rafael Costa',
                'email' => 'rafael.costa@example.com',
                'phone' => '555-2002',
                'password' => Hash::make('password'),
                'roles' => ['vet'],
            ],
            [
                'name' => 'Camila Ribeiro',
                'email' => 'camila.ribeiro@example.com',
                'phone' => '555-2003',
                'password' => Hash::make('password'),
                'roles' => ['receptionist'],
            ],
            [
                'name' => 'Lucas Mendes',
                'email' => 'lucas.mendes@example.com',
                'phone' => '555-2004',
                'password' => Hash::make('password'),
                'roles' => ['inventory_manager'],
            ],
        ];

        foreach ($users as $userData) {
            $roles = $userData['roles'];
            unset($userData['roles']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $roleIds = Role::whereIn('slug', $roles)->pluck('id');
            $user->roles()->sync($roleIds);
        }
    }
}
