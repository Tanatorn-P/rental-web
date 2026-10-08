<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\customer;
use App\Models\Staff;


class AdminDashboardController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        $staff = Staff::all();
        $products = Product::all();
        $orders = Order::all();

        $totalCustomers = $customers->count();
        $totalStaff = $staff->count();

        $totalOrders = $orders->count();
        $activeRentals = $orders->whereIn('order_status', ['approved', 'confirmed', 'rented', 'overdue'])->count();
        $totalRevenue = $orders->sum('total_price');
        $overdueCount = $orders->where('order_status', 'overdue')->count();
        $pendingCount = $orders->where('order_status', 'pending')->count();
        $cleaningCount = $orders->where('order_status', 'cleaning')->count();
        $inspectionCount = $orders->where('order_status', 'inspection')->count();
        $repairCount = $orders->where('order_status', 'repair')->count();   

        $totalProducts = $products->count();
        $availableCount = $products->whereIn('status', ['available'])->count();
        $rentedCount = $products->where('status', ['rented', 'preparing'])->count();
        $maintenanceCount = $products->whereIn('status', ['inspection', 'not_ready'])->count();
        $topProducts = $products->map(function ($product) {
            // นับจำนวนออเดอร์ที่มี product_id นี้อยู่ใน JSON array 'item'
            $product->rent_count = Order::whereNotIn('order_status', ['pending', 'cancelled'])
                ->whereJsonContains('item', $product->product_id)
                ->count();
            return $product;
        })
        ->sortByDesc('rent_count') // เรียงจากมากไปน้อย
        ->take(5)                   // เอาเฉพาะ 5 อันดับแรก
        ->values();
        return view('admin.dashboard', compact(
            'customers', 'products', 'orders', 'totalOrders', 'totalProducts', 
            'activeRentals', 'totalRevenue', 'topProducts', 'availableCount', 'rentedCount', 
            'maintenanceCount', 'totalCustomers', 'totalStaff', 'overdueCount', 'pendingCount', 'cleaningCount', 'inspectionCount', 'repairCount'
        ));
    }
}
