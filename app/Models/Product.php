<?php

namespace App\Models;

use App\Services\ProductSKUGenerator;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    // If the primary key is a UUID instead of auto-increment
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'base_price',
        'sale_price',
        'cost_price',
        'stock_status',
        'type',
        'image_path',
        'gallery_images',
        'is_active',
        'is_featured',
        'available_from',
        'weight',
        'view_count',
        'purchase_count',
        'average_rating',
        'review_count',
    ];

    protected $casts = [
        'base_price' => 'float',
        'sale_price' => 'float',
        'cost_price' => 'float',
        'stock_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'track_inventory' => 'boolean',
        'allow_backorder' => 'boolean',
        // no images array anymore
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'available_from' => 'datetime',
        'weight' => 'float',
        'view_count' => 'integer',
        'purchase_count' => 'integer',
        'average_rating' => 'float',
        'review_count' => 'integer',
        'gallery_images' => 'array',
    ];


    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->sku) && $product->category_id) {
                $product->sku = ProductSKUGenerator::generateSKU($product->category_id);
            }
        });
    }


    public function getRouteKeyName()
    {
        return 'id'; // UUID
    }

    //
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }


    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'product_id');
    }

    /**
     * Get cart items for this product.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }


}
