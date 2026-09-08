<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'service'])]
class Courier extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [];
    }

    public function shippingRates()
    {
        return $this->hasMany(ShippingRate::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}