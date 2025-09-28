<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    /**
     * Create a new class instance.
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Product::with(['productVariants', 'category'])
            ->when($this->filters['active_only'] ?? false, function ($query) {
                return $query->where('is_active', true);
            });

        $products = $query->get();

        if ($this->filters['include_variants'] ?? false) {
            return $this->flattenWithVariants($products);
        }

        return $products;
    }

    protected function flattenWithVariants(Collection $products)
    {
        $flattened = collect();

        foreach ($products as $product) {
            if ($product->productVariants->count() > 0) {
                foreach ($product->productVariants as $variant) {
                    $flattened->push((object) [
                        'product' => $product,
                        'variant' => $variant,
                    ]);
                }
            } else {
                $flattened->push((object) [
                    'product' => $product,
                    'variant' => null,
                ]);
            }
        }

        return $flattened;
    }

    public function headings(): array
    {
        if ($this->filters['include_variants'] ?? false) {
            return [
                'Type',
                'Product ID',
                'Product Name',
                'Product SKU',
                'Category',
                'Base Price',
                'Sale Price',
                'Stock Quantity',
                'Variant ID',
                'Variant Name',
                'Variant SKU',
                'Variant Price Adjustment',
                'Variant Stock',
                'Variant Attributes',
                'Is Active',
                'Is Featured',
                'Created At',
                'Updated At'
            ];
        }

        return [
            'ID',
            'Name',
            'SKU',
            'Category',
            'Base Price',
            'Sale Price',
            'Cost Price',
            'Stock Quantity',
            'Low Stock Threshold',
            'Is Active',
            'Is Featured',
            'Tags',
            'Created At',
            'Updated At'
        ];
    }

    public function map($row): array
    {
        if ($this->filters['include_variants'] ?? false) {
            $product = $row->product;
            $variant = $row->variant;

            return [
                $product->type,
                $product->id,
                $product->name,
                $product->sku,
                $product->category->name ?? '',
                $product->base_price,
                $product->sale_price,
                $product->stock_quantity,
                $variant->id ?? '',
                $variant->name ?? '',
                $variant->sku ?? '',
                $variant->price_adjustment ?? '',
                $variant->stock_quantity ?? '',
                $variant ? $variant->attributes : '',
                $product->is_active ? 'Yes' : 'No',
                $product->is_featured ? 'Yes' : 'No',
                $product->created_at->format('Y-m-d H:i:s'),
                $product->updated_at->format('Y-m-d H:i:s')
            ];
        }

        return [
            $row->id,
            $row->name,
            $row->sku,
            $row->category->name ?? '',
            $row->base_price,
            $row->sale_price,
            $row->cost_price,
            $row->stock_quantity,
            $row->low_stock_threshold,
            $row->is_active ? 'Yes' : 'No',
            $row->is_featured ? 'Yes' : 'No',
            $row->tags ? implode(', ', json_decode($row->tags)) : '',
            $row->created_at->format('Y-m-d H:i:s'),
            $row->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
