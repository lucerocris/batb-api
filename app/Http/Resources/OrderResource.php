<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'orderID' => $this->id,
            'userID' => $this->user_id,
            'orderNumber' => $this->order_number,
            'status' => $this->status,
            'fulfillmentStatus' => $this->fulfillment_status,
            'paymentStatus' => $this->payment_status,
            'paymentMethod' => $this->payment_method,
            'paymentDueDate' => $this->payment_due_date,
            'paymentSentDate' => $this->payment_sent_date,
            'paymentVerifiedDate' => $this->payment_verified_date,
            'paymentVerifiedBy' => $this->payment_verified_by,
            'expiresAt' => $this->expires_at,
            'paymentInstructions' => $this->payment_instructions,
            'paymentReference' => $this->payment_reference,
            'subtotal' => $this->subtotal,
            'taxAmount' => $this->tax_amount,
            'shippingAmount' => $this->shipping_amount,
            'discountAmount' => $this->discount_amount,
            'totalAmount' => $this->total_amount,
            'tip' => $this->tip,
            'imageUrl' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'email'=> $this->email,
            'refundedAmount' => $this->refunded_amount,
            'currency' => $this->currency,
            'shippingAddress' => $this->shipping_address,
            'billingAddress' => $this->billing_address,
            'adminNotes' => $this->admin_notes,
            'customerNotes' => $this->customer_notes,
            'reminderSentCount' => $this->reminder_sent_count,
            'lastReminderSent' => $this->last_reminder_sent,
            'orderDate' => $this->order_date,

            'customer' => new UserResource($this->whenLoaded('user')),
            'orderItems' => OrderItemResource::collection($this->whenLoaded('orderItems')),
        ];
    }
}
