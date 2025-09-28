<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Services\FileUploadService; // Add this import
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    // Remove the HandlesFileUpload trait usage
    // use HandlesFileUpload; // Remove this line

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with(['products'])->get();
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreCategoryRequest $request,
        FileUploadService $fileUploadService 
    ) {
        $validated = $request->validated();
        $category = Category::create($validated);

        if ($request->hasFile('image')) {
            $storedPath = $fileUploadService->handleCategoryImage($category, $request->file('image'));
            if ($storedPath) {
                $category->update(['image_path' => $storedPath]);
            }
        }

        return response()->json([
            'message' => 'Category created successfully!',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateCategoryRequest $request, 
        Category $category,
        FileUploadService $fileUploadService 
    ) {
        $validated = $request->validated();
        $oldName = $category->name;
        $newName = $validated['name'] ?? $oldName;

        if ($request->hasFile('image')) {
            $storedPath = $fileUploadService->handleCategoryImage($category, $request->file('image'), true);
            $validated['image_path'] = $storedPath;
            
            if ($category->image_path && $category->image_path !== $storedPath) {
                $fileUploadService->replaceImage($category->image_path, fn() => null);
            }
        } else {
            if ($oldName !== $newName && $category->image_path) {
                $movedPath = $fileUploadService->moveCategoryImage($category, $oldName, $newName);
                if ($movedPath) {
                    $validated['image_path'] = $movedPath;
                }
            }
        }

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully!',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.']);
    }

    public function showAll(){

        $categories = Category::withTrashed()
            ->with(['products'])->get();

        return CategoryResource::collection($categories);

    }

    public function trashed(){

       $categories = Category::onlyTrashed()->with(['products'])->get();

        return response()->json($categories);

    }

    public function restoreCategory($id){
        $category = Category::withTrashed()->findOrFail($id);


        if(!$category->trashed()){
            return response()->json(['message' => 'Category not deleted']);

        }
        $category->restore();

        return response()->json(['message' => 'Category restored']);

    }
}
