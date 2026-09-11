<?php

namespace Database\Seeders;

use App\Models\Courier;
use App\Models\ShippingRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed kurir beserta tarif pengiriman per kg.
     */
    public function run(): void
    {
        $couriers = [
            [
                'name' => 'JNE',
                'service' => 'Reguler',
                'rates' => [
                    ['province' => 'DKI Jakarta', 'city' => 'Jakarta', 'rate_per_kg' => 9000, 'etd_days' => 2],
                    ['province' => 'Jawa Barat', 'city' => 'Bandung', 'rate_per_kg' => 12000, 'etd_days' => 2],
                    ['province' => 'Jawa Timur', 'city' => 'Surabaya', 'rate_per_kg' => 15000, 'etd_days' => 3],
                    ['province' => 'Bali', 'city' => 'Denpasar', 'rate_per_kg' => 18000, 'etd_days' => 3],
                ],
            ],
            [
                'name' => 'JNE',
                'service' => 'YES (Yakin Esok Sampai)',
                'rates' => [
                    ['province' => 'DKI Jakarta', 'city' => 'Jakarta', 'rate_per_kg' => 15000, 'etd_days' => 1],
                    ['province' => 'Jawa Barat', 'city' => 'Bandung', 'rate_per_kg' => 18000, 'etd_days' => 1],
                    ['province' => 'Jawa Timur', 'city' => 'Surabaya', 'rate_per_kg' => 22000, 'etd_days' => 2],
                    ['province' => 'Bali', 'city' => 'Denpasar', 'rate_per_kg' => 25000, 'etd_days' => 2],
                ],
            ],
            [
                'name' => 'J&T Express',
                'service' => 'Express',
                'rates' => [
                    ['province' => 'DKI Jakarta', 'city' => 'Jakarta', 'rate_per_kg' => 8000, 'etd_days' => 2],
                    ['province' => 'Jawa Barat', 'city' => 'Bandung', 'rate_per_kg' => 10000, 'etd_days' => 2],
                    ['province' => 'Jawa Timur', 'city' => 'Surabaya', 'rate_per_kg' => 13000, 'etd_days' => 3],
                    ['province' => 'Bali', 'city' => 'Denpasar', 'rate_per_kg' => 16000, 'etd_days' => 3],
                ],
            ],
            [
                'name' => 'SiCepat',
                'service' => 'REG',
                'rates' => [
                    ['province' => 'DKI Jakarta', 'city' => 'Jakarta', 'rate_per_kg' => 7500, 'etd_days' => 2],
                    ['province' => 'Jawa Barat', 'city' => 'Bandung', 'rate_per_kg' => 9000, 'etd_days' => 2],
                    ['province' => 'Jawa Timur', 'city' => 'Surabaya', 'rate_per_kg' => 12000, 'etd_days' => 3],
                    ['province' => 'Bali', 'city' => 'Denpasar', 'rate_per_kg' => 15000, 'etd_days' => 3],
                ],
            ],
            [
                'name' => 'Pos Indonesia',
                'service' => 'Kilat Khusus',
                'rates' => [
                    ['province' => 'DKI Jakarta', 'city' => 'Jakarta', 'rate_per_kg' => 10000, 'etd_days' => 3],
                    ['province' => 'Jawa Barat', 'city' => 'Bandung', 'rate_per_kg' => 11500, 'etd_days' => 3],
                    ['province' => 'Jawa Timur', 'city' => 'Surabaya', 'rate_per_kg' => 14000, 'etd_days' => 4],
                    ['province' => 'Bali', 'city' => 'Denpasar', 'rate_per_kg' => 16000, 'etd_days' => 4],
                ],
            ],
        ];

        foreach ($couriers as $courier) {
            $rates = $courier['rates'];
            unset($courier['rates']);

            $courierModel = Courier::updateOrCreate(
                ['name' => $courier['name'], 'service' => $courier['service']],
                $courier
            );

            foreach ($rates as $rate) {
                ShippingRate::updateOrCreate(
                    ['courier_id' => $courierModel->id, 'province' => $rate['province'], 'city' => $rate['city']],
                    $rate
                );
            }
        }
    }
}