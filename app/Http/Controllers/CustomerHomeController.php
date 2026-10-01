<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class CustomerHomeController extends Controller
{
    public function index(): View
    {
        $customer = Auth::guard('customer')->user() ?? Customer::first();
        $customerId = $customer ? $customer->customer_id : null;

        // ดึงรายการเช่าปัจจุบัน (เปลี่ยน status เป็น order_status)
        $activeRental = Order::where('customer_id', $customerId)
            ->whereIn('order_status', ['approved', 'preparing', 'rented', 'confirmed'])
            ->latest()
            ->first();

        $upcomingEvents = Order::where('customer_id', $customerId)
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->get();

        $notifications = Order::where('customer_id', $customerId)
            ->where(function ($q) {
                $q->whereNotNull('reject_reason')
                    ->orWhereIn('order_status', ['approved', 'preparing', 'confirmed']);
            })
            ->latest()
            ->take(3)
            ->get();

        return view('customer.dashboard.home', compact('customer', 'activeRental', 'upcomingEvents', 'notifications'));
    }
}
