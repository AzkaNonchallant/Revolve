<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['courier_id', 'province', 'city', 'rate_per_kg', 'etd_days'])]
class ShippingRate extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'rate_per_kg' => 'decimal:2',
            'etd_days' => 'integer',
        ];
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}