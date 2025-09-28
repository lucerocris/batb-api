<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
        use HasFactory;

    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'product_sku',
        'variant_name',
        'variant_sku',
        'product_attributes',
        'quantity',
        'unit_price',
        'line_total',
        'discount_amount',
        'customization',
        'customization_notes',
        'fulfillment_status',
        'quantity_shipped',
        'quantity_returned',
    ];

    protected $casts = [
        'product_attributes' => 'array', // JSON to array
        'quantity' => 'integer',
        'unit_price' => 'float',
        'line_total' => 'float',
        'discount_amount' => 'float',
        'quantity_shipped' => 'integer',
        'quantity_returned' => 'integer',
    ];

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function productVariant() : BelongsTo 
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');   
    }

}
