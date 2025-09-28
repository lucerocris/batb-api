<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderReceiptMail;
use App\Mail\AdminOrderNotificationMail;
use App\Http\Requests\UpdateOrderRequest;
use App\Services\OrderNumberGenerator;
use App\Models\OrderItem;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\OrderCalculationService;
use App\Services\OrderItemAutoFillService;
use App\Services\InventoryMovementService;

class OrderController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user','verifiedBy','orderItems'])->get();
        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     * LIMPYOHAN NI SOON
     * */
    public function store(
        StoreOrderRequest $request,
        OrderCalculationService $orderCalculationService,
        OrderItemAutoFillService $orderItemAutoFillService,
        InventoryMovementService $inventoryMovementService,
        FileUploadService $fileUploadService
    ) {
        return DB::transaction(function () use ($request, $orderCalculationService, $orderItemAutoFillService, $inventoryMovementService, $fileUploadService) {
            $orderData = $request->toSnakeCase();
            $order = Order::create($orderData);

            $calculations = $orderCalculationService->calculateOrderTotals(
                $orderData['order_items'],
                $orderData['shipping_amount'] ?? 0.0,
                $orderData['tax_amount'] ?? 0.0,
                $orderItemAutoFillService
            );

            foreach ($calculations['processed_items'] as $orderItem) {
                $order->orderItems()->create($orderItem);

                $movementPayload = $inventoryMovementService->orderItemPayloadGenerator(
                    $orderItem,
                    $order->id,
                    $order->user_id,
                    "decrease",
                    "purchase"
                );
            }

            $order->update([
                'subtotal'     => $calculations['subtotal'],
                'total_amount' => $calculations['total']
            ]);

            $storedPath = null;
            if ($request->hasFile('image')) {
                $storedPath = $fileUploadService->handleOrderPaymentImage($order, $request->file('image'));
                if ($storedPath) {
                    $order->image_path = $storedPath;
                    $order->save();
                }
            }

            $order->load('orderItems');

            DB::afterCommit(function () use ($order) {
                Mail::to($order->email)->send(new OrderReceiptMail($order));
            });

            return response()->json([
                'message' => 'Order created and receipt sent',
                'image_path' => $storedPath,
                'has_image' => $request->hasFile('image'),
                'order_id' => $order->id
            ]);
        });
    }



    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['orderItems']);
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order, InventoryMovementService $inventoryMovementService)
    {
        DB::transaction(function() use ($order, $inventoryMovementService, $request){
            $order->update($request->toSnakeCase());

            $order->load('orderItems');
            if (Str::lower($request->status) === "cancelled") {
                foreach ($order->orderItems as $orderItem) {
                    $orderItemPayload = $inventoryMovementService->orderItemPayloadGenerator(
                        $orderItem->toArray(),
                        $order->id,
                        $order->user_id,
                        "increase", // adjustmentType
                        "cancellation"
                    );

                    $inventoryMovementService->adjustStockAndLog($orderItemPayload);
                }
            }
        });

        return new OrderResource($order->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(['message' => 'Order successfully deleted']);
    }
}
