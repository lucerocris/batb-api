<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Services\FileUploadService; 

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['orders', 'addresses'])->get();
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        Request $request,
        FileUploadService $fileUploadService 
    ) {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'in:admin,customer,manager',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'username' => 'nullable|string|max:255|unique:users,username',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $user = User::create($validated);
        if ($request->hasFile('image')) {
            $storedPath = $fileUploadService->handleUserImage($user, $request->file('image'));
            if ($storedPath) {
                $user->image_path = $storedPath;
                $user->save();
            }
        }
    
        return new UserResource($user);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['orders', 'addresses']);
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request, 
        User $user,
        FileUploadService $fileUploadService
    ) {
        $validated = $request->validate([
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:8',
            'role' => 'sometimes|in:admin,customer,manager',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'date_of_birth' => 'sometimes|nullable|date',
            'username' => 'sometimes|nullable|string|max:255|unique:users,username,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        if ($request->hasFile('image')) {
            $storedPath = $fileUploadService->replaceImage(
                $user->image_path,
                fn() => $fileUploadService->handleUserImage($user, $request->file('image'))
            );
            if ($storedPath) {
                $validated['image_path'] = $storedPath;
            }
        }
        
        $user->update($validated);
        
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {

        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}
