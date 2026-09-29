<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StaffMaintenanceController extends Controller
{
    public function index(): View
    {
        $products = Product::where('status', 'not_ready')
            ->get()
            ->map(function (Product $product) {
                $order = Order::where('order_status', 'เสียหาย')
                    ->where('item', 'like', '%"product_id":"'.$product->product_id.'"%')
                    ->latest('order_id')
                    ->first();

                return [
                    'product' => $product,
                    'reject_reason' => $order->reject_reason ?? '-',
                ];
            });

        return view('staff.maintenance', compact('products'));
    }

    public function complete(Product $product): RedirectResponse
    {
        $product->update(['status' => 'available']);

        return redirect()->route('staff.maintenance.index')->with('success', 'ปิดงานสำหรับชุด '.$product->product_name.' แล้ว');
    }
}
