<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffInspectionController extends Controller
{
    public function index(): View
    {
        $inspectionProductIds = Product::where('status', 'inspection')->pluck('product_id');

        if ($inspectionProductIds->isEmpty()) {
            return view('staff.inspection', ['orders' => collect()]);
        }

        $orders = Order::with('customer')
            ->where(function ($query) use ($inspectionProductIds) {
                foreach ($inspectionProductIds as $productId) {
                    $query->orWhere('item', 'like', '%"product_id":"'.$productId.'"%');
                }
            })
            ->get();

        return view('staff.inspection', compact('orders'));
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'is_ready' => ['required', 'boolean'],
            'reject_reason' => ['required_if:is_ready,0', 'nullable', 'string', 'max:500'],
        ]);

        $order->update([
            'order_status' => $validated['is_ready'] ? 'คืนแล้ว' : 'เสียหาย',
            'reject_reason' => $validated['reject_reason'] ?? null,
        ]);

        foreach ($order->orderItems() as $item) {
            $item['product']?->update([
                'status' => $validated['is_ready'] ? 'available' : 'not_ready',
            ]);
        }

        return redirect()->route('staff.inspection.index')->with('success', 'บันทึกผลตรวจสภาพ #'.$order->order_id.' แล้ว');
    }
}
