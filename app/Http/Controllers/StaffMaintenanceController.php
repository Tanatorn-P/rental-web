<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffMaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('product_name');

        $products = Product::where('status', 'not_ready')
            ->when($search, fn ($query) => $query->where('product_name', 'like', '%'.$search.'%'))
            ->get()
            ->map(function (Product $product) {
                $order = Order::where('order_status', 'damaged')
                    ->get()
                    ->first(function (Order $order) use ($product) {
                        $productIds = $order->orderItems()->pluck('product_id')->all();

                        return in_array($product->product_id, $productIds, true);
                    });

                return [
                    'product' => $product,
                    'reject_reason' => $order->reject_reason ?? '-',
                ];
            });

        return view('staff.maintenance', compact('products', 'search'));
    }

    public function complete(Product $product): RedirectResponse
    {
        $product->update(['status' => 'available']);

        return redirect()->route('staff.maintenance.index')->with('success', 'ปิดงานสำหรับชุด '.$product->product_name.' แล้ว');
    }
}
