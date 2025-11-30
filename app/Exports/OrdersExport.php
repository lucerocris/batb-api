<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;


    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Order::with(['user', 'orderItems.product'])
            ->when($this->filters['status'] ?? false, function ($query) {
                return $query->where('status', $this->filters['status']);
            })
            ->when($this->filters['date_from'] ?? false, function ($query) {
                return $query->whereDate('order_date', '>=', $this->filters['date_from']);
            })
            ->when($this->filters['date_to'] ?? false, function ($query) {
                return $query->whereDate('order_date', '<=', $this->filters['date_to']);
            })
            ->orderBy('order_date', 'desc');

        $orders = $query->get();

        if ($this->filters['include_items'] ?? false) {
            return $this->flattenWithItems($orders);
        }

        return $orders;
    }

    protected function flattenWithItems(Collection $orders)
    {
        $flattened = collect();

        foreach ($orders as $order) {
            if ($order->orderItems->count() > 0) {
                foreach ($order->orderItems as $item) {
                    $flattened->push((object) [
                        'order' => $order,
                        'item' => $item,
                    ]);
                }
            } else {
                $flattened->push((object) [
                    'order' => $order,
                    'item' => null,
                ]);
            }
        }

        return $flattened;
    }

    public function headings(): array
    {
        if ($this->filters['include_items'] ?? false) {
            return [
                'Type',
                'Order ID',
                'Order Number',
                'Customer Email',
                'Customer Name',
                'Status',
                'Payment Status',
                'Fulfillment Status',
                'Payment Method',
                'Subtotal',
                'Tax Amount',
                'Shipping Amount',
                'Discount Amount',
                'Total Amount',
                'Currency',
                'Item ID',
                'Product Name',
                'Product SKU',
                'Quantity',
                'Unit Price',
                'Total Price',
                'Shipping Address',
                'Payment Reference',
                'Order Date',
                'Payment Due Date',
                'Payment Verified Date',
                'Created At',
                'Updated At'
            ];
        }

        return [
            'Order ID',
            'Order Number',
            'Customer Email',
            'Customer Name',
            'Status',
            'Payment Status',
            'Fulfillment Status',
            'Payment Method',
            'Subtotal',
            'Tax Amount',
            'Shipping Amount',
            'Discount Amount',
            'Total Amount',
            'Refunded Amount',
            'Currency',
            'Tip',
            'Shipping Address',
            'Billing Address',
            'Payment Reference',
            'Customer Notes',
            'Admin Notes',
            'Reminder Count',
            'Last Reminder Sent',
            'Order Date',
            'Payment Due Date',
            'Payment Sent Date',
            'Payment Verified Date',
            'Expires At',
            'Created At',
            'Updated At'
        ];
    }

    public function map($row): array
    {
        if ($this->filters['include_items'] ?? false) {
            $order = $row->order;
            $item = $row->item;

            return [
                'Order',
                $order->id,
                $order->order_number,
                $order->email,
                $order->user->name ?? 'Guest',
                ucfirst(str_replace('_', ' ', $order->status)),
                ucfirst(str_replace('_', ' ', $order->payment_status)),
                ucfirst(str_replace('_', ' ', $order->fulfillment_status)),
                $order->payment_method ? ucfirst(str_replace('_', ' ', $order->payment_method)) : '',
                $order->subtotal,
                $order->tax_amount,
                $order->shipping_amount,
                $order->discount_amount,
                $order->total_amount,
                $order->currency,
                $item->id ?? '',
                $item->product->name ?? '',
                $item->product->sku ?? '',
                $item->quantity ?? '',
                $item->unit_price ?? '',
                $item->total_price ?? '',
                $this->formatAddress($order->shipping_address),
                $order->payment_reference,
                $order->order_date->format('Y-m-d H:i:s'),
                $order->payment_due_date?->format('Y-m-d H:i:s'),
                $order->payment_verified_date?->format('Y-m-d H:i:s'),
                $order->created_at->format('Y-m-d H:i:s'),
                $order->updated_at->format('Y-m-d H:i:s')
            ];
        }

        return [
            $row->id,
            $row->order_number,
            $row->email,
            $row->user->name ?? 'Guest',
            ucfirst(str_replace('_', ' ', $row->status)),
            ucfirst(str_replace('_', ' ', $row->payment_status)),
            ucfirst(str_replace('_', ' ', $row->fulfillment_status)),
            $row->payment_method ? ucfirst(str_replace('_', ' ', $row->payment_method)) : '',
            $row->subtotal,
            $row->tax_amount,
            $row->shipping_amount,
            $row->discount_amount,
            $row->total_amount,
            $row->refunded_amount,
            $row->currency,
            $row->tip,
            $this->formatAddress($row->shipping_address),
            $this->formatAddress($row->billing_address),
            $row->payment_reference,
            $row->customer_notes,
            $row->admin_notes,
            $row->reminder_sent_count,
            $row->last_reminder_sent?->format('Y-m-d H:i:s'),
            $row->order_date->format('Y-m-d H:i:s'),
            $row->payment_due_date?->format('Y-m-d H:i:s'),
            $row->payment_sent_date?->format('Y-m-d H:i:s'),
            $row->payment_verified_date?->format('Y-m-d H:i:s'),
            $row->expires_at?->format('Y-m-d H:i:s'),
            $row->created_at->format('Y-m-d H:i:s'),
            $row->updated_at->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Format address JSON to readable string
     */
    protected function formatAddress($address)
    {
        if (!$address) {
            return '';
        }

        $addressData = is_string($address) ? json_decode($address, true) : $address;

        if (!is_array($addressData)) {
            return '';
        }

        $parts = [];

        $fields = ['street', 'city', 'state', 'postal_code', 'country'];

        foreach ($fields as $field) {
            if (!empty($addressData[$field])) {
                $parts[] = $addressData[$field];
            }
        }

        return implode(', ', $parts);
    }
}
