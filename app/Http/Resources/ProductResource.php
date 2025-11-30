<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'categoryID' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'shortDescription' => $this->short_description,
            'color' => $this->color,
            'sku' => $this->sku,
            'basePrice' => $this->base_price,
            'salePrice' => $this->sale_price,
            'costPrice' => $this->cost_price,
            'stockStatus' => $this->stock_status,
            'type' => $this->type,
            'brand' => $this->brand,
            'imageUrl' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'imageGallery' => collect($this->gallery_images ?? [])
                ->map(fn ($path) => asset('storage/' . ltrim($path, '/')))
                ->all(),
            'isActive' => (bool) $this->is_active,
            'isFeatured' => (bool) $this->is_featured,
            'tags' => $this->tags ?? [],
            'createdAt'=> $this->created_at,
            'productVariants' => ProductVariantResource::collection($this->whenLoaded('productVariants')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'orderItems' => OrderItemResource::collection($this->whenLoaded('orderItems'))
        ];
    }
}
