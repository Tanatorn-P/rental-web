<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'customer_id', 'item', 'status',
        'order_status', 'reject_reason',
        'event_date', 'pickup_date', 'pickup_time',
        'return_date', 'return_time',
        'total_price', 'security_price', 'damage_price',
    ];

    protected $casts = [
        'event_date' => 'date',
        'pickup_date' => 'date',
        'return_date' => 'date',
        'order_status' => 'boolean',
        'item' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * อ่าน item JSON แล้วดึง Product แต่ละชิ้นมาผูกให้
     * คืนค่าเป็น Collection ของ ['product_id', 'size', 'product']
     */
    public function orderItems(): Collection
    {
        $entries = collect($this->item ?? []);
        $productIds = $entries->pluck('product_id')->filter()->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        return $entries->map(fn ($entry) => [
            'product_id' => $entry['product_id'] ?? null,
            'size' => $entry['size'] ?? null,
            'product' => $products->get($entry['product_id'] ?? null),
        ]);
    }
}
