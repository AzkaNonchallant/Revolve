<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@revolve.com'],
            [
                'name' => 'Admin Revolve',
                'password' => 'password',
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'suspended_at' => null,
            ]
        );
    }
}
