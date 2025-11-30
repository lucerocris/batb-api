<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class StoreCartRequest extends FormRequest
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
            'product_id' => [
                'required',
                'uuid',
                'exists:products,id',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:99'
            ],
            'size' => [
                'nullable',
                'string',
                'max:50'
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product.',
            'product_id.uuid' => 'Invalid product ID format.',
            'product_id.exists' => 'The selected product does not exist.',
            'quantity.required' => 'Please specify a quantity.',
            'quantity.integer' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Maximum quantity is 99.',
            'size.max' => 'Size cannot exceed 50 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('product_id')) {
                $product = Product::find($this->product_id);

                // Check if product exists and is available
                if ($product) {
                    if (!$product->is_active) {
                        $validator->errors()->add(
                            'product_id',
                            'This product is currently unavailable.'
                        );
                    }

                    if ($product->stock_status !== 'available') {
                        $validator->errors()->add(
                            'product_id',
                            'This product is out of stock.'
                        );
                    }
                }
            }
        });
    }

    /**
     * Get validated product.
     */
    public function getProduct(): ?Product
    {
        return Product::find($this->product_id);
    }

    /**
     * Get the price to use for cart.
     */
    public function getPrice(): float
    {
        $product = $this->getProduct();
        return $product ? ($product->sale_price ?? $product->base_price) : 0;
    }
}
