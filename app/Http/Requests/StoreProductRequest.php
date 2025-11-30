<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\ProductSKUGenerator;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * AUTO FILL PRODUCT SKU
     */
    protected function prepareForValidation(): void
    {
        if ($this->categoryId) {
            $this->merge([
                'sku' => ProductSKUGenerator::generateSKU($this->categoryId)
            ]);
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'categoryId' => 'required|integer|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'shortDescription' => 'nullable|string',
            'basePrice' => 'required|numeric|min:0',
            'salePrice' => 'nullable|numeric|min:0',
            'costPrice' => 'nullable|numeric|min:0',
            'stockQuantity' => 'required|integer|min:0',
            'reservedQuantity' => 'integer|min:0',
            'lowStockThreshold' => 'integer|min:0',
            'trackInventory' => 'boolean',
            'allowBackorder' => 'boolean',
            'type' => 'required|in:premium,classic',

            // Single file upload for product image
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'isActive' => 'boolean',
            'isFeatured' => 'boolean',
            'availableFrom' => 'nullable|date',
            'availableUntil' => 'nullable|date|after_or_equal:available_from',
            'metaTitle' => 'nullable|string|max:255',
            'metaDescription' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',

        ];
    }

    /**
     * Convert camelCase request data into snake_case DB fields
     */
    public function toSnakeCase(): array
    {
        $data = $this->validated();

        return [
            'category_id'         => $data['categoryId'],
            'name'                => $data['name'],
            'slug'                => $data['slug'],
            'description'         => $data['description'] ?? null,
            'short_description'   => $data['shortDescription'] ?? null,
            'base_price'          => $data['basePrice'],
            'sale_price'          => $data['salePrice'] ?? null,
            'cost_price'          => $data['costPrice'] ?? null,
            'stock_quantity'      => $data['stockQuantity'],
            'reserved_quantity'   => $data['reservedQuantity'] ?? 0,
            'low_stock_threshold' => $data['lowStockThreshold'] ?? 0,
            'track_inventory'     => $data['trackInventory'] ?? false,
            'allow_backorder'     => $data['allowBackorder'] ?? false,
            'type'                => $data['type'],
            'image'               => $data['image'] ?? null,
            'is_active'           => $data['isActive'] ?? true,
            'is_featured'         => $data['isFeatured'] ?? false,
            'available_from'      => $data['availableFrom'] ?? null,
            'available_until'     => $data['availableUntil'] ?? null,
            'meta_title'          => $data['metaTitle'] ?? null,
            'meta_description'    => $data['metaDescription'] ?? null,
            'tags'                => $data['tags'] ?? null,


        ];
    }
}
