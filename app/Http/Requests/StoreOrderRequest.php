<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\OrderNumberGenerator;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $shippingProvince = $this->input('shippingAddress.province');

        $shippingAmount = null;
        if ($shippingProvince && strtolower($shippingProvince) !== 'cebu') {
            $shippingAmount = 200.00;
        }

        $this->merge([
            'orderNumber' => OrderNumberGenerator::generateRandomOrderNumber(),
            'orderDate' => now(),
            'expiresAt' => now()->addHours(24),
            'fulfillmentStatus' => 'pending',
            'paymentStatus' => 'pending',
            'shippingAmount' => $shippingAmount,
        ]);
    }

    public function rules(): array
    {
        return [
            // Store Order Request
            'userId' => 'nullable|uuid|exists:users,id',
            'orderNumber' => 'required|string|max:20|unique:orders,order_number',
            'fulfillmentStatus' => 'required|in:pending,processing,fulfilled,shipped,delivered,cancelled',
            'paymentStatus' => 'required|in:pending,awaiting_confirmation,paid,failed,refunded',
            'paymentMethod' => 'required|in:bank_transfer,gcash',
            'email' => 'required|string|max:100',
            'expiresAt' => 'nullable|date',
            'orderDate' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'idempotencyKey' => 'required|uuid',


            'shippingAddress' => 'required|array',
            'shippingAddress.firstName' => 'required|string|max:100',
            'shippingAddress.lastName' => 'required|string|max:100',
            'shippingAddress.addressLine1' => 'required|string|max:255',
            'shippingAddress.addressLine2' => 'nullable|string|max:255',
            'shippingAddress.city' => 'required|string|max:100',
            'shippingAddress.province' => 'required|string|max:100',
            'shippingAddress.barangay' => 'required|string|max:100',
            'shippingAddress.postalCode' => 'required|string|max:10',
            'shippingAddress.countryCode' => 'required|string|size:2',
            'shippingAddress.region' => 'required|string|max:100',
            'shippingAddress.phone' => 'required|string|max:100',

            'billingAddress' => 'nullable|array',
            'billingAddress.firstName' => 'nullable|string|max:100',
            'billingAddress.lastName' => 'nullable|string|max:100',
            'billingAddress.addressLine1' => 'nullable|string|max:255',
            'billingAddress.addressLine2' => 'nullable|string|max:255',
            'billingAddress.city' => 'nullable|string|max:100',
            'billingAddress.province' => 'nullable|string|max:100',
            'billingAddress.barangay' => 'nullable|string|max:100',
            'billingAddress.postalCode' => 'nullable|string|max:10',
            'billingAddress.countryCode' => 'nullable|string|size:2',
            'billingAddress.region' => 'nullable|string|max:100',
            'billingAddress.phone' => 'nullable|string|max:100',

            'discountAmount' => 'nullable|numeric|min:0',
            'shippingAmount' => 'nullable|numeric|min:0',
            'taxAmount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'customerNotes' => 'nullable|string',

            // Validate order items with optional design grouping metadata
            'orderItems' => 'required|array|min:1',
            'orderItems.*.productId' => 'required|uuid|exists:products,id',
            'orderItems.*.quantity' => 'required|integer|min:1',
            'orderItems.*.unitPrice' => 'required|numeric|min:0',

            // Optional designs metadata array for snapshot
            'designs' => 'nullable|array',
            'designs.*.designKey' => 'required|string|max:64',
            'designs.*.name' => 'nullable|string|max:150',
            'designs.*.imageUrl' => 'nullable|url|max:2048',

            // Optional uploaded design images keyed by designKey
            'designImages' => 'nullable|array',
            'designImages.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:8192',

            // Messages
            'email.email' => 'Please provide a valid email address.',
            'paymentMethod.required' => 'Please select a payment method.',
            'shippingAddress.required' => 'Shipping address is required.',
            'orderItems.required' => 'Order must contain at least one item.',
            'orderItems.min' => 'Order must contain at least one item.',
        ];
    }

    public function toSnakeCase(): array
    {
        $data = $this->validated();

        $snake = [
            'user_id' => $data['userId'] ?? null,
            'order_number' => $data['orderNumber'],
            'fulfillment_status' => $data['fulfillmentStatus'],
            'payment_status' => $data['paymentStatus'],
            'payment_method' => $data['paymentMethod'],
            'email' => $data['email'],
            'expires_at' => $data['expiresAt'] ?? null,
            'order_date' => $data['orderDate'],
            'idempotency_key' => $data['idempotencyKey'],
            'discount_amount' => $data['discountAmount'] ?? 0,
            'currency' => $data['currency'],
            'customer_notes' => $data['customerNotes'] ?? null,
            'shipping_amount' => $data['shippingAmount'] ?? 0,
            'tax_amount' => $data['taxAmount'] ?? 0,
            'shipping_address' => $data['shippingAddress'],
            'billing_address' => $data['billingAddress'] ?? null,
        ];

        // Map order items
        $snake['order_items'] = collect($data['orderItems'])
            ->map(function ($item) {
                return [
                    'product_id' => $item['productId'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unitPrice'],
                ];
            })
            ->toArray();

        return $snake;
    }
}
