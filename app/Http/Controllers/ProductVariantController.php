<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Http\Resources\ProductVariantResource;
use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use App\Services\InventoryMovementService;
use App\Services\FileUploadService; // Add this import
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Services\OriginalProductService;

class ProductVariantController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $variants = ProductVariant::with(['orderItems', 'product', 'inventoryLogs'])->get();
        return ProductVariantResource::collection($variants);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreProductVariantRequest $request, 
        InventoryMovementService $service, 
        OriginalProductService $original,
        FileUploadService $fileUploadService 
    ) {
        $variant = DB::transaction(function () use ($request, $service, $original) {
            $product = Product::with('productVariants')->findOrFail($request->product_id);

            if ($product->productVariants()->count() === 0) {
                $original->makeOriginalVariant($product);
            }

            $validated = ProductVariant::create($request->toSnakeCase());
            $inventoryLog = [
                'product_id' => $validated->product_id,
                'product_variant_id' => $validated->id,
                'quantity' => $validated->stock_quantity,
            ];
            $service->newProductVariantLog($inventoryLog);

            return $validated;
        });

        if ($request->hasFile('image')) {
            $storedPath = $fileUploadService->handleProductVariantImage($variant, $request->file('image'));
            if ($storedPath) {
                $variant->image_path = $storedPath;
                $variant->save();
            }
        }

        return new ProductVariantResource($variant);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductVariant $productVariant)
    {
        return new ProductVariantResource($productVariant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateProductVariantRequest $request, 
        ProductVariant $productVariant,
        FileUploadService $fileUploadService 
    ) {
        $validated = $request->toSnakeCase();
        $productVariant->update($validated);

        if ($request->hasFile('image')) {
            $storedPath = $fileUploadService->replaceImage(
                $productVariant->image_path,
                fn() => $fileUploadService->handleProductVariantImage($productVariant, $request->file('image'))
            );
            if ($storedPath) {
                $productVariant->image_path = $storedPath;
                $productVariant->save();
            }
        }

        return response()->json([
            'message' => 'Product variant updated successfully!',
            'data' => new ProductVariantResource($productVariant),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductVariant $productVariant)
    {
        $productVariant->delete();

        return response()->json(['message' => 'Product variant deleted successfully.']);
    }

}
