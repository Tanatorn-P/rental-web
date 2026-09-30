<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerHomeController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user() ?? Customer::first();
        $customerId = $customer ? $customer->customer_id : null;

        // ดึงรายการเช่าปัจจุบัน (Active Order) พร้อม Relationship
        $activeRental = Order::where('customer_id', $customerId)
            ->whereIn('order_status', ['approved', 'preparing', 'rented', 'อนุมัติแล้ว', 'รอรับชุด'])
            ->with(['orderItems.product'])
            ->latest()
            ->first();

        // ดึงรายการจองที่จะเกิดขึ้นในอนาคตเพื่อทำ Countdown
        $upcomingEvents = Order::where('customer_id', $customerId)
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->get();

        // ดึงข้อมูลการแจ้งเตือนล่าสุด
        $notifications = Order::where('customer_id', $customerId)
            ->where(function($query) {
                $query->whereNotNull('reject_reason')
                      ->orWhereIn('order_status', ['approved', 'preparing', 'อนุมัติแล้ว']);
            })
            ->latest()
            ->take(3)
            ->get();

        return view('customer.dashboard.home', compact('customer', 'activeRental', 'upcomingEvents', 'notifications'));
    }
}