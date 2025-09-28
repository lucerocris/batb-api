<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVariantRequest extends FormRequest
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
            //
            'productId' => 'sometimes|uuid|exists:products,id',
            'name' => 'sometimes|string|max:255',
            'sku' => 'sometimes|string|max:255|unique:product_variants,sku,' . $this->id,
            'priceAdjustment' => 'sometimes|numeric',
            'stockQuantity' => 'sometimes|integer',
            'reservedQuantity' => 'sometimes|integer',
            'attributes' => 'sometimes|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'isActive' => 'sometimes|boolean',
            'sortOrder' => 'sometimes|integer',
        ];
    }

    public function toSnakeCase(): array
    {
        $data = $this->validated();

    return [
        'product_id'       => $data['productId'] ?? $data['product_id'] ?? null,
        'name'             => $data['name'] ?? null,
        'sku'              => $data['sku'] ?? null,
        'price_adjustment' => $data['priceAdjustment'] ?? $data['price_adjustment'] ?? null,
        'stock_quantity'   => $data['stockQuantity'] ?? $data['stock_quantity'] ?? null,
        'reserved_quantity'=> $data['reservedQuantity'] ?? $data['reserved_quantity'] ?? null,
        'attributes'       => $data['attributes'] ?? null,
        'image'            => $data['image'] ?? null,
        'is_active'        => $data['isActive'] ?? $data['is_active'] ?? null,
        'sort_order'       => $data['sortOrder'] ?? $data['sort_order'] ?? null,
        ];
    }

}
