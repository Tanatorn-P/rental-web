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
        $orderId = $request->query('order_id');

        $order = null;
        if ($orderId !== null) {
            $order = Order::with('customer')->where('id', $orderId)->where('status', 'approved')->first();
        }

        return view('staff.pickup', compact('order', 'orderId'));
    }

    public function confirm(Order $order): RedirectResponse
    {
        if ($order->status !== 'approved') {
            return redirect()->route('staff.pickup.index')->with('error', 'คำสั่งนี้ยังไม่พร้อมส่งมอบ');
        }

        $order->update(['status' => 'rented']);

        foreach ($order->orderItems() as $item) {
            $item['product']?->update(['status' => 'rented']);
        }

        return redirect()->route('staff.pickup.index')->with('success', 'ยืนยันการส่งมอบ #'.$order->id.' แล้ว');
    }
}
