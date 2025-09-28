<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'role' => $this->role,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'phoneNumber' => $this->phone_number,
            'dateOfBirth' => $this->date_of_birth,
            'username' => $this->username,
            'totalOrders' => $this->total_orders,
            'totalSpent' => $this->total_spent,
            'passwordChangedAt' => $this->password_changed_at,
            'failedLoginAttempts' => $this->failed_login_attempts,
            'lockedUntil' => $this->locked_until,
            'imageUrl' => $this->image_path
                ? asset('storage/' . $this->image_path)
                : null,
            'createdAt' => $this->created_at,

            'orders' => OrderResource::collection($this->whenLoaded('orders')),
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
        ];
    }
}
