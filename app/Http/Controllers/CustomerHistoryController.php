<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CustomerHistoryController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user() ?? Customer::first();
        $customerId = $customer ? $customer->customer_id : null;

        $historyList = Order::where('customer_id', $customerId)
            ->whereIn('order_status', ['returned', 'completed', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('customer.history.index', compact('historyList'));
    }
}
