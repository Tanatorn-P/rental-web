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
        $customerId = $customer ? $customer->id : null;

        // ดึงรายการเช่าปัจจุบัน (Active Order)
        $activeRental = Order::where('customer_id', $customerId)
            ->whereIn('status', ['approved', 'preparing', 'rented'])
            ->latest()
            ->first();

        // ดึงรายการจองที่จะเกิดขึ้นในอนาคตเพื่อทำ Countdown
        $upcomingEvents = Order::where('customer_id', $customerId)
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->get();

        // ดึงข้อมูลการแจ้งเตือนล่าสุด
        $notifications = Order::where('customer_id', $customerId)
            ->whereNotNull('reject_reason')
            ->orWhereIn('status', ['approved', 'preparing'])
            ->latest()
            ->take(3)
            ->get();

        return view('customer.dashboard.home', compact('customer', 'activeRental', 'upcomingEvents', 'notifications'));
    }
}