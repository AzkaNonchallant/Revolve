<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    use HasFactory;

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::query()->where('key', $key)->value('value') ?? $default;
    }
}
