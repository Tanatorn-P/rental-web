<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffInspectionController extends Controller
{
    public function index(): View
    {
        // ใช้ order.status = 'returned' เป็นตัวกรองแทน (เดิม whereHas('product') ใช้ไม่ได้แล้วเพราะไม่มี relation ตรง)
        $orders = Order::with('customer')->where('status', 'returned')->get();

        return view('staff.inspection', compact('orders'));
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'order_status' => ['required', 'boolean'],
            'reject_reason' => ['required_if:order_status,0', 'nullable', 'string', 'max:500'],
        ]);

        $order->update([
            'status' => 'completed',
            'order_status' => $validated['order_status'],
            'reject_reason' => $validated['reject_reason'] ?? null,
        ]);

        foreach ($order->orderItems() as $item) {
            $item['product']?->update([
                'status' => $validated['order_status'] ? 'available' : 'not_ready',
            ]);
        }

        return redirect()->route('staff.inspection.index')->with('success', 'บันทึกผลตรวจสภาพ #' . $order->id . ' แล้ว');
    }
}