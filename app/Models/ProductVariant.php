<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price_adjustment',
        'stock_quantity',
        'reserved_quantity',
        'attributes',
        'image_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price_adjustment' => 'float',  // Always a decimal
        'stock_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'attributes' => 'array',        // JSON to array
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($variant) {
            if (empty($variant->sku)) {
                $variant->sku = 'VAR-' . strtoupper(str()->random(6));
            }
        });
    }

    public function orderItems() : HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_variant_id');
    }

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function inventoryLogs() : HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'product_variant_id');
    }
}
