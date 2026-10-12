<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * สถานะออเดอร์ที่ถือว่าชุดไม่ว่างในช่วงวันที่นั้น
     * (pending = รอพนักงานยืนยัน ก็ต้องกันวันไว้ ไม่ให้คนอื่นจองซ้อน)
     *
     * @var list<string>
     */
    private const BLOCKING_STATUSES = ['pending', 'approved', 'confirmed', 'rented', 'overdue'];

    // หน้าแรกเลือกหมวดหมู่
    public function find(): View
    {
        $categories = [
            'weding' => 'Wedding',
            'graduation' => 'Graduation',
            'party' => 'Party',
            'formal' => 'Formal',
            'photoshoot' => 'Photoshoot',
            'costume' => 'Costume',
        ];

        $sizes = Product::whereNotNull('size')
            ->distinct()
            ->pluck('size');

        $size = request('size');
        $price = request('price');
        $sort = request('sort'); // price_asc | price_desc
        $pickupDate = request('pickup_date');
        $returnDate = request('return_date');

        // ไซส์ที่แนะนำจากขนาดตัวของลูกค้า (ถ้ามีข้อมูลในโปรไฟล์)
        $customer = Auth::guard('customer')->user();
        $bodyMeasurements = null;
        $recommendedSize = null;

        if ($customer instanceof Customer) {
            $bodyMeasurements = [
                'bust' => $this->measurement($customer->bust),
                'waist' => $this->measurement($customer->waist),
                'hips' => $this->measurement($customer->hips),
            ];

            $recommendedSize = $this->recommendSize(
                $bodyMeasurements['bust'],
                $bodyMeasurements['waist'],
                $bodyMeasurements['hips'],
            );
        }

        $products = Product::where('status', 'available')
            ->when($size, function ($query) use ($size) {
                $query->where('size', $size);
            })
            ->when($price, function ($query) use ($price) {

                if ($price == 'under500') {
                    $query->where('rental_fee', '<', 500);
                }

                if ($price == '500to1000') {
                    $query->whereBetween('rental_fee', [500, 1000]);
                }

                if ($price == 'over1000') {
                    $query->where('rental_fee', '>', 1000);
                }

            })
            ->when($sort === 'price_asc', function ($query) {
                $query->orderBy('rental_fee')->orderBy('product_id');
            })
            ->when($sort === 'price_desc', function ($query) {
                $query->orderByDesc('rental_fee')->orderBy('product_id');
            })
            ->get();

        if ($pickupDate && $returnDate) {

            $rows = DB::table('orders')
                ->whereIn('order_status', self::BLOCKING_STATUSES)
                ->whereDate('pickup_date', '<=', $returnDate)
                ->whereDate('return_date', '>=', $pickupDate)
                ->pluck('item');

            $bookedProductIds = [];

            foreach ($rows as $items) {
                $decoded = json_decode((string) $items, true);

                if (is_array($decoded)) {
                    foreach ($decoded as $id) {
                        $bookedProductIds[] = (string) $id;
                    }
                }
            }

            $bookedProductIds = array_values(array_unique($bookedProductIds));

            $products = $products->whereNotIn(
                'product_id',
                $bookedProductIds
            );
        }

        return view('dress.find', compact(
            'categories',
            'sizes',
            'products',
            'size',
            'price',
            'sort',
            'recommendedSize',
            'bodyMeasurements',
            'pickupDate',
            'returnDate'
        ));
    }

    // หน้าที่ 2 แสดงชุดตามหมวดหมู่
    public function category(string $category): View
    {
        $products = Product::where('category', $category)
            ->where('status', 'available')
            ->get();

        $categoryNames = [
            'weding' => 'Wedding',
            'graduation' => 'Graduation',
            'party' => 'Party',
            'formal' => 'Formal',
            'photoshoot' => 'Photoshoot',
            'costume' => 'Costume',
        ];

        $categoryName = $categoryNames[$category] ?? $category;

        return view('dress.category', compact(
            'products',
            'categoryName'
        ));
    }

    public function show(string $product_id): View
    {
        $product = Product::where('product_id', $product_id)->firstOrFail();

        return view('dress.product', compact('product'));
    }

    public function availability(string $product_id): View
    {
        $product = Product::where('product_id', $product_id)->firstOrFail();

        $pickupDate = request('pickup_date');
        $returnDate = request('return_date');

        // จำนวนวันเช่าของชุด และค่าเช่าเพิ่มต่อวัน (ปฏิทินใช้คำนวณวันคืน)
        $rentalDays = (int) $product->rental_duration_days;
        $extraFee = $this->extraDayFee($product);

        // ตรวจสอบว่าช่วงวันที่ที่เลือกมีการจองหรือไม่
        $isBooked = false;

        if ($pickupDate && $returnDate) {
            $isBooked = DB::table('orders')
                ->whereJsonContains('item', $product_id)
                ->whereIn('order_status', self::BLOCKING_STATUSES)
                ->whereDate('pickup_date', '<=', $returnDate)
                ->whereDate('return_date', '>=', $pickupDate)
                ->exists();
        }

        // ดึงช่วงวันที่มีการจองของชุดนี้
        $bookedRanges = DB::table('orders')
            ->whereJsonContains('item', $product_id)
            ->whereIn('order_status', self::BLOCKING_STATUSES)
            ->get([
                'pickup_date',
                'return_date',
            ]);

        return view('dress.availability', compact(
            'product',
            'pickupDate',
            'returnDate',
            'isBooked',
            'bookedRanges',
            'rentalDays',
            'extraFee'
        ));
    }

    public function bookingSummary(string $product_id): View|RedirectResponse
    {
        $product = Product::where('product_id', $product_id)->firstOrFail();

        $pickupDate = (string) request('pickup_date');
        $returnDate = (string) request('return_date');

        $error = $this->rangeError($product, $pickupDate, $returnDate);

        if ($error !== null) {
            return redirect()
                ->route('dress.availability', $product_id)
                ->with('error', $error);
        }

        $price = $this->calculateRental($product, $pickupDate, $returnDate);

        return view('dress.booking-summary', compact(
            'product', 'pickupDate', 'returnDate', 'price'
        ));
    }

    public function bookingInformation(string $product_id): View
    {
        $product = Product::where('product_id', $product_id)->firstOrFail();

        $pickupDate = (string) request('pickup_date');
        $returnDate = (string) request('return_date');
        $price = $this->calculateRental($product, $pickupDate, $returnDate);

        return view('dress.booking-information', compact(
            'product', 'pickupDate', 'returnDate', 'price'
        ));

    }

    public function confirmBooking(string $product_id): RedirectResponse
    {
        $product = Product::where('product_id', $product_id)->firstOrFail();
        $fullname = request('fullname');
        $phone = request('phone');
        $bankAccount = request('bank_account');
        $address = request('address');
        $pickupDate = (string) request('pickup_date');
        $returnDate = (string) request('return_date');

        $error = $this->rangeError($product, $pickupDate, $returnDate);

        if ($error !== null) {
            return redirect()
                ->route('dress.availability', $product_id)
                ->with('error', $error);
        }

        // กันจองซ้อน (กรณีมีคนจองตัดหน้าระหว่างที่ลูกค้ากรอกข้อมูล)
        $alreadyBooked = DB::table('orders')
            ->whereJsonContains('item', $product_id)
            ->whereIn('order_status', self::BLOCKING_STATUSES)
            ->whereDate('pickup_date', '<=', $returnDate)
            ->whereDate('return_date', '>=', $pickupDate)
            ->exists();

        if ($alreadyBooked) {
            return redirect()
                ->route('dress.availability', $product_id)
                ->with('error', 'ชุดนี้ถูกจองในช่วงวันที่เลือกแล้ว');
        }

        $price = $this->calculateRental($product, $pickupDate, $returnDate);

        $slipImage = request()->file('slip_image');
        $slipPath = null;

        if ($slipImage) {
            $slipPath = $slipImage->store('slips', 'public');
        }
        $customerId = Auth::guard('customer')->id();
        $customer = DB::table('customers')
            ->where('customer_id', $customerId)
            ->first();
        DB::table('customers')
            ->where('customer_id', $customerId)
            ->update([
                'fullname' => $fullname,
                'phone' => $phone,
                'bank_account' => $bankAccount,
                'address' => $address,
                'updated_at' => now(),
            ]);

        DB::table('orders')->insert([
            'order_id' => 'O'.str_pad(
                (string) (DB::table('orders')->count() + 1),
                4,
                '0',
                STR_PAD_LEFT
            ),

            'customer_id' => $customerId,

            'item' => json_encode([
                $product_id,
            ]),

            'event_date' => $pickupDate,

            'pickup_date' => $pickupDate,

            'pickup_time' => '00:00:00',

            'return_date' => $returnDate,

            'return_time' => '00:00:00',

            'total_price' => $price['total'] + $product->deposit,

            'deposit_prices' => $product->deposit,

            'damage_price' => 0,

            'reject_reason' => null,

            // รอพนักงานตรวจสลิปและอนุมัติ (Reservation Queue) -> approved
            'order_status' => 'pending',

            'slip_image' => $slipPath,

            'tracking_number' => null,

            'created_at' => now(),

            'updated_at' => now(),
        ]);

        return redirect()->route('dress.booking.success');
    }

    /**
     * แปลงค่าขนาดตัวจากโปรไฟล์เป็นตัวเลข (ว่าง/ไม่ใช่ตัวเลข/0 = ไม่มีข้อมูล)
     */
    private function measurement(mixed $value): ?float
    {
        return is_numeric($value) && (float) $value > 0 ? (float) $value : null;
    }

    private function recommendSize(?float $bust, ?float $waist, ?float $hips): ?string
    {
        $measurements = array_filter([$bust, $waist, $hips], static fn ($value) => $value !== null);

        if ($measurements === []) {
            return null;
        }

        $largestMeasurement = max($measurements);

        return match (true) {
            $largestMeasurement <= 80 => 'XS',
            $largestMeasurement <= 86 => 'S',
            $largestMeasurement <= 92 => 'M',
            $largestMeasurement <= 100 => 'L',
            $largestMeasurement <= 108 => 'XL',
            default => 'XXL',
        };
    }

    /**
     * ตรวจช่วงวันที่เลือก: ต้องครบ, วันคืนไม่ก่อนวันรับ และเช่าไม่น้อยกว่าจำนวนวันของชุด
     * คืนข้อความ error หรือ null ถ้าถูกต้อง
     */
    private function rangeError(Product $product, string $pickupDate, string $returnDate): ?string
    {
        if ($pickupDate === '' || $returnDate === '' || $returnDate < $pickupDate) {
            return 'กรุณาเลือกวันรับและวันคืนให้ถูกต้อง';
        }

        $baseDays = (int) $product->rental_duration_days;

        if ($this->countRentalDays($pickupDate, $returnDate) < $baseDays) {
            return 'ชุดนี้ต้องเช่าอย่างน้อย '.$baseDays.' วัน';
        }

        return null;
    }

    /**
     * นับจำนวนวันจากวันรับถึงวันคืน (รับ 6 -> คืน 10 = 4 วัน)
     * ตรงกับข้อมูล orders: return_date = pickup_date + rental_duration_days
     */
    private function countRentalDays(string $pickupDate, string $returnDate): int
    {
        $days = Carbon::parse($pickupDate)->startOfDay()
            ->diffInDays(Carbon::parse($returnDate)->startOfDay());

        return max(0, (int) round($days));
    }

    private function extraDayFee(Product $product): float
    {
        return (float) ($product->extra_day_fee ?? 200);
    }

    /**
     * @return array{days: int, baseDays: int, extraDays: int, extraFee: float, extraCost: float, total: float}
     */
    private function calculateRental(Product $product, string $pickupDate, string $returnDate): array
    {
        $days = $this->countRentalDays($pickupDate, $returnDate);
        $baseDays = (int) $product->rental_duration_days;
        $extraFee = $this->extraDayFee($product);
        $extraDays = max(0, $days - $baseDays);

        return [
            'days' => $days,
            'baseDays' => $baseDays,
            'extraDays' => $extraDays,
            'extraFee' => $extraFee,
            'extraCost' => $extraDays * $extraFee,
            'total' => (float) $product->rental_fee + ($extraDays * $extraFee),
        ];
    }
}