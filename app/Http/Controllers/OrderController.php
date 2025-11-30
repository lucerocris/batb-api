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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        // Allowed includes (relations only).
        $allowedIncludes = [
            'user',
            'verifiedBy',
            'orderItems',
            'orderItems.product',
        ];

        $requested = collect(explode(',', (string) $request->query('include', '')))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->unique()
            ->values();

        $with = [];
        if ($requested->isNotEmpty()) {
            foreach ($requested as $inc) {
                if ($inc === 'designs') { // pseudo-include: no relation to load
                    continue;
                }
                if (in_array($inc, $allowedIncludes, true)) {
                    // expanding shorthand: if 'orderItems' is requested, also load common nested relations
                    if ($inc === 'orderItems') {
                        $with[] = 'orderItems';
                        // auto-include common nested relations for items
                        $with[] = 'orderItems.product';
                    } else {
                        $with[] = $inc;
                    }
                }
            }
        }

        // Default eager loads when include is not provided
        if (empty($with)) {
            $with = [
                'user',
                'verifiedBy',
                'orderItems.product',
            ];
        }

        $query = Order::with(array_values(array_unique($with)));

        // Add filtering options
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Date range filtering
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Ordering
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $allowedSortFields = ['created_at', 'order_date', 'total_amount', 'status', 'order_number'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     *
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

            $existing = Order::where('idempotency_key', $orderData['idempotency_key'])->first();
            if ($existing) {
                return response()->json([
                    'message' => 'Duplicate request. Returning existing order.',
                    'order_id' => $existing->id
                ], 200);
            }

            $order = Order::create($orderData);


            $calculations = $orderCalculationService->calculateOrderTotals(
                $orderData['order_items'],
                $orderItemAutoFillService,
                $orderData['shipping_amount'] ?? 0.0,
                $orderData['tax_amount'] ?? 0.0
            );

            // Batch insert order items for better performance
            $orderItemsData = [];
            $movementsData = [];
            $now = now();

            foreach ($calculations['processed_items'] as $orderItem) {
                // Remove non-table fields before insert
                $cleanItem = Arr::except($orderItem, ['name', 'image_url']);

                // Prepare order item data for bulk insert
                $orderItemsData[] = array_merge($cleanItem, [
                    'order_id' => $order->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Prepare inventory movement data
                $movementsData[] = $inventoryMovementService->orderItemPayloadGenerator(
                    $orderItem,
                    $order->id,
                    $order->user_id,
                    "decrease",
                    "purchase"
                );
            }

            // Bulk insert order items (single query instead of N queries)
            if (!empty($orderItemsData)) {
                OrderItem::insert($orderItemsData);
            }

            // Process inventory movements (still need validation, so process individually)
            foreach ($movementsData as $movementPayload) {
                $inventoryMovementService->adjustStockAndLog($movementPayload);
            }

            // Combine all order updates into a single update query
            $updateData = [
                'subtotal'     => $calculations['subtotal'],
                'total_amount' => $calculations['total']
            ];

            $storedPath = null;
            if ($request->hasFile('image')) {
                $storedPath = $fileUploadService->handleOrderPaymentImage($order, $request->file('image'));
                if ($storedPath) {
                    $updateData['image_path'] = $storedPath;
                }
            }

            // Single update instead of multiple save/update calls
            $order->update($updateData);

            $order->load('orderItems');

            // Queue email instead of sending synchronously (improves response time)
            DB::afterCommit(function () use ($order) {
                Mail::to($order->email)->queue(new OrderReceiptMail($order));
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
    public function show(Order $order, Request $request)
    {
        // Allowed includes similar to index
        $allowedIncludes = [
            'user',
            'verifiedBy',
            'orderItems',
            'orderItems.product',
        ];

        $requested = collect(explode(',', (string) $request->query('include', '')))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->unique()
            ->values();

        $with = [];
        if ($requested->isNotEmpty()) {
            foreach ($requested as $inc) {
                if ($inc === 'designs') { // pseudo-include
                    continue;
                }
                if (in_array($inc, $allowedIncludes, true)) {
                    if ($inc === 'orderItems') {
                        $with[] = 'orderItems';
                        $with[] = 'orderItems.product';
                    } else {
                        $with[] = $inc;
                    }
                }
            }
        }

        if (empty($with)) {
            $with = [
                'user',
                'verifiedBy',
                'orderItems.product',
            ];
        }

        $order->load(array_values(array_unique($with)));
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

