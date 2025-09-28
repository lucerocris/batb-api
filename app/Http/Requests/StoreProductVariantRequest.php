<?php

namespace App\Http\Requests;

use App\Services\ProductVariantSKUGenerator;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(){
        if ($this->product_id) {
            $this->merge([
                'sku' => ProductVariantSKUGenerator::generateSKU($this->product_id)
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'priceAdjustment' => 'numeric',
            'stockQuantity' => 'required|integer|min:0',
            'reservedQuantity' => 'integer',
            'attributes' => 'required|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'isActive' => 'boolean',
        ];
    }

    public function toSnakeCase(): array
    {
         $data = $this->validated();

        return [
        'name'             => $data['name'] ?? null,
        'price_adjustment' => $data['priceAdjustment'] ?? 0,
        'stock_quantity'   => $data['stockQuantity'] ?? 0,
        'reserved_quantity'=> $data['reservedQuantity'] ?? 0,
        'attributes'       => $data['attributes'] ?? [],
        'image'            => $data['image'] ?? null,
        'is_active'        => $data['isActive'] ?? true,
        ];

    }
}