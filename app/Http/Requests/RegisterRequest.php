<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        if (!$this->filled('role')) {
        $this->merge(['role' => 'customer']);
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'in:admin,customer,manager',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
        ];
    }

    public function toSnakeCase(): array
    {
        $data = $this->validated();

        return [
        'email'      => $data['email'] ?? null,
        'password'   => $data['password'] ?? null,
        'role'       => $data['role'] ?? 'customer',
        'first_name' => $data['firstName'] ?? null,
        'last_name'  => $data['lastName'] ?? null,
    ];
    }
}
