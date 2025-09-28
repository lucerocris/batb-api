<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
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
            'productID' => $this->product_id,
            'name' => $this->name,
            'sku' => $this->sku,
            'priceAdjustment' => $this->price_adjustment,
            'stockQuantity' => $this->stock_quantity,
            'reservedQuantity' => $this->reserved_quantity,
            'attributes' => $this->attributes,
            'imageUrl' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'color' => $this->color,
            'isActive' => $this->is_active,
            'sortOrder' => $this->sort_order,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,

            'orderItems' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'inventoryLogs' => InventoryMovementResource::collection($this->whenLoaded('inventoryLogs'))
        ];
    }
}
