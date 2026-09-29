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
        $entries = collect($this->item ?? []);
        $productIds = $entries->pluck('product_id')->filter()->all();
        $products = Product::whereIn('product_id', $productIds)->get()->keyBy('product_id');

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
