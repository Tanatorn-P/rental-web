<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerRentalController extends Controller
{
    public function show($id = null)
    {
        $customer = Auth::guard('customer')->user() ?? Customer::first();
        $customerId = $customer ? $customer->customer_id : null;

        $query = Order::where('customer_id', $customerId)->with(['orderItems.product']);

        if ($id) {
            $rental = $query->where('order_id', $id)->firstOrFail();
        } else {
            $rental = $query->whereIn('order_status', ['approved', 'preparing', 'rented', 'อนุมัติแล้ว'])
                ->latest()
                ->first();
        }

        return view('customer.rentals.show', compact('rental'));
    }
}