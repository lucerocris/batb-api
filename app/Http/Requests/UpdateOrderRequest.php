<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'status' => 'nullable|string',
            'totalAmount' => 'nullable|decimal',
            'adminNotes' => 'nullable|string|max:2000',
        ];
    }

    public function toSnakeCase(): array
    {
        $data = $this->validated();

        return array_filter([
        'status'       => $data['status'] ?? null,
        'total_amount' => $data['totalAmount'] ?? null,
        'admin_notes'  => $data['adminNotes'] ?? null,
        ], fn($value) => $value !== null);
    }
}
