<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerHistoryController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user() ?? Customer::first();
        $customerId = $customer ? $customer->customer_id : null;

        // ดึง Orders ผ่าน Relation หรือ Query ตรงโดยระบุ customer_id และ order_status
        $historyList = Order::where('customer_id', $customerId)
            ->whereIn('order_status', ['returned', 'completed', 'rejected', 'คืนแล้ว', 'สำเร็จ'])
            ->with(['orderItems.product']) // Eager loading Relationship
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('customer.history.index', compact('historyList'));
    }
}