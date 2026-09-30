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
        $customerId = $customer ? $customer->id : null;

        $query = Order::where('customer_id', $customerId);

        if ($id) {
            $rental = $query->where('id', $id)->firstOrFail();
        } else {
            $rental = $query->whereIn('status', ['approved', 'preparing', 'rented'])
                ->latest()
                ->first();
        }

        return view('customer.rentals.show', compact('rental'));
    }
}