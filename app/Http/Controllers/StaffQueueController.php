<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffQueueController extends Controller
{
    public function index(): View
    {
        $orders = Order::with('customer')->where('order_status', 'pending')->orderBy('event_date')->get();

        return view('staff.queue', compact('orders'));
    }

    public function approve(Order $order): RedirectResponse
    {
        if ($order->order_status !== 'pending') {
            return redirect()->route('staff.queue.index')->with('error', 'คำขอนี้ถูกดำเนินการไปแล้ว');
        }

        $order->update(['order_status' => 'approved']);

        foreach ($order->orderItems() as $item) {
            $item['product']?->update(['status' => 'preparing']);
        }

        return redirect()->route('staff.queue.index')->with('success', 'อนุมัติคำขอ #'.$order->order_id.' แล้ว');
    }

    public function reject(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'reject_reason' => ['required', 'string', 'max:255'],
        ]);

        if ($order->order_status !== 'pending') {
            return redirect()->route('staff.queue.index')->with('error', 'คำขอนี้ถูกดำเนินการไปแล้ว');
        }

        $order->update([
            'order_status' => 'cancelled',
            'reject_reason' => $validated['reject_reason'],
        ]);

        return redirect()->route('staff.queue.index')->with('success', 'ปฏิเสธคำขอ #'.$order->order_id.' แล้ว');
    }
}
