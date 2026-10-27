<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id', 'address_id', 'courier_id', 'shipping_rate_id', 'voucher_id', 'voucher_code',
    'order_number', 'subtotal', 'shipping_cost', 'discount', 'voucher_discount',
    'total', 'commission_rate', 'payment_method', 'delivery_schedule', 'status',
])]
class Order extends Model
{
    use HasFactory;

    public const STATUS_MAP = [
        'pending' => 'Pending',
        'waiting_payment' => 'Menunggu Pembayaran',
        'paid' => 'Dibayar',
        'processed' => 'Diproses',
        'packing' => 'Dikemas',
        'shipped' => 'Dikirim',
        'delivered' => 'Diterima',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'return_requested' => 'Return Diajukan',
        'returned' => 'Return',
        'refund_requested' => 'Refund Diajukan',
        'refunded' => 'Refund',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'discount' => 'decimal:2',
            'voucher_discount' => 'decimal:2',
            'total' => 'decimal:2',
            'delivery_schedule' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function shippingRate()
    {
        return $this->belongsTo(ShippingRate::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tracking()
    {
        return $this->hasMany(OrderTracking::class)->latest();
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function latestTracking()
    {
        return $this->hasOne(OrderTracking::class)->latest();
    }

    public function statusLabel(): string
    {
        return self::STATUS_MAP[$this->status] ?? $this->status;
    }

    public function isPayable(): bool
    {
        return in_array($this->status, ['pending', 'waiting_payment']);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'waiting_payment', 'paid']);
    }

    public function scopeForSeller($query, int $sellerId)
    {
        return $query->whereHas('items', fn ($q) => $q->where('seller_id', $sellerId));
    }
}
