
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class StoreOrderItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'orderId' => 'required|uuid|exists:orders,id',
            'productId' => 'required|uuid|exists:products,id',
            'productName' => 'required|string|max:255',
            'productSku' => 'required|string|max:255',
            'variantName' => 'nullable|string|max:255',
            'variantSku' => 'nullable|string|max:255',
            'productAttributes' => 'nullable|array',
            'quantity' => 'required|integer|min:1',
            'discountAmount' => 'nullable|numeric|min:0',
            'customization' => 'nullable|array',
            'customizationNotes' => 'nullable|string',
            'fulfillmentStatus' => 'nullable|in:pending,processing,shipped,delivered,cancelled,returned',
            'quantityShipped' => 'nullable|integer|min:0',
            'quantityReturned' => 'nullable|integer|min:0',
            'unitPrice' => 'required|numeric|min:0',

            /**
             * Validate order_items as well since its only one payload
             */
            'orderItems' => 'required|array|min:1',
            'orderItems.*.productId' => 'required|uuid|exists:products,id',
            'orderItems.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function toSnakeCase(): array
    {
        $data = $this->validated();
        $snake = [
            'order_id' => $data['orderId'] ?? null,
            'product_id' => $data['productId'] ?? null,
            'product_name' => $data['productName'] ?? null,
            'product_sku' => $data['productSku'] ?? null,
            'variant_name' => $data['variantName'] ?? null,
            'variant_sku' => $data['variantSku'] ?? null,
            'product_attributes' => $data['productAttributes'] ?? null,
            'quantity' => $data['quantity'] ?? null,
            'unit_price' => $data['unitPrice'] ?? $data['unit_price'] ?? null,
            'line_total' => $data['lineTotal'] ?? $data['line_total'] ?? null,
            'discount_amount' => $data['discountAmount'] ?? $data['discount_amount'] ?? null,
            'customization' => $data['customization'] ?? null,
            'customization_notes' => $data['customizationNotes'] ?? $data['customization_notes'] ?? null,
            'fulfillment_status' => $data['fulfillmentStatus'] ?? $data['fulfillment_status'] ?? null,
            'quantity_shipped' => $data['quantityShipped'] ?? $data['quantity_shipped'] ?? null,
            'quantity_returned' => $data['quantityReturned'] ?? $data['quantity_returned'] ?? null,
        ];

        // Map nested orderItems
        if (!empty($data['orderItems'])) {
            $snake['order_items'] = collect($data['orderItems'])->map(function ($item) {
                return [
                    'product_id' => $item['productId'] ?? null,
                    'quantity' => $item['quantity'] ?? null,
                    'unit_price' => $item['unitPrice'] ?? null,
                ];

            })->toArray();
        }
        return $snake;

    }
}
