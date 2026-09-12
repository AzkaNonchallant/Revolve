<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'order_id', 'transaction_id', 'payment_method', 'amount',
    'payment_status', 'paid_at', 'refunded_at',
])]
class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_REFUNDED = 'refunded';

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function statusLabel(): string
    {
        return match ($this->payment_status) {
            self::STATUS_PAID => 'Dibayar',
            self::STATUS_FAILED => 'Gagal',
            self::STATUS_REFUNDED => 'Refund',
            default => 'Menunggu',
        };
    }
}
