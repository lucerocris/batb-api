<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderItemResource;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Http\Requests\StoreOrderItemRequest;
use App\Http\Requests\UpdateOrderItemRequest;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderItems = OrderItem::with(['product', 'order', 'productVariant'])->get();
        return OrderItemResource::collection($orderItems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderItemRequest $request)
    {
        $orderItem = OrderItem::create($request->toSnakeCase());
        return response()->json([
            'message' => 'Order item created successfully!',
            'data' => $orderItem
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderItem $orderItem)
    {
        $orderItem->load(['product', 'order', 'productVariant']);
        return new OrderItemResource($orderItem);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderItemRequest $request, OrderItem $orderItem)
    {
        $orderItem->update($request->toSnakeCase());

        return response()->json([
            'message' => 'Order item updated successfully!',
            'data' => new OrderItemResource($orderItem)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderItem $orderItem)
    {
        $orderItem->delete();

        return response()->json(['message' => 'Order item deleted successfully!']);
    }

}
