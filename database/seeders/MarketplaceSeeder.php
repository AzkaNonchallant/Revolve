<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MarketplaceSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->seedCategories();
        $this->seedBrands();
        $this->seedSettings();
        $this->seedShops();
        $this->updateProducts();
        $this->seedVouchers();
        $this->seedBanners();
    }

    private function seedCategories(): void
    {
        $parents = [
            ['name' => 'Sepatu', 'children' => ['Sneakers', 'Running', 'Boots', 'Sandal', 'Casual']],
            ['name' => 'Baju', 'children' => ['Kaos', 'Kemeja', 'Hoodie', 'Sweater', 'Polo']],
            ['name' => 'Celana', 'children' => ['Jeans', 'Cargo', 'Chino', 'Jogger', 'Shorts']],
            ['name' => 'Jaket', 'children' => ['Bomber', 'Varsity', 'Windbreaker', 'Denim', 'Hoodie Jacket']],
        ];

        foreach ($parents as $parent) {
            $parentModel = Category::updateOrCreate(
                ['slug' => Str::slug($parent['name'])],
                ['name' => $parent['name'], 'parent_id' => null, 'is_active' => true]
            );

            foreach ($parent['children'] as $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($childName)],
                    ['name' => $childName, 'parent_id' => $parentModel->id, 'is_active' => true]
                );
            }
        }
    }

    private function seedBrands(): void
    {
        $names = ['Revolve Originals', 'NovaStep', 'UrbanLoft', 'Fashionista', 'Duramax'];

        foreach ($names as $name) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );
        }
    }

    private function seedSettings(): void
    {
        Setting::updateOrCreate(['key' => 'commission_rate'], ['value' => '5']);
    }

    private function seedShops(): void
    {
        $seller = User::where('email', 'seller@revolve.com')->first();

        if ($seller && $seller->role === 'seller') {
            Shop::updateOrCreate(
                ['user_id' => $seller->id],
                [
                    'name' => 'Revolve Store',
                    'slug' => 'revolve-store',
                    'description' => 'Toko fashion premium: sepatu, baju, celana, dan jaket berkualitas untuk gaya terbaikmu.',
                    'phone' => $seller->phone_number ?: '081234567890',
                    'is_active' => true,
                    'is_verified' => true,
                ]
            );
        }
    }

    private function updateProducts(): void
    {
        $seller = User::where('email', 'seller@revolve.com')->first();
        $brands = Brand::all();

        $types = [
            'Sneakers' => ['Sneakers'],
            'Running' => ['Lari', 'Running'],
            'Casual' => ['Kulit Formal', 'Kanvas Retro'],
            'Sandal' => ['Sandal'],
            'Kaos' => ['Kaos', 'T-Shirt'],
            'Kemeja' => ['Kemeja', 'Batik', 'Oxford', 'Flanel'],
            'Polo' => ['Polo'],
            'Jeans' => ['Jeans'],
            'Cargo' => ['Cargo'],
            'Chino' => ['Chino'],
            'Jogger' => ['Joger'],
            'Bomber' => ['Bomber'],
            'Windbreaker' => ['Windbreaker'],
            'Denim' => ['Denim'],
            'Hoodie Jacket' => ['Hoodie'],
            'Celana' => ['Kulot Wanita', 'Training'],
            'Jaket' => ['Parka'],
        ];

        foreach (Product::all() as $product) {
            $subCategory = null;
            foreach ($types as $typeName => $keywords) {
                foreach ($keywords as $keyword) {
                    if (Str::contains(Str::lower($product->name), Str::lower($keyword))) {
                        $subCategory = Category::where('slug', Str::slug($typeName))->first();
                        break 2;
                    }
                }
            }

            $price = (int) $product->price;

            $data = [
                'approval_status' => Product::APPROVAL_APPROVED,
                'approved_at' => now(),
                'status' => 1,
                'brand_id' => $brands->random()->id,
            ];

            if ($subCategory) {
                $data['category_id'] = $subCategory->id;
            }

            if ($seller) {
                $data['seller_id'] = $seller->id;
            }

            if ($product->discount_price === null && $price > 150000) {
                $data['discount_price'] = (int) round($price * 0.9);
            }

            $product->update($data);

            if ($product->variants()->count() === 0) {
                $stockPerVariant = (int) max(1, floor($product->stock / 3));

                if ($product->category && in_array($product->category->slug, ['sneakers', 'running', 'boots', 'sandal', 'casual', 'denim', 'bomber', 'varsity', 'windbreaker', 'hoodie-jacket'])) {
                    $sizes = ['40', '41', '42', '43'];
                    $label = 'Sepatu';
                } else {
                    $sizes = ['S', 'M', 'L', 'XL'];
                    $label = 'Pakaian';
                }

                foreach ($sizes as $size) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $size,
                        'color' => 'Hitam',
                        'sku' => strtoupper(Str::slug($product->name)).'-'.$size,
                        'price' => null,
                        'stock' => $stockPerVariant,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }

    private function seedVouchers(): void
    {
        Voucher::updateOrCreate(
            ['code' => 'WELCOME10'],
            [
                'name' => 'Diskon Sambutan 10%',
                'type' => 'percent',
                'value' => 10,
                'min_purchase' => 100000,
                'max_discount' => 50000,
                'quota' => 500,
                'scope' => 'marketplace',
                'is_active' => true,
            ]
        );

        Voucher::updateOrCreate(
            ['code' => 'HEBAT50K'],
            [
                'name' => 'Potongan Rp50.000',
                'type' => 'nominal',
                'value' => 50000,
                'min_purchase' => 300000,
                'max_discount' => null,
                'quota' => 200,
                'scope' => 'marketplace',
                'is_active' => true,
            ]
        );

        Voucher::updateOrCreate(
            ['code' => 'SEPATU15'],
            [
                'name' => 'Diskon Sepatu 15%',
                'type' => 'percent',
                'value' => 15,
                'min_purchase' => 200000,
                'max_discount' => 75000,
                'quota' => 300,
                'scope' => 'category',
                'category_id' => Category::where('slug', 'sepatu')->value('id'),
                'is_active' => true,
            ]
        );
    }

    private function seedBanners(): void
    {
        $heading = [
            'Promo Besar-besaran', 'Koleksi Fashion Terbaru', 'Diskon Musim Ini',
        ];

        foreach ($heading as $title) {
            Banner::updateOrCreate(
                ['title' => $title],
                [
                    'image' => '',
                    'link' => '/products',
                    'type' => 'hero',
                    'sort_order' => 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
