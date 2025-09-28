<?php

namespace App\Services;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Log;
use App\Services\OrderItemAutoFillService;

class OrderCalculationService{
    /**
     * Calculate line total for order items and return processed items
     */
    public function processOrderItems(array $orderItems, OrderItemAutoFillService $fill)
    {
        return array_map(function($item) use ($fill){
            
            $item = $fill->fillProductData($item);
            
            // Calculate line total
            $item['line_total'] = $item['unit_price'] * $item['quantity'];

            return $item;
        }, $orderItems);
    }

     /**
     * Calculate subtotal from order items
     */
    public function calculateSubtotal(array $orderItems): float
    {
        return array_sum(array_column($orderItems, 'line_total'));
    }

     /**
     * Calculate total amount including shipping and tax
     */
    public function calculateTotal(float $subtotal, float $shippingAmount = 0, float $taxAmount = 0): float
    {
        return $subtotal + $shippingAmount + $taxAmount;
    }

    
    /**
     * Process complete order calculations
     */
    public function calculateOrderTotals(array $orderItems, float $shippingAmount = 0, float $taxAmount = 0, OrderItemAutoFillService $fill): array
    {
        $processedItems = $this->processOrderItems($orderItems, $fill);
        $subtotal = $this->calculateSubtotal($processedItems);
        $total = $this->calculateTotal($subtotal, $shippingAmount, $taxAmount);

        return [
            'processed_items' => $processedItems,
            'subtotal' => $subtotal,
            'total' => $total
        ];
    }

}