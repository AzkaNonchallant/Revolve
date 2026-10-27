<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id'])]
class Wishlist extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'wishlist_items')
            ->withPivot('variant_id')
            ->withTimestamps();
    }
}
