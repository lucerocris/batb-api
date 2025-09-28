<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\OrderNumberGenerator;


class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'order_number' => OrderNumberGenerator::generateRandomOrderNumber(),
            'order_date' => now()
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        // Store Order Request
        'userId' => 'nullable|uuid|exists:users,id',
        'orderNumber' => 'required|string|max:20|unique:orders,order_number',
        //'status' => 'required|in:pending_payment,payment_sent,payment_verified,processing,shipped,delivered,cancelled,refunded,expired',
        'fulfillmentStatus' => 'required|in:pending,processing,partial,fulfilled,cancelled',
        'paymentStatus' => 'required|in:pending,awaiting_confirmation,partial,paid,failed,refunded,disputed',
        'paymentMethod' => 'nullable|in:bank_transfer,gcash,paymaya,cash_on_delivery,western_union,maya_bank,bpi,bdo',
        'paymentDueDate' => 'nullable|date',
        'paymentSentDate' => 'nullable|date',
        'paymentVerifiedDate' => 'nullable|date',
        'paymentVerifiedBy' => 'nullable|uuid|exists:users,id',
        'email'=>'required|string|max:100',
        'expiresAt' => 'nullable|date',
        'paymentInstructions' => 'nullable|array',
        'paymentReference' => 'nullable|string|max:100',
        'taxAmount' => 'nullable|numeric|min:0',
        'shippingAmount' => 'nullable|numeric|min:0',
        'discountAmount' => 'nullable|numeric|min:0',
        'refundedAmount' => 'nullable|numeric|min:0',
        'currency' => 'required|string|size:3',
        'shippingAddress' => 'required|array',
        'billingAddress' => 'nullable|array',
        'adminNotes' => 'nullable|string',
        'customerNotes' => 'nullable|string',
        'reminderSentCount' => 'nullable|integer|min:0',
        'lastReminderSent' => 'nullable|date',
        'orderDate' => 'required|date',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',


        /**
         * Validate order_items as well since its only one payload
         */
        'orderItems'               => 'required|array|min:1',
        'orderItems.*.productId'  => 'required|uuid|exists:products,id',
        'orderItems.*.productVariantId' => 'nullable|integer|exists:product_variants,id',
        'orderItems.*.quantity'    => 'required|integer|min:1',
        'orderItems.*.unitPrice' => 'required|numeric|min:0'

        // image upload (optional)
        ];
    }

    public function toSnakeCase()
    {
        $data = $this->validated();


    $snake = [
        'user_id' => $data['userId'],
        'order_number' => $data['orderNumber'],
        'fulfillment_status' => $data['fulfillmentStatus'],
        'payment_status' => $data['paymentStatus'],
        'email' => $data['email'],
        'currency' => $data['currency'],
        'order_date' => $data['orderDate'],
    ];


    $snake['shipping_address'] = [
        'first_name' => $data['shippingAddress']['firstName'],
        'last_name' => $data['shippingAddress']['lastName'],
        'address_line1' => $data['shippingAddress']['addressLine1'],
        'address_line2' => $data['shippingAddress']['addressLine2'] ?? null,
        'city' => $data['shippingAddress']['city'],
        'state_province' => $data['shippingAddress']['stateProvince'],
        'postal_code' => $data['shippingAddress']['postalCode'],
        'country_code' => $data['shippingAddress']['countryCode'],
    ];


    $snake['order_items'] = collect($data['orderItems'])->map(function ($item) {
        return [
            'product_id' => $item['productId'],
            'product_variant_id' => $item['productVariantId'] ?? null,
            'quantity' => $item['quantity'],
            'unit_price' => $item['unitPrice']
        ];
    })->toArray();

    return $snake;
    }
}
