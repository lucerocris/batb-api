<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiriesRequest;
use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InquiriesController extends Controller
{
    /**
     * Store a newly created inquiry.
     */
    public function store(StoreInquiriesRequest $request): JsonResponse|RedirectResponse
    {
        $inquiry = Inquiry::create($request->validated());

        // For API responses
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Inquiry submitted successfully.',
                'data' => $inquiry
            ], 201);
        }

        // For web form submissions
        return redirect()->back()->with('success', 'Thank you! Your inquiry has been submitted successfully.');
    }
}
