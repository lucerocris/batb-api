<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Validator;
use App\Validation\InventoryMovementValidation;

class InventoryMovementService{
    /**
     * These methods call the product model to update actual stocks
     */
    public function productUpdateStockLog(string $productId, string $adjustmentType, int $quantity){
        try{
            $product = Product::findOrFail($productId);
            if(Str::lower($adjustmentType) === "increase"){
                $product->stock_quantity += $quantity;
            }else if(Str::lower($adjustmentType) === "decrease"){
                if($product->stock_quantity  < $quantity){
                    throw new \Exception('There is insufficient stocks for this request');
                }
                $product->stock_quantity -= $quantity;
            }else {
                throw new \Exception('adjustment type value unknown');
            }
            $product->save();
        }catch(ModelNotFoundException $e){
            throw new \Exception('No Product was found');
        }
    }


    /**
     * Method To Create inventoryMovement Payload for orderItem
     * 
     * REFACTOR SOON
     */
    public function orderItemPayloadGenerator(array $orderItem, string $orderId, ?string $userId = null, string $adjustmentType, string $type)
    {
        return [
            'product_id'         => $orderItem['product_id'],
            'product_variant_id' => $orderItem['product_variant_id'] ?? null,
            'quantity'           => $orderItem['quantity'],
            'adjustment_type'    => $adjustmentType,
            'type'               => $type,
            'order_id'           => $orderId,
            'user_id'            => $userId,
            'created_at'         => now(),
            'updated_at'         => now(),
        ];
    }

    public function newProductPayloadGenerator(array $productDetails){
        return [
            'product_id'            => $productDetails['product_id'],
            'quantity'              => $productDetails['quantity'],
            'adjustment_type'       => 'increase',
            'initial_quantity'      => 0,
            'type'                  => 'creation',
            'created_at'            => now(),
            'updated_at'            => now(),
        ];
    }


    public function adjustStockAndLog(array $data): InventoryMovement
    {
        $data = InventoryMovementValidation::prepare($data);

        $validator = Validator::make($data, InventoryMovementValidation::rules());

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $validated = $validator->validated();
        return DB::transaction(function () use ($validated) {
            // Create inventory movement record
            
            $movement = InventoryMovement::create($validated);

            // Adjust stock
            $this->productUpdateStockLog(
                $validated['product_id'],
                $validated['adjustment_type'],
                $validated['quantity']
            );

            return $movement;
        });
    }

    public function newProductLog(array $productDetails){
        $productLog = $this->newProductPayloadGenerator($productDetails);
        InventoryMovement::create($productLog);
    } 

}