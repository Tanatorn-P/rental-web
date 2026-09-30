<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerNotificationController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user() ?? Customer::first();
        $customerId = $customer ? $customer->customer_id : null;

        $notifications = Order::where('customer_id', $customerId)
            ->latest()
            ->get();

        return view('customer.notifications.index', compact('notifications'));
    }
}