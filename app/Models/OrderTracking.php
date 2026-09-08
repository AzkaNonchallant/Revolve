<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['order_id', 'status', 'note'])]
class OrderTracking extends Model
{
    use HasFactory;

    protected $table = 'order_tracking';

    protected function casts(): array
    {
        return [];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}