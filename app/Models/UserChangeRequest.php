<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id', 'field', 'old_value', 'new_value', 'reason',
    'status', 'admin_note', 'reviewed_by', 'reviewed_at',
])]
class UserChangeRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const FIELDS = [
        'name' => 'Nama',
        'email' => 'Email',
        'phone_number' => 'Nomor Telepon',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getFieldLabelAttribute(): string
    {
        return self::FIELDS[$this->field] ?? ucfirst($this->field);
    }

    public function getOldValueDisplayAttribute(): string
    {
        return self::displayValue($this->old_value);
    }

    public function getNewValueDisplayAttribute(): string
    {
        return self::displayValue($this->new_value);
    }

    private static function displayValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '—';
        }

        return (string) $value;
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default => 'Menunggu Review',
        };
    }
}
