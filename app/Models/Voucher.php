<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'code', 'name', 'type', 'value', 'min_purchase', 'max_discount',
    'starts_at', 'ends_at', 'quota', 'used_count', 'scope',
    'category_id', 'seller_id', 'created_by', 'is_active',
])]
class Voucher extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_purchase' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->where(fn ($q) => $q->whereNull('quota')->orWhereColumn('used_count', '<', 'quota'));
    }

    public function isValidFor(int $subtotal): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }
        if ($this->quota !== null && $this->used_count >= $this->quota) {
            return false;
        }
        if ($subtotal < $this->min_purchase) {
            return false;
        }

        return true;
    }

    public function discountAmountFor(int $subtotal): int
    {
        if ($this->type === 'percent') {
            $amount = (int) round(($subtotal * $this->value) / 100);
        } else {
            $amount = (int) round($this->value);
        }

        if ($this->max_discount && $amount > $this->max_discount) {
            $amount = (int) round($this->max_discount);
        }

        return min($amount, $subtotal);
    }
}
