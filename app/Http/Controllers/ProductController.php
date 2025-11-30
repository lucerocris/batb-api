<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\FileUploadService;
use App\Services\InventoryMovementService;
use App\Services\OriginalProductService;
use App\Services\ProductSKUGenerator;
use App\Traits\HandlesFileUpload;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ProductCreationService;

class ProductController extends Controller
{
    use HandlesFileUpload;

    public function __construct(
        private FileUploadService $fileUploadService,
        private ProductCreationService $creationService
    ) {}


    public function getRouteKeyName()
    {
        return 'id';
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category'])->get();
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $productData = $request->toSnakeCase();
        $variantsData = $productData['product_variants'] ?? [];
        unset($productData['product_variants']);

        $product = $this->creationService->createProduct($productData, $variantsData);

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {

        $product->load(['category']);
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(UpdateProductRequest $request, Product $product)
     {
         $data = $request->toSnakeCase();
         
         $this->handleProductImage($product, $data['image'] ?? null);
         $this->updateProductFields($product, $data);
         
         return new ProductResource($product);
     }

     private function handleProductImage(Product $product, $image): void
     {
         if (!$image instanceof \Illuminate\Http\UploadedFile) {
             return;
         }
 
         $storedPath = $this->fileUploadService->replaceImage(
             $product->image_path,
             fn() => $this->fileUploadService->handleProductImage($product, $image)
         );
 
         if ($storedPath) {
             $product->image_path = $storedPath;
             $product->save();
         }
     }
 
     /**
      * Update product fields excluding variants and image
      */
     private function updateProductFields(Product $product, array $data): void
     {
         $productUpdateData = collect($data)->except(['product_variants', 'image'])->toArray();
         $product->update($productUpdateData);
     }

     /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }

    public function showAll(){

        $products = Product::withTrashed()
            ->with(['category'])->get();

        return ProductResource::collection($products);
    }

    public function trashed(){

        $products = Product::onlyTrashed()->with(['category'])->get();

        return response()->json($products);

    }

    public function restoreProduct($id){

        $product = Product::withTrashed()->findOrFail($id);


        if(!$product->trashed()){
            return response()->json(['message' => 'Product not deleted']);
        }

        $product->restore();

        return response()->json(['message' => 'Product restored']);
    }

    public function addProduct(StoreProductRequest $request)
    {
        $productData = $request->toSnakeCase();
        $variantsData = $productData['product_variants'] ?? [];
        unset($productData['product_variants']);

        $product = $this->creationService->createProduct($productData, $variantsData);

        return new ProductResource($product);
    }

}