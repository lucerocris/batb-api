<?php

namespace App\Http\Controllers;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Resources\AddressResource;
use App\Http\Requests\StoreAddressRequest;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $address = Address::with(['user'])->get();
        return AddressResource::collection($address);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddressRequest $request)
    {


        $address = Address::create($request->toSnakeCase());

        return new AddressResource($address);
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        return new AddressResource($address);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address)
    {

        $address->update($request->toSnakeCase());

        return response()->json([
            'message' => 'Address updated successfully!',
            'data' => new AddressResource($address)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        $address->delete();
        return response()->json(['message' => 'Address deleted successfully']);
    }
}
