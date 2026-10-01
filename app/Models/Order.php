<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Order extends Model
{
    protected $table = 'orders';

    protected $primaryKey = 'order_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'order_id', 'customer_id', 'item',
        'event_date', 'pickup_date', 'pickup_time',
        'return_date', 'return_time',
        'total_price', 'deposit_prices', 'damage_price',
        'reject_reason', 'order_status',
        'slip_image', 'tracking_number',
    ];

    protected $casts = [
        'event_date' => 'date',
        'pickup_date' => 'date',
        'return_date' => 'date',
        'item' => 'array',
    ];

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * อ่าน item JSON แล้วดึง Product แต่ละชิ้นมาผูกให้
     *
     * @return Collection<int, array{product_id: string|null, size: string|null, product: Product|null}>
     */
    public function orderItems(): Collection
{
    $rawItems = $this->item ?? [];
    if (is_string($rawItems)) {
        $rawItems = json_decode($rawItems, true) ?? [];
    }

    // ดึง product_id ไม่ว่าจะเก็บเป็น ["G001"] หรือ [["product_id" => "G001"]]
    $productIds = collect($rawItems)->map(function ($entry) {
        if (is_string($entry)) return $entry;
        return $entry['product_id'] ?? null;
    })->filter()->values()->all();

    $products = Product::whereIn('product_id', $productIds)->get()->keyBy('product_id');

    return collect($rawItems)->map(function ($entry) use ($products) {
        $productId = is_string($entry) ? $entry : ($entry['product_id'] ?? null);
        $size = is_array($entry) ? ($entry['size'] ?? null) : null;

        return [
            'product_id' => $productId,
            'size'       => $size,
            'product'    => $productId ? $products->get($productId) : null,
        ];
    });
}
}
