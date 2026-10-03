<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('order_id');

        /** @var LengthAwarePaginator<int, Order> $orders */
        $orders = Order::with('customer')
            ->whereIn('order_status', ['returned', 'overdue', 'damaged', 'cancelled'])
            ->when($search, fn ($query) => $query->where('order_id', 'like', '%'.$search.'%'))
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('staff.history', compact('orders', 'search'));
    }
}