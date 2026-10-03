<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffInspectionController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('order_id');

        $inspectionProductIds = Product::where('status', 'inspection')->pluck('product_id');

        if ($inspectionProductIds->isEmpty()) {
            return view('staff.inspection', ['orders' => collect(), 'search' => $search]);
        }

        $orders = Order::with('customer')
            ->where(function ($query) use ($inspectionProductIds) {
                foreach ($inspectionProductIds as $productId) {
                    $query->orWhere('item', 'like', '%"product_id":"'.$productId.'"%');
                }
            })
            ->when($search, fn ($query) => $query->where('order_id', 'like', '%'.$search.'%'))
            ->get();

        return view('staff.inspection', compact('orders', 'search'));
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'is_ready' => ['required', 'boolean'],
            'reject_reason' => ['required_if:is_ready,0', 'nullable', 'string', 'max:500'],
        ]);

        $order->update([
            'order_status' => $validated['is_ready'] ? 'returned' : 'damaged',
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
