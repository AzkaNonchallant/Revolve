<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'email', 'password', 'phone_number', 'role',
    'avatar', 'is_active', 'suspended_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_CUSTOMER = 'customer';

    public const ROLE_SELLER = 'seller';

    public const ROLE_ADMIN = 'admin';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'suspended_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null || ! $this->is_active;
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function wishlist()
    {
        return $this->hasOne(Wishlist::class);
    }

    public function latestSellerRequest()
    {
        return $this->hasOne(SellerRequest::class)->latest();
    }

    public function sellerRequests()
    {
        return $this->hasMany(SellerRequest::class);
    }

    public function changeRequests()
    {
        return $this->hasMany(UserChangeRequest::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function dashboardRoute(): string
    {
        if ($this->isAdmin()) {
            return route('admin.dashboard');
        }
        if ($this->isSeller() && $this->shop) {
            return route('seller.dashboard');
        }

        return route('home');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset('storage/'.$this->avatar) : null;
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_SELLER => 'Seller',
            default => 'Pembeli',
        };
    }
}
