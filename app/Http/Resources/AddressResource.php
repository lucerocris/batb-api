<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'type' => $this->type,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'company' => $this->company,
            'addressLine1' => $this->address_line_1,
            'addressLine2' => $this->address_line_2,
            'city' => $this->city,
            'stateProvince' => $this->state_province,
            'postalCode' => $this->postal_code,
            'countryCode' => $this->country_code,
            'phone' => $this->phone,
            'isDefaultShipping' => (bool) $this->is_default_shipping,
            'isDefaultBilling' => (bool) $this->is_default_billing,
            'isActive' => (bool) $this->is_active,
            'isValidated' => (bool) $this->is_validated,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,


        ];
    }
}
