<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use App\Http\Resources\InventoryMovementResource;
use App\Http\Requests\StoreInventoryMovementRequest;
use App\Http\Requests\UpdateInventoryMovementRequest;
use App\Services\InventoryMovementService;
use Illuminate\Support\Facades\DB;

class InventoryMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movements = InventoryMovement::with(['user','order','product', 'productVariant'])->get();

        return InventoryMovementResource::collection($movements);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryMovementRequest $request, InventoryMovementService $service)
    {
        $service->adjustStockAndLog($request->validated());

        return response()->json([
            'message' => 'Inventory Log Created and Stock Adjusted'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryMovement $inventoryMovement)
    {
        return new InventoryMovementResource($inventoryMovement);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventoryMovementRequest $request, InventoryMovement $inventoryMovement)
    {
        $inventoryMovement->update($request->toSnakeCase());

        return response()->json([
            'message' => 'Inventory movement updated successfully!',
            'data' => new InventoryMovementResource($inventoryMovement)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryMovement $inventoryMovement)
    {
        $inventoryMovement->delete();

        return response()->json([
            'message' => 'Inventory movement deleted successfully.'
        ]);
    }
}

