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
        'created_at'
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

    /**
     * Accept payment proof and update statuses accordingly
     *
     * @param string|null $verifiedBy User ID of the admin who verified the payment
     * @return bool
     */
    public function acceptPayment(?string $verifiedBy = null): bool
    {
        if ($this->payment_status !== 'pending') {
            throw new \Exception("Payment can only be accepted when status is 'pending'");
        }

        $this->payment_status = 'paid';
        $this->fulfillment_status = 'fulfilled';
        $this->payment_verified_date = now();

        if ($verifiedBy) {
            $this->payment_verified_by = $verifiedBy;
        }

        return $this->save();
    }

    /**
     * Reject payment proof and update statuses accordingly
     *
     * @param string|null $verifiedBy User ID of the admin who rejected the payment
     * @return bool
     */
    public function rejectPayment(?string $verifiedBy = null): bool
    {
        if ($this->payment_status !== 'pending') {
            throw new \Exception("Payment can only be rejected when status is 'pending'");
        }

        $this->payment_status = 'failed';
        $this->fulfillment_status = 'cancelled';
        $this->payment_verified_date = now();

        if ($verifiedBy) {
            $this->payment_verified_by = $verifiedBy;
        }

        return $this->save();
    }

    /**
     * Progress fulfillment status
     * Only allows progression when payment_status is 'paid'
     *
     * @param string $newStatus The new fulfillment status
     * @return bool
     */
    public function progressFulfillment(string $newStatus): bool
    {
        $allowedStatuses = ['fulfilled', 'shipped', 'delivered'];

        if (!in_array($newStatus, $allowedStatuses)) {
            throw new \Exception("Invalid fulfillment status: {$newStatus}");
        }

        if ($this->payment_status !== 'paid') {
            throw new \Exception("Fulfillment can only progress when payment_status is 'paid'");
        }

        // Ensure status progression is sequential
        $statusOrder = ['pending' => 0, 'fulfilled' => 1, 'shipped' => 2, 'delivered' => 3];
        $currentOrder = $statusOrder[$this->fulfillment_status] ?? -1;
        $newOrder = $statusOrder[$newStatus] ?? -1;

        if ($newOrder <= $currentOrder) {
            throw new \Exception("Cannot regress fulfillment status. Current: {$this->fulfillment_status}, Attempted: {$newStatus}");
        }

        $this->fulfillment_status = $newStatus;
        return $this->save();
    }

    /**
     * Handle refund and update statuses
     *
     * @param float|null $refundedAmount The amount to refund
     * @return bool
     */
    public function processRefund(?float $refundedAmount = null): bool
    {
        if ($this->payment_status !== 'paid') {
            throw new \Exception("Refunds can only be processed when payment_status is 'paid'");
        }

        $this->payment_status = 'refunded';

        if ($refundedAmount !== null) {
            $this->refunded_amount = $refundedAmount;
        } else {
            // Default to full refund if amount not specified
            $this->refunded_amount = $this->total_amount ?? 0;
        }

        // Cancel fulfillment if order was not yet delivered
        if (!in_array($this->fulfillment_status, ['delivered', 'cancelled'])) {
            $this->fulfillment_status = 'cancelled';
        }

        return $this->save();
    }


    public function getCustomerNameAttribute(): string
    {
        $address = $this->shipping_address;

        if (!is_array($address)) {
            return '';
        }

        return trim(($address['first_name'] ?? '') . ' ' . ($address['last_name'] ?? ''));
    }

    /**
     * Check if fulfillment can be progressed
     *
     * @return bool
     */
    public function canProgressFulfillment(): bool
    {
        return $this->payment_status === 'paid'
            && !in_array($this->fulfillment_status, ['delivered', 'cancelled']);
    }

    /**
     * Check if payment can be reviewed (accepted or rejected)
     *
     * @return bool
     */
    public function canReviewPayment(): bool
    {
        return $this->payment_status === 'pending';
    }
}
