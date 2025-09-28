<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'imageUrl' => $this->image_path
                ? asset('storage/'.$this->image_path)
                : null,
            'sortOrder' => $this->sort_order,
            'isActive' => $this->is_active,
            'metaData' => $this->meta_data,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'productCount' => $this->whenLoaded('products', fn () => $this->products->count(), $this->products()->count()),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'imageUrl' => $this->image_path
                ? asset('storage/'.$this->image_path)
                : null, 
        ];
    }
}
