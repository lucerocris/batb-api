<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Validation\InventoryMovementValidation;


class StoreInventoryMovementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {

        $this->merge([
            'product_id'         => $this->input('productId'),
            'product_variant_id' => $this->input('productVariantId'),
            'order_id'           => $this->input('orderId'),
            'user_id'            => $this->input('userId'),
            'type'               => $this->input('type'),
            'adjustment_type'    => $this->input('adjustmentType'),
            'quantity'           => $this->input('quantity'),
            'initial_quantity'   => $this->input('initialQuantity'),
            'notes'              => $this->input('notes'),
            'reference'          => $this->input('reference'),
            'meta_data'          => $this->input('metaData'),
        ]);


        $this->merge(
            InventoryMovementValidation::prepare($this->all())
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return InventoryMovementValidation::rules();
    }
}
