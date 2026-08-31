<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Administrator Utama', 'email' => 'admin@tokokita.com', 'password' => 'adminpassword123', 'role' => Role::Admin],
            ['name' => 'Admin CS', 'email' => 'cs@tokokita.com', 'password' => 'cs12345', 'role' => Role::CustomerService],
            ['name' => 'Staf Gudang 01', 'email' => 'gudang@tokokita.com', 'password' => 'gudang123', 'role' => Role::Gudang],
            ['name' => 'Manajer Keuangan', 'email' => 'finance@tokokita.com', 'password' => 'finance123', 'role' => Role::Manajemen],
        ];

        foreach ($users as $data) {
            User::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'], // otomatis di-hash oleh cast 'hashed'
                    'role' => $data['role'],
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
