<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    // UUID primary key
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_customer',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
        'applicable_products',
        'applicable_categories',
        'first_order_only',
    ];

    protected $casts = [
        'value' => 'float',
        'minimum_amount' => 'float',
        'maximum_discount' => 'float',
        'usage_limit' => 'integer',
        'usage_limit_per_customer' => 'integer',
        'used_count' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'applicable_products' => 'array',   
        'applicable_categories' => 'array', 
        'first_order_only' => 'boolean',
    ];
}
