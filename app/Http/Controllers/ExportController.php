<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Exports\ProductExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportProducts(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,xlsx',
            'include_variants' => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
            'active_only' => 'boolean'
        ]);

        $filename = 'products_' . Carbon::now()->format('Y-m-d_H-i-s') . '.' . $request->format;

        return Excel::download(
            new ProductExport($request->all()),
            $filename
        );
    }

    public function exportOrders(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,xlsx',
            'status' => 'nullable|in:for_verification,payment_verified,shipped,delivered,cancelled,refunded,expired',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'include_items' => 'boolean'
        ]);

        $filename = 'orders_' . Carbon::now()->format('Y-m-d_H-i-s') . '.' . $request->format;

        return Excel::download(
            new OrdersExport($request->all()),
            $filename
        );
    }
}
