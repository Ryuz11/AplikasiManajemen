<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Manajer Warehouse',
                'email' => 'warehouse@example.com',
                'role' => 'warehouse',
            ],
            [
                'name' => 'Manajer Purchase',
                'email' => 'purchase@example.com',
                'role' => 'purchase',
            ],
            [
                'name' => 'Manajer Finance',
                'email' => 'finance@example.com',
                'role' => 'finance',
            ],
        ];

        foreach ($users as $userData) {
            $user = \App\Models\User::factory()->create([
                'name' => $userData['name'],
                'email' => $userData['email'],
            ]);
            $user->assignRole($userData['role']);
        }
    }
}
