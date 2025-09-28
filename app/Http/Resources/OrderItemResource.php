<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'orderId' => $this->order_id,
            'productId' => $this->product_id,
            'productVariantId' => $this->product_variant_id,
            'productName' => $this->product_name,
            'productSku' => $this->product_sku,
            'variantName' => $this->variant_name,
            'variantSku' => $this->variant_sku,
            'productAttributes' => $this->product_attributes,
            'quantity' => $this->quantity,
            'unitPrice' => $this->unit_price,
            'lineTotal' => $this->line_total,
            'discountAmount' => $this->discount_amount,
            'customization' => $this->customization,
            'customizationNotes' => $this->customization_notes,
            'fulfillmentStatus' => $this->fulfillment_status,
            'quantityShipped' => $this->quantity_shipped,
            'quantityReturned' => $this->quantity_returned,
            
            // Nested resources
            'product' => new ProductResource($this->whenLoaded('product')),
            'order' => new OrderResource($this->whenLoaded('order')),
            'product_variant' => new ProductVariantResource($this->whenLoaded('productVariant'))
        ];
    }
}
