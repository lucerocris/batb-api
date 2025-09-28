<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
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
            'userId' => 'required|uuid|exists:users,id',
            'type' => 'required|in:shipping,billing,both',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'addressLine1' => 'required|string|max:255',
            'addressLine2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'stateProvince' => 'required|string|max:255',
            'postalCode' => 'required|string|max:20',
            'countryCode' => 'required|string|size:2',
            'phone' => 'nullable|string|max:20',
            'isDefaultShipping' => 'boolean',
            'isDefaultBilling' => 'boolean',
            'isActive' => 'boolean',
            'isValidated' => 'boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ];
    }

    public function toSnakeCase(): array
    {
    $data = $this->validated();

        return [
        'user_id'            => $data['userID'] ?? null,
        'type'               => $data['type'] ?? null,
        'first_name'         => $data['firstName'] ?? null,
        'last_name'          => $data['lastName'] ?? null,
        'company'            => $data['company'] ?? null,
        'address_line_1'     => $data['addressLine1'] ?? null,
        'address_line_2'     => $data['addressLine2'] ?? null,
        'city'               => $data['city'] ?? null,
        'state_province'     => $data['stateProvince'] ?? null,
        'postal_code'        => $data['postalCode'] ?? null,
        'country_code'       => $data['countryCode'] ?? null,
        'phone'              => $data['phone'] ?? null,
        'is_default_shipping'=> $data['isDefaultShipping'] ?? false,
        'is_default_billing' => $data['isDefaultBilling'] ?? false,
        'is_active'          => $data['isActive'] ?? true,
        'is_validated'       => $data['isValidated'] ?? false,
        'latitude'           => $data['latitude'] ?? null,
        'longitude'          => $data['longitude'] ?? null,
        ];
    }

}
