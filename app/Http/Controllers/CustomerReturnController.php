<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerReturnController extends Controller
{
    /**
     * สถานะที่ลูกค้ายังต้องส่งคืนชุด
     * (overdue = พนักงานรับคืนแล้วแต่คืนช้า จึงไม่อยู่ในรายการนี้)
     *
     * @var list<string>
     */
    private const RETURNABLE_STATUSES = ['rented'];

    public function index(Request $request): View
    {
        $customerId = Auth::guard('customer')->id();
        $search = $request->query('order_id');

        $orders = Order::where('customer_id', $customerId)
            ->whereIn('order_status', self::RETURNABLE_STATUSES)
            ->when($search, fn ($query) => $query->where('order_id', 'like', '%'.$search.'%'))
            ->orderBy('return_date')
            ->get();

        return view('customer.return.index', compact('orders', 'search'));
    }

    public function confirm(Request $request, Order $order): RedirectResponse
    {
        // ลูกค้าแจ้งคืนได้เฉพาะออเดอร์ของตัวเอง
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        if (! in_array($order->order_status, self::RETURNABLE_STATUSES, true)) {
            return redirect()->route('return.index')->with('error', 'คำสั่งนี้ไม่อยู่ในสถานะที่ส่งคืนได้');
        }

        $validated = $request->validate([
            'tracking_number' => ['required', 'string', 'max:50'],
        ]);

        $order->update(['tracking_number' => $validated['tracking_number']]);

        return redirect()->route('return.index')->with(
            'success',
            'แจ้งส่งคืน #'.$order->order_id.' แล้ว รอพนักงานตรวจรับชุด'
        );
    }
}
