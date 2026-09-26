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
        $orders = Order::with('customer')->where('status', 'pending')->orderBy('event_date')->get();

        return view('staff.queue', compact('orders'));
    }

    public function approve(Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return redirect()->route('staff.queue.index')->with('error', 'คำขอนี้ถูกดำเนินการไปแล้ว');
        }

        $order->update(['status' => 'approved']);

        foreach ($order->orderItems() as $item) {
            $item['product']?->update(['status' => 'preparing']);
        }

        return redirect()->route('staff.queue.index')->with('success', 'อนุมัติคำขอ #' . $order->id . ' แล้ว');
    }

    public function reject(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'reject_reason' => ['required', 'string', 'max:255'],
        ]);

        if ($order->status !== 'pending') {
            return redirect()->route('staff.queue.index')->with('error', 'คำขอนี้ถูกดำเนินการไปแล้ว');
        }

        $order->update(['status' => 'rejected', 'reject_reason' => $validated['reject_reason']]);

        return redirect()->route('staff.queue.index')->with('success', 'ปฏิเสธคำขอ #' . $order->id . ' แล้ว');
    }
}