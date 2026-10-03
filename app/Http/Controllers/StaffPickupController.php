<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffPickupController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('order_id');

        $orders = Order::with('customer')
            ->where('order_status', 'approved')
            ->when($search, fn ($query) => $query->where('order_id', 'like', '%'.$search.'%'))
            ->orderBy('pickup_date')
            ->get();

        return view('staff.pickup', compact('orders', 'search'));
    }

    public function confirm(Order $order): RedirectResponse
    {
        if ($order->order_status !== 'approved') {
            return redirect()->route('staff.pickup.index')->with('error', 'คำสั่งนี้ยังไม่พร้อมส่งมอบ');
        }

        $order->update(['order_status' => 'rented']);

        foreach ($order->orderItems() as $item) {
            $item['product']?->update(['status' => 'rented']);
        }

        return redirect()->route('staff.pickup.index')->with('success', 'ยืนยันการส่งมอบ #'.$order->order_id.' แล้ว');
    }
}