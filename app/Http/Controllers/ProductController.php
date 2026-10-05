<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
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
        $pickupDate = request('pickup_date');
        $returnDate = request('return_date');

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
            ->get();

        if ($pickupDate && $returnDate) {

            $rows = DB::table('orders')
                ->where('order_status', 'confirmed')
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

        // ตรวจสอบว่าช่วงวันที่ที่เลือกมีการจองหรือไม่
        $isBooked = false;

        if ($pickupDate && $returnDate) {
            $isBooked = DB::table('orders')
                ->whereJsonContains('item', $product_id)
                ->where('order_status', 'confirmed')
                ->whereDate('pickup_date', '<=', $returnDate)
                ->whereDate('return_date', '>=', $pickupDate)
                ->exists();
        }

        // ดึงช่วงวันที่มีการจองของชุดนี้
        $bookedRanges = DB::table('orders')
            ->whereJsonContains('item', $product_id)
            ->where('order_status', 'confirmed')
            ->get([
                'pickup_date',
                'return_date',
            ]);

        return view('dress.availability', compact(
            'product',
            'pickupDate',
            'returnDate',
            'isBooked',
            'bookedRanges'
        ));
    }

    public function bookingSummary(string $product_id): View|RedirectResponse
    {
        $product = Product::where('product_id', $product_id)->firstOrFail();

        $pickupDate = (string) request('pickup_date');
        $returnDate = (string) request('return_date');

        if ($pickupDate === '' || $returnDate === '' || $returnDate < $pickupDate) {
            return redirect()
                ->route('dress.availability', $product_id)
                ->with('error', 'กรุณาเลือกวันรับและวันคืนให้ถูกต้อง');
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
        if ($pickupDate === '' || $returnDate === '' || $returnDate < $pickupDate) {
            return redirect()
                ->route('dress.availability', $product_id)
                ->with('error', 'กรุณาเลือกวันรับและวันคืนให้ถูกต้อง');
        }

        // กันจองซ้อน (กรณีมีคนจองตัดหน้าระหว่างที่ลูกค้ากรอกข้อมูล)
        $alreadyBooked = DB::table('orders')
            ->whereJsonContains('item', $product_id)
            ->where('order_status', 'confirmed')
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

            'order_status' => 'confirmed',

            'slip_image' => $slipPath,

            'tracking_number' => null,

            'created_at' => now(),

            'updated_at' => now(),
        ]);

        return redirect()->route('dress.booking.success');
    }

    /**
     * @return array{days: int, baseDays: int, extraDays: int, extraFee: float, extraCost: float, total: float}
     */
    private function calculateRental(Product $product, string $pickupDate, string $returnDate): array
    {
        $days = (int) Carbon::parse($pickupDate)->diffInDays(Carbon::parse($returnDate)) + 1;
        $baseDays = (int) ($product->rental_days ?? 3);
        $extraFee = (float) ($product->extra_day_fee ?? 200);
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
