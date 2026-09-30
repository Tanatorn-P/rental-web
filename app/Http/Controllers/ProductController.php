<?php

namespace App\Http\Controllers;


use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // หน้าแรกเลือกหมวดหมู่
   public function find()
{
    $categories = [
        'weding' => 'Wedding',
        'graduation' => 'Graduation',
        'party' => 'Party',
        'formal' => 'Formal',
        'photoshoot' => 'Photoshoot',
        'costume' => 'Costume',
    ];

    return view('dress.find', compact('categories'));
}
    // หน้าที่ 2 แสดงชุดตามหมวดหมู่
    public function category($category)
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
    public function show($product_id)
{
    $product = Product::where('product_id', $product_id)->firstOrFail();

    return view('dress.product', compact('product'));
}
public function availability($product_id)
{
    $product = Product::where('product_id', $product_id)->firstOrFail();

    $pickupDate = request('pickup_date');
    $returnDate = request('return_date');

    $isBooked = false;

    if ($pickupDate && $returnDate) {
        $isBooked = DB::table('orders')
            ->whereJsonContains('item', $product_id)
            ->where('order_status', 'confirmed')
            ->whereDate('pickup_date', '<=', $returnDate)
            ->whereDate('return_date', '>=', $pickupDate)
            ->exists();
    }

    return view('dress.availability', compact(
        'product',
        'pickupDate',
        'returnDate',
        'isBooked'
    ));
}
}