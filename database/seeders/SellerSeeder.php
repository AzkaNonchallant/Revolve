<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $seller = User::updateOrCreate(
            ['email' => 'seller@revolve.com'],
            [
                'name' => 'Seller Revolve',
                'password' => 'password',
                'role' => User::ROLE_SELLER,
                'is_active' => true,
                'suspended_at' => null,
            ]
        );

        Shop::updateOrCreate(
            ['user_id' => $seller->id],
            [
                'name' => 'Revolve Store',
                'slug' => 'revolve-store',
                'description' => 'Toko fashion premium: sepatu, baju, celana, dan jaket berkualitas untuk gaya terbaikmu.',
                'phone' => $seller->phone_number ?: '081234567890',
                'address' => 'Jakarta Selatan, DKI Jakarta',
                'is_active' => true,
                'is_verified' => true,
            ]
        );
    }
}
