<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    /**
     * @return BelongsTo<Customer, Order>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * อ่าน item JSON แล้วดึง Product แต่ละชิ้นมาผูกให้
     *
     * @return Collection<int, array{product_id: string|null, size: string|null, product: Product|null}>
     */
    public function orderItems(): Collection
    {
        $entries = collect($this->item ?? []);
        $productIds = $entries->pluck('product_id')->filter()->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        return $entries
            ->values()
            ->map(function (array $entry) use ($products): array {
                $productId = $entry['product_id'] ?? null;

                return [
                    'product_id' => is_string($productId) ? $productId : null,
                    'size' => is_string($entry['size'] ?? null) ? $entry['size'] : null,
                    'product' => $productId !== null ? $products->get($productId) : null,
                ];
            });
    }
}