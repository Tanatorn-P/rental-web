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
        $search = $request->query('order_id');

        $orders = Order::with('customer')
            ->where('order_status', 'rented')
            ->when($search, fn ($query) => $query->where('order_id', 'like', '%'.$search.'%'))
            ->orderBy('return_date')
            ->get()
            ->map(function (Order $order) {
                $lateDays = 0;
                $penalty = 0;

                if (now()->startOfDay()->gt($order->return_date->copy()->startOfDay())) {
                    $lateDays = (int) $order->return_date->diffInDays(now());
                    $penalty = $lateDays * 100;
                }

                return ['order' => $order, 'lateDays' => $lateDays, 'penalty' => $penalty];
            });

        return view('staff.return', compact('orders', 'search'));
    }

    public function confirm(Order $order): RedirectResponse
    {
        if ($order->order_status !== 'rented') {
            return redirect()->route('staff.return.index')->with('error', 'คำสั่งนี้ไม่ได้อยู่ในสถานะกำลังเช่า');
        }

        $isLate = now()->startOfDay()->gt($order->return_date->copy()->startOfDay());

        $order->update(['order_status' => $isLate ? 'overdue' : 'returned']);

        foreach ($order->orderItems() as $item) {
            $item['product']?->update(['status' => 'inspection']);
        }

        return redirect()->route('staff.return.index')->with('success', 'รับคืนสินค้า #'.$order->order_id.' แล้ว');
    }
}