<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('product')?->id;

        return [
            'categoryId' => 'sometimes|integer|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:products,slug,' . $productId . ',id',
            'description' => 'nullable|string',
            'shortDescription' => 'nullable|string',
            'sku' => 'sometimes|string|max:255|unique:products,sku,' . $productId . ',id',
            'basePrice' => 'sometimes|numeric|min:0',
            'salePrice' => 'nullable|numeric|min:0',
            'costPrice' => 'nullable|numeric|min:0',
            'stockQuantity' => 'integer|min:0',
            'reservedQuantity' => 'integer|min:0',
            'lowStockThreshold' => 'integer|min:0',
            'trackInventory' => 'boolean',
            'allowBackorder' => 'boolean',
            'type' => 'sometimes|in:classic,premium',
            // Single file upload for product image
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'isActive' => 'sometimes|boolean',
            'isFeatured' => 'sometimes|boolean',
            'availableFrom' => 'nullable|date',
            'availableUntil' => 'nullable|date|after_or_equal:available_from',
            'metaTitle' => 'nullable|string|max:255',
            'metaDescription' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'viewCount' => 'nullable|integer|min:0',
            'purchaseCount' => 'nullable|integer|min:0',
            'averageRating' => 'nullable|numeric|min:0|max:5',
            'reviewCount' => 'nullable|integer|min:0',

            'productVariants' => 'nullable|array',
            'productVariants.*.id' => 'sometimes|exists:product_variants,id',
            'productVariants.*.name' => 'required|string|max:255',
            'productVariants.*.priceAdjustment' => 'numeric|min:0',
            'productVariants.*.stockQuantity' => 'integer|min:0',
            'productVariants.*.reservedQuantity' => 'integer|min:0',
            'productVariants.*.attributes' => 'array',
            'productVariants.*.attributes.*' => 'string',
            'productVariants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'productVariants.*.isActive' => 'boolean',
            'productVariants.*.sortOrder' => 'integer|min:0',
        ];
   }

    public function toSnakeCase(): array
    {
        $data = $this->validated();

        return array_filter([
            'category_id'        => $data['categoryId'] ?? $data['category_id'] ?? null,
            'name'               => $data['name'] ?? null,
            'slug'               => $data['slug'] ?? null,
            'description'        => $data['description'] ?? null,
            'short_description'  => $data['shortDescription'] ?? $data['short_description'] ?? null,
            'sku'                => $data['sku'] ?? null,
            'base_price'         => $data['basePrice'] ?? $data['base_price'] ?? null,
            'sale_price'         => $data['salePrice'] ?? $data['sale_price'] ?? null,
            'cost_price'         => $data['costPrice'] ?? $data['cost_price'] ?? null,
            'stock_quantity'     => $data['stockQuantity'] ?? $data['stock_quantity'] ?? null,
            'reserved_quantity'  => $data['reservedQuantity'] ?? $data['reserved_quantity'] ?? null,
            'low_stock_threshold'=> $data['lowStockThreshold'] ?? $data['low_stock_threshold'] ?? null,
            'track_inventory'    => $data['trackInventory'] ?? $data['track_inventory'] ?? null,
            'allow_backorder'    => $data['allowBackorder'] ?? $data['allow_backorder'] ?? null,
            'type'               => $data['type'] ?? null,
            'image'              => $data['image'] ?? null,
            'is_active'          => $data['isActive'] ?? $data['is_active'] ?? null,
            'is_featured'        => $data['isFeatured'] ?? $data['is_featured'] ?? null,
            'available_from'     => $data['availableFrom'] ?? $data['available_from'] ?? null,
            'available_until'    => $data['availableUntil'] ?? $data['available_until'] ?? null,
            'meta_title'         => $data['metaTitle'] ?? $data['meta_title'] ?? null,
            'meta_description'   => $data['metaDescription'] ?? $data['meta_description'] ?? null,
            'tags'               => $data['tags'] ?? null,
            'view_count'         => $data['viewCount'] ?? $data['view_count'] ?? null,
            'purchase_count'     => $data['purchaseCount'] ?? $data['purchase_count'] ?? null,
            'average_rating'     => $data['averageRating'] ?? $data['average_rating'] ?? null,
            'review_count'       => $data['reviewCount'] ?? $data['review_count'] ?? null,

            'product_variants'   => collect($data['productVariants'] ?? [])->map(function ($variant) {
                return [
                    'id'                => $variant['id'] ?? null,
                    'name'              => $variant['name'],
                    'price_adjustment'  => $variant['priceAdjustment'] ?? 0,
                    'stock_quantity'    => $variant['stockQuantity'] ?? 0,
                    'reserved_quantity' => $variant['reservedQuantity'] ?? 0,
                    'attributes'        => $variant['attributes'] ?? [],
                    'image'             => $variant['image'] ?? null,
                    'is_active'         => $variant['isActive'] ?? true,
                    'sort_order'        => $variant['sortOrder'] ?? 0,
                ];
            })->toArray(),
        ], fn($value) => $value !== null);
    }

}
