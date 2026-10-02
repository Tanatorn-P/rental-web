<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        $pickupToday = Order::whereDate('pickup_date', $today)->where('order_status', 'approved')->count();
        $returnToday = Order::whereDate('return_date', $today)->where('order_status', 'rented')->count();
        $pendingApproval = Order::where('order_status', 'pending')->count();
        $overdue = Order::whereDate('return_date', '<', $today)->where('order_status', 'rented')->count();
        $notReady = Product::where('status', 'not_ready')->count();

        $pendingList = Order::with('customer')->where('order_status', 'pending')->orderBy('event_date')->take(5)->get();
        $overdueList = Order::with('customer')->whereDate('return_date', '<', $today)->where('order_status', 'rented')->get();

        $todaySchedule = Order::with('customer')
            ->where(function ($query) use ($today) {
                $query->whereDate('pickup_date', $today)->orWhereDate('return_date', $today);
            })
            ->get()
            ->map(function (Order $order) use ($today) {
                $isPickup = $order->pickup_date->toDateString() === $today;

                return [
                    'type' => $isPickup ? 'Pickup' : 'Return',
                    'time' => $isPickup ? $order->pickup_time : $order->return_time,
                    'order_id' => $order->order_id,
                    'customer_name' => $order->customer->fullname ?? '-',
                ];
            })
            ->sortBy('time')
            ->values();

        $pendingList = Order::with('customer')->where('order_status', 'รอดำเนินการ')->orderBy('event_date')->take(5)->get();
        $overdueList = Order::with('customer')->whereDate('return_date', '<', $today)->where('order_status', 'กำลังเช่า')->get();

        return view('staff.dashboard', compact(
            'pickupToday', 'returnToday', 'pendingApproval', 'notReady', 'overdue',
            'todaySchedule', 'pendingList', 'overdueList'
        ));
    }
}
