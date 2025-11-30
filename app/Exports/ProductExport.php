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
        $query = Product::with(['category'])
            ->when($this->filters['active_only'] ?? false, function ($query) {
                return $query->where('is_active', true);
            });

        $products = $query->get();

        return $products;
    }


    public function headings(): array
    {
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
