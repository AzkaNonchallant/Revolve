<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'seller_id', 'category_id', 'brand_id', 'name', 'slug', 'description', 'material',
    'price', 'discount_price', 'stock', 'weight', 'image', 'status',
    'approval_status', 'approval_note', 'approved_at',
])]
class Product extends Model
{
    use HasFactory;

    public const APPROVAL_PENDING = 'pending';

    public const APPROVAL_APPROVED = 'approved';

    public const APPROVAL_REJECTED = 'rejected';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'weight' => 'integer',
            'stock' => 'integer',
            'status' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'seller_id', 'user_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvals()
    {
        return $this->hasMany(ProductApproval::class)->latest();
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true)->orderBy('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 1)
            ->where('approval_status', self::APPROVAL_APPROVED);
    }

    public function scopeForCustomer($query)
    {
        return $query->published()->where('stock', '>', 0);
    }

    public function hasDiscount(): bool
    {
        return $this->discount_price !== null && (float) $this->discount_price > 0 && (float) $this->discount_price < (float) $this->price;
    }

    public function activePrice(): float
    {
        return (float) ($this->hasDiscount() ? $this->discount_price : $this->price);
    }

    public function discountPercent(): int
    {
        if (! $this->hasDiscount()) {
            return 0;
        }

        return (int) round(((float) $this->price - (float) $this->discount_price) / (float) $this->price * 100);
    }

    public function totalStock(): int
    {
        $variantStock = $this->variants->sum('stock');

        return $variantStock > 0 ? $variantStock : (int) $this->stock;
    }

    public function reviewsAverage(): float
    {
        return round((float) $this->reviews->where('is_hidden', false)->avg('rating') ?? 0, 1);
    }

    public function reviewsCount(): int
    {
        return $this->reviews->where('is_hidden', false)->count();
    }

    public function soldCount(): int
    {
        return (int) $this->orderItems()->whereHas('order', fn ($q) => $q->whereIn('status', ['paid', 'processed', 'packing', 'shipped', 'delivered', 'completed']))->sum('quantity');
    }

    public function pendingApproval(): bool
    {
        return $this->approval_status === self::APPROVAL_PENDING;
    }

    public function isActive(): bool
    {
        return (int) $this->status === 1;
    }

    public function approvalStatusLabel(): string
    {
        return match ($this->approval_status) {
            self::APPROVAL_APPROVED => 'Disetujui',
            self::APPROVAL_REJECTED => 'Ditolak',
            default => 'Menunggu Review',
        };
    }
}
