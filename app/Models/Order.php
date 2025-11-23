<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Order extends Model
{
    use HasFactory, HasUuids;

    // UUID as primary key
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'order_number',
        'status',
        'fulfillment_status',
        'payment_status',
        'payment_method',
        'payment_due_date',
        'payment_sent_date',
        'payment_verified_date',
        'payment_verified_by',
        'expires_at',
        'payment_instructions',
        'payment_reference',
        'idempotency_key',
        'subtotal',
        'tax_amount',
        'phone_number',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'email',
        'refunded_amount',
        'currency',
        'shipping_address',
        'billing_address',
        'admin_notes',
        'customer_notes',
        'reminder_sent_count',
        'last_reminder_sent',
        'order_date',
        'tip',
    ];

    protected $casts = [
        'payment_due_date' => 'datetime',
        'payment_sent_date' => 'datetime',
        'payment_verified_date' => 'datetime',
        'expires_at' => 'datetime',
        'payment_instructions' => 'array', // JSON → array
        'subtotal' => 'float',
        'tax_amount' => 'float',
        'shipping_amount' => 'float',
        'discount_amount' => 'float',
        'tip' =>'float',
        'total_amount' => 'float',
        'refunded_amount' => 'float',
        'shipping_address' => 'array',     // JSON → array
        'billing_address' => 'array',      // JSON → array
        'reminder_sent_count' => 'integer',
        'last_reminder_sent' => 'datetime',
        'order_date' => 'datetime',
    ];

    //
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Keep a single canonical relation name
    public function verifiedBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    public function orderItems() : HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function inventoryLogs() : HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'order_id');
    }

    /**
     * Calculate total amount the user has spent
     *
     *
     */

}
