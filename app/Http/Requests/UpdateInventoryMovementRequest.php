<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryMovementRequest extends FormRequest
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
            'productId' => 'sometimes|uuid|exists:products,id',
            'productVariantId' => 'sometimes|integer|exists:product_variants,id',
            'orderId' => 'sometimes|nullable|uuid|exists:orders,id',
            'userId' => 'sometimes|uuid|exists:users,id',
            'type' => 'sometimes|string|in:purchase,sale,adjustment,return,transfer',
            'quantityChange' => 'sometimes|integer',
            'quantityBefore' => 'sometimes|integer',
            'quantityAfter' => 'sometimes|integer',
            'notes' => 'sometimes|string|max:1000',
            'reference' => 'sometimes|string|max:255',
            'metaData' => 'sometimes|array',
        ];
    }

    public function toSnakeCase(): array
    {
    $data = $this->validated();

    return [
        'product_id'       => $data['productId'] ?? null,
        'product_variant_id' => $data['productVariantId'] ?? null,
        'order_id'         => $data['orderId'] ?? null,
        'user_id'          => $data['userId'] ?? null,
        'type'             => $data['type'] ?? null,
        'quantity_change'  => $data['quantityChange'] ?? null,
        'quantity_before'  => $data['quantityBefore'] ?? null,
        'quantity_after'   => $data['quantityAfter'] ?? null,
        'notes'            => $data['notes'] ?? null,
        'reference'        => $data['reference'] ?? null,
        'meta_data'        => $data['metaData'] ?? null,
        ];
    }

}
