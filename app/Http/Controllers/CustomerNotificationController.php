<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CustomerNotificationController extends Controller
{
    public function index(): View
    {
        $customer = Auth::guard('customer')->user() ?? Customer::first();
        $customerId = $customer ? $customer->customer_id : null;

        $notifications = Order::where('customer_id', $customerId)
            ->orderByDesc('updated_at')
            ->get();

        return view('customer.notifications.index', compact('notifications'));
    }
}
