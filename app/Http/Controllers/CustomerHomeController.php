<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CustomerHomeController extends Controller
{
    public function index(): View
    {
        $customer = Auth::guard('customer')->user() ?? Customer::first();
        $customerId = $customer ? $customer->customer_id : null;

        // ดึงรายการเช่าปัจจุบัน
        $activeRental = Order::where('customer_id', $customerId)
            ->whereIn('order_status', ['approved', 'preparing', 'rented', 'confirmed'])
            ->latest()
            ->first();

        // งานที่กำลังจะถึง: เฉพาะรายการที่ยังมีผล (ไม่รวมยกเลิก/คืนแล้ว) และวันงานยังไม่ผ่าน
        $upcomingEvents = Order::where('customer_id', $customerId)
            ->whereIn('order_status', ['pending', 'approved', 'confirmed', 'rented'])
            ->whereDate('event_date', '>=', today())
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();

        // แจ้งเตือนล่าสุด: 3 ออเดอร์ที่มีการอัปเดตล่าสุด (ข้อความตามสถานะ)
        $notifications = Order::where('customer_id', $customerId)
            ->orderByDesc('updated_at')
            ->take(3)
            ->get();

        return view('customer.dashboard.home', compact('customer', 'activeRental', 'upcomingEvents', 'notifications'));
    }
}