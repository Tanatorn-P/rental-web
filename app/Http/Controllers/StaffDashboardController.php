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

        $pickupToday = Order::whereDate('pickup_date', $today)->where('status', 'approved')->count();
        $returnToday = Order::whereDate('return_date', $today)->where('status', 'rented')->count();
        $pendingApproval = Order::where('status', 'pending')->count();
        $notReady = Product::where('status', 'not_ready')->count();
        $overdue = Order::whereDate('return_date', '<', $today)->where('status', 'rented')->count();

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
                    'order_id' => $order->id,
                    'customer_name' => $order->customer->fullname ?? '-',
                ];
            })
            ->sortBy('time')
            ->values();

        $pendingList = Order::with('customer')->where('status', 'pending')->orderBy('event_date')->take(5)->get();
        $overdueList = Order::with('customer')->whereDate('return_date', '<', $today)->where('status', 'rented')->get();

        return view('staff.dashboard', compact(
            'pickupToday', 'returnToday', 'pendingApproval', 'notReady', 'overdue',
            'todaySchedule', 'pendingList', 'overdueList'
        ));
    }
}