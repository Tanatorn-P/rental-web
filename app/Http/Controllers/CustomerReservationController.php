<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CustomerReservationController extends Controller
{
    public function index(): View
    {
        $customer = Auth::guard('customer')->user() ?? Customer::first();
        $customerId = $customer ? $customer->customer_id : null;

        $reservations = Order::where('customer_id', $customerId)
            ->whereIn('order_status', ['pending', 'approved', 'confirmed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.reservations.index', compact('reservations'));
    }
}