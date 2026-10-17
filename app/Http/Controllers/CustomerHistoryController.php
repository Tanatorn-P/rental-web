<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CustomerHistoryController extends Controller
{
    /**
     * สถานะที่ถือว่า "จบรายการแล้ว" จากมุมลูกค้า
     * - returned  = พนักงานรับคืนแล้ว (ถ้าชุดยังเป็น inspection จะขึ้นป้าย "รอตรวจสภาพชุด")
     * - overdue   = รับคืนแล้วแต่คืนล่าช้า
     * - damaged   = ตรวจพบความเสียหาย
     * - cancelled = ยกเลิก
     * - completed, rejected
     *
     * @var list<string>
     */
    private const HISTORY_STATUSES = ['returned', 'overdue', 'damaged', 'cancelled', 'completed', 'rejected'];

    public function index(): View
    {
        $customer = Auth::guard('customer')->user() ?? Customer::first();
        $customerId = $customer ? $customer->customer_id : null;

        $historyList = Order::where('customer_id', $customerId)
            ->whereIn('order_status', self::HISTORY_STATUSES)
            ->orderByDesc('pickup_date')
            ->orderByDesc('order_id')
            ->get();

        return view('customer.history.index', compact('historyList'));
    }
}
