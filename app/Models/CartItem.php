<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['cart_id', 'product_id', 'variant_id', 'quantity', 'is_selected'])]
class CartItem extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'is_selected' => 'boolean',
        ];
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function effectivePrice(): float
    {
        if ($this->variant && $this->variant->price !== null) {
            return (float) $this->variant->price;
        }

        return $this->product->activePrice();
    }
}
