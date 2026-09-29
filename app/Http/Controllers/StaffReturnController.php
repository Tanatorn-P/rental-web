<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffReturnController extends Controller
{
    public function index(Request $request): View
    {
        $orderId = $request->query('order_id');

        $order = null;
        if ($orderId !== null) {
            $order = Order::with('customer')->where('order_id', $orderId)->where('order_status', 'กำลังเช่า')->first();
        }

        $lateDays = 0;
        $penalty = 0;
        if ($order !== null && now()->gt($order->return_date)) {
            $lateDays = (int) $order->return_date->diffInDays(now());
            $penalty = $lateDays * 100;
        }

        return view('staff.return', compact('order', 'orderId', 'lateDays', 'penalty'));
    }

    public function confirm(Request $request, Order $order): RedirectResponse
    {
        if ($order->order_status !== 'กำลังเช่า') {
            return redirect()->route('staff.return.index')->with('error', 'คำสั่งนี้ไม่ได้อยู่ในสถานะกำลังเช่า');
        }

        $validated = $request->validate([
            'return_time' => ['required', 'date_format:H:i'],
        ]);

        $isLate = now()->gt($order->return_date);

        $order->update([
            'order_status' => $isLate ? 'เลยกำหนดคืน' : 'คืนแล้ว',
            'return_time' => $validated['return_time'],
        ]);

        foreach ($order->orderItems() as $item) {
            $item['product']?->update(['status' => 'inspection']);
        }

        return redirect()->route('staff.return.index')->with('success', 'รับคืนสินค้า #'.$order->order_id.' แล้ว');
    }
}
