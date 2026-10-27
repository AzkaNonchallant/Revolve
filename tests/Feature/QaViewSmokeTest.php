<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\SellerRequest;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserChangeRequest;
use App\Models\Voucher;
use Database\Seeders\MarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QaViewSmokeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $seller;

    private Product $product;

    private SellerRequest $sellerRequest;

    private UserChangeRequest $changeRequest;

    private Order $order;

    private Category $category;

    private Voucher $voucher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => MarketplaceSeeder::class]);

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->seller = User::factory()->create(['role' => 'seller', 'is_active' => true]);
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);

        Shop::create([
            'user_id' => $this->seller->id,
            'name' => 'Toko Uji',
            'slug' => 'toko-uji',
            'is_active' => true,
            'is_verified' => true,
        ]);

        $this->category = Category::whereNotNull('parent_id')->first() ?? Category::first();
        $brand = Brand::first();

        $this->product = Product::create([
            'seller_id' => $this->seller->id,
            'category_id' => $this->category?->id,
            'brand_id' => $brand?->id,
            'name' => 'Produk Uji Smoke',
            'slug' => 'produk-uji-smoke',
            'price' => 150000,
            'discount_price' => 130000,
            'stock' => 5,
            'weight' => 300,
            'status' => 1,
            'approval_status' => Product::APPROVAL_APPROVED,
        ]);

        $this->sellerRequest = SellerRequest::create([
            'user_id' => $this->seller->id,
            'shop_name' => 'Toko Uji',
            'phone' => '081230000000',
            'address' => 'Jakarta',
            'status' => SellerRequest::STATUS_PENDING,
        ]);

        $this->changeRequest = UserChangeRequest::create([
            'user_id' => $customer->id,
            'field' => 'name',
            'old_value' => 'Lama',
            'new_value' => 'Baru',
            'status' => UserChangeRequest::STATUS_PENDING,
        ]);

        $this->voucher = Voucher::create([
            'code' => 'SMOKE10',
            'name' => 'Smoke',
            'type' => 'percent',
            'value' => 10,
            'scope' => 'marketplace',
            'is_active' => true,
        ]);

        Review::create([
            'product_id' => $this->product->id,
            'user_id' => $customer->id,
            'rating' => 5,
            'review' => 'Bagus',
            'is_hidden' => false,
        ]);

        $this->order = Order::create([
            'user_id' => $customer->id,
            'address_id' => Address::create([
                'user_id' => $customer->id,
                'receiver_name' => 'Budi',
                'phone' => '08123123123',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'district' => 'Kebayoran Baru',
                'village' => 'Senayan',
                'postal_code' => '12110',
                'full_address' => 'Jl. Sudirman No. 1',
            ])->id,
            'order_number' => 'RV-1000001',
            'subtotal' => 150000,
            'shipping_cost' => 10000,
            'discount' => 0,
            'total' => 160000,
            'status' => 'paid',
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'seller_id' => $this->seller->id,
            'price' => 150000,
            'quantity' => 1,
            'subtotal' => 150000,
        ]);
    }

    public function test_admin_pages_render(): void
    {
        $this->actingAs($this->admin);

        $routes = [
            ['name' => 'admin.dashboard', 'params' => []],
            ['name' => 'admin.users.index', 'params' => []],
            ['name' => 'admin.users.show', 'params' => [$this->admin->id]],
            ['name' => 'admin.sellers.index', 'params' => []],
            ['name' => 'admin.sellers.show', 'params' => [$this->seller->id]],
            ['name' => 'admin.seller-requests.index', 'params' => []],
            ['name' => 'admin.seller-requests.show', 'params' => [$this->sellerRequest->id]],
            ['name' => 'admin.change-requests.index', 'params' => []],
            ['name' => 'admin.change-requests.show', 'params' => [$this->changeRequest->id]],
            ['name' => 'admin.products.index', 'params' => []],
            ['name' => 'admin.products.show', 'params' => [$this->product->id]],
            ['name' => 'admin.products.edit', 'params' => [$this->product->id]],
            ['name' => 'admin.categories.index', 'params' => []],
            ['name' => 'admin.categories.create', 'params' => []],
            ['name' => 'admin.categories.edit', 'params' => [$this->category->id]],
            ['name' => 'admin.orders.index', 'params' => []],
            ['name' => 'admin.orders.show', 'params' => [$this->order->id]],
            ['name' => 'admin.payments.index', 'params' => []],
            ['name' => 'admin.commissions.index', 'params' => []],
            ['name' => 'admin.vouchers.index', 'params' => []],
            ['name' => 'admin.vouchers.create', 'params' => []],
            ['name' => 'admin.vouchers.edit', 'params' => [$this->voucher->id]],
            ['name' => 'admin.banners.index', 'params' => []],
            ['name' => 'admin.banners.create', 'params' => []],
            ['name' => 'admin.banners.edit', 'params' => [$this->product->id]],
            ['name' => 'admin.reviews.index', 'params' => []],
            ['name' => 'admin.reports.index', 'params' => []],
            ['name' => 'admin.notifications.index', 'params' => []],
            ['name' => 'admin.settings.index', 'params' => []],
        ];

        foreach ($routes as $route) {
            $response = $this->get(route($route['name'], $route['params']));
            $response->assertOk("Halaman {$route['name']} harus 200");
        }
    }

    public function test_seller_pages_render(): void
    {
        $this->actingAs($this->seller);

        $routes = [
            'seller.dashboard',
            'seller.shop.edit',
            'seller.products.index',
            'seller.products.pending',
            'seller.products.create',
            ['seller.products.edit', $this->product->id],
            'seller.orders.index',
            ['seller.orders.show', $this->order->id],
            'seller.sales.index',
            'seller.statistics.index',
            'seller.notifications.index',
        ];

        foreach ($routes as $route) {
            $name = is_array($route) ? $route[0] : $route;
            $param = is_array($route) ? $route[1] : null;

            $response = $this->get(route($name, $param ? [$param] : []));
            $response->assertOk("Halaman {$name} harus 200");
        }
    }
}
