<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderItemRequest extends FormRequest
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
            'quantity' => 'sometimes|integer|min:1',
            'unitPrice' => 'sometimes|numeric|min:0',
            'lineTotal' => 'sometimes|numeric|min:0',
            'discountAmount' => 'nullable|numeric|min:0',
            'customization' => 'nullable|string',
            'customizationNotes' => 'nullable|string',
            'fulfillmentStatus' => 'nullable|string',
            'quantityShipped' => 'nullable|integer|min:0',
            'quantityReturned' => 'nullable|integer|min:0'
        ];
    }

    public function toSnakeCase(): array
    {
        $data = $this->validated();

        return array_filter([
        'quantity'           => $data['quantity'] ?? null,
        'unit_price'         => $data['unitPrice'] ?? null,
        'line_total'         => $data['lineTotal'] ?? null,
        'discount_amount'    => $data['discountAmount'] ?? null,
        'customization'      => $data['customization'] ?? null,
        'customization_notes'=> $data['customizationNotes'] ?? null,
        'fulfillment_status' => $data['fulfillmentStatus'] ?? null,
        'quantity_shipped'   => $data['quantityShipped'] ?? null,
        'quantity_returned'  => $data['quantityReturned'] ?? null,
        ], fn($value) => $value !== null);
    }

}
