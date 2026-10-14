<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * ชื่อเดือนภาษาไทยแบบเต็ม (คีย์ 1-12)
     *
     * @return array<int, string>
     */
    private function monthNamesTh(): array
    {
        return [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม',
        ];
    }

    /**
     * ชื่อเดือนภาษาไทยแบบย่อ (คีย์ 1-12) ใช้ตอนขึ้นป้ายช่วงวันที่ของแต่ละสัปดาห์
     *
     * @return array<int, string>
     */
    private function monthAbbrTh(): array
    {
        return [
            1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
            5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
            9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.',
        ];
    }

    /**
     * สร้างชุดข้อมูลกราฟรายได้แบบรายสัปดาห์ของเดือนที่เลือก
     *
     * สัปดาห์เริ่มวันอาทิตย์ สัปดาห์ไหนที่ "วันอาทิตย์" ของสัปดาห์นั้นตกอยู่ในเดือนที่เลือก
     * ทั้งสัปดาห์ (อาทิตย์-เสาร์) จะถูกนับเป็นของเดือนนั้นทั้งแท่ง แม้บางวันในสัปดาห์
     * จะล้นไปอยู่เดือนถัดไปก็ตาม
     *
     * รายได้นับจากฟิลด์ total_price ของตาราง orders ทุกออเดอร์
     * (ครอบคลุมทั้งค่าเช่าและค่าสินค้าที่รวมอยู่ในยอดเดียวกัน) อิงตามวันที่สร้างออเดอร์ (created_at)
     *
     * @return array{
     *     year:int, month:int, month_label:string,
     *     labels:array<int,string>, values:array<int,float>, max_value:float,
     *     prev:array{year:int,month:int}, next:array{year:int,month:int}, can_go_next:bool
     * }
     */
    private function buildRevenueChart(int $year, int $month): array
    {
        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();

        $labels = [];
        $values = [];

        // หาวันอาทิตย์ทุกวันที่ตกอยู่ในเดือนนี้ -> แต่ละวันอาทิตย์ = จุดเริ่มสัปดาห์ 1 แท่ง
        $sundays = [];
        $cursor = $monthStart->copy();
        while ($cursor->lte($monthEnd)) {
            if ($cursor->dayOfWeek === Carbon::SUNDAY) {
                $sundays[] = $cursor->copy();
            }
            $cursor->addDay();
        }

        $abbr = $this->monthAbbrTh();

        foreach ($sundays as $sunday) {
            $weekStart = $sunday->copy()->startOfDay();
            $weekEnd = $sunday->copy()->addDays(6)->endOfDay();

            $sum = (float) Order::query()
                    ->whereBetween('event_date', [$weekStart, $weekEnd])
                    ->whereYear('event_date', $year)
                    ->whereMonth('event_date', $month)
                    ->where('order_status', 'rented')
                    ->sum('total_price');

            $startLabel = $weekStart->day.' '.$abbr[$weekStart->month];
            $endLabel = $weekEnd->day.' '.$abbr[$weekEnd->month];

            $labels[] = $startLabel.' - '.$endLabel;
            $values[] = $sum;
        }

        $now = Carbon::now();
        $prev = Carbon::create($year, $month, 1)->subMonthNoOverflow();
        $next = Carbon::create($year, $month, 1)->addMonthNoOverflow();

        $nextIsFuture = $next->year > $now->year || ($next->year === $now->year && $next->month > $now->month);

        return [
            'year' => $year,
            'month' => $month,
            'month_label' => ($this->monthNamesTh()[$month] ?? $month).' '.$year,
            'labels' => $labels,
            'values' => $values,
            'max_value' => $values === [] ? 0.0 : max($values),
            'prev' => ['year' => $prev->year, 'month' => $prev->month],
            'next' => ['year' => $next->year, 'month' => $next->month],
            'can_go_next' => ! $nextIsFuture,
        ];
    }

    public function index(Request $request): View
    {
        $now = Carbon::now();

        // รับปี/เดือนที่จะดูจาก query string (?year=&month=) ไม่มีก็ใช้เดือนปัจจุบัน
        // การเปลี่ยนเดือนทำผ่านลิงก์ธรรมดา กดแล้วรีเฟรชหน้าได้ตามปกติ
        $year = (int) $request->query('year', $now->year);
        $month = (int) $request->query('month', $now->month);
        $month = max(1, min(12, $month));

        $customers = Customer::all();
        $staff = Staff::all();
        $products = Product::all();
        $orders = Order::all();

        $totalCustomers = $customers->count();
        $totalStaff = $staff->count();

        $totalOrders = $orders->count();
        $activeRentals = $orders->whereIn('order_status', ['approved', 'confirmed', 'rented', 'overdue'])->count();
        $totalRevenue = $orders->sum('total_price');
        $overdueCount = $orders->where('order_status', 'overdue')->count();
        $pendingCount = $orders->where('order_status', 'pending')->count();
        $cleaningCount = $orders->where('order_status', 'cleaning')->count();
        $inspectionCount = $orders->where('order_status', 'inspection')->count();
        $repairCount = $orders->where('order_status', 'repair')->count();

        // สินค้าที่ "อยู่ในกระบวนการเช่า" = สถานะ preparing (กำลังเตรียม), rented (เช่าแล้ว),
        // inspection (กำลังตรวจสอบหลังรับคืน) เท่านั้น
        $inprogressCount = $products->whereIn('status', ['preparing', 'rented', 'inspection'])->count();

        $totalProducts = $products->count();
        $availableCount = $products->where('status', 'available')->count();

        // "อยู่กับลูกค้า" นับเฉพาะสินค้าที่สถานะ rented อย่างเดียว
        $withCustomerCount = $products->where('status', 'rented')->count();

        // "ไม่พร้อมให้เช่า" นับเฉพาะสินค้าที่สถานะ not_ready อย่างเดียว
        $notReadyCount = $products->where('status', 'not_ready')->count();

        $topProducts = $products->map(function ($product) {
            // นับจำนวนออเดอร์ที่มี product_id นี้อยู่ใน JSON array 'item'
            $product->rent_count = Order::whereNotIn('order_status', ['pending', 'cancelled'])
                ->whereJsonContains('item', $product->product_id)
                ->count();

            return $product;
        })
            ->sortByDesc('rent_count') // เรียงจากมากไปน้อย
            ->take(5)                   // เอาเฉพาะ 5 อันดับแรก
            ->values();

        $chart = $this->buildRevenueChart($year, $month);

        return view('admin.dashboard', compact(
            'customers', 'products', 'orders', 'totalOrders', 'totalProducts',
            'activeRentals', 'totalRevenue', 'topProducts', 'availableCount',
            'withCustomerCount', 'notReadyCount', 'totalCustomers', 'totalStaff',
            'overdueCount', 'pendingCount', 'cleaningCount', 'inspectionCount',
            'repairCount', 'inprogressCount', 'chart'
        ));
    }
}
