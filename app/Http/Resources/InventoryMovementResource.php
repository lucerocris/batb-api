<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryMovementResource extends JsonResource
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
            'product' => new ProductResource($this->whenLoaded('product')),
            'order' => new OrderResource($this->whenLoaded('order')),
            'user' => new UserResource($this->whenLoaded('user')),
            'type' => $this->type,
            'quantity' => $this->quantity,
            'initialQuantity' => $this->initial_quantity, 
            'notes' => $this->notes,
            'reference' => $this->reference,
            'metaData' => $this->meta_data,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
