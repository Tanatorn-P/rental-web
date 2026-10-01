<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CustomerRentalController extends Controller
{
    public function show(string|int|null $id = null): View
    {
        $customer = Auth::guard('customer')->user() ?? Customer::first();
        $customerId = $customer ? $customer->customer_id : null;

        $query = Order::where('customer_id', $customerId);

        if ($id) {
            $rental = $query->where('order_id', $id)->firstOrFail(); // เปลี่ยนเป็น order_id
        } else {
            $rental = $query->whereIn('order_status', ['approved', 'preparing', 'rented', 'confirmed'])
                ->latest()
                ->first();
        }

        return view('customer.rentals.show', compact('rental'));
    }
}
