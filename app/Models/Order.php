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
        'order_id',
        'customer_id',
        'item',
        'event_date',
        'pickup_date',
        'pickup_time',
        'return_date',
        'return_time',
        'total_price',
        'deposit_prices',
        'damage_price',
        'reject_reason',
        'order_status',
        'slip_image',
        'tracking_number',
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
     * รองรับทุกรูปแบบที่อาจเจอใน DB:
     * - ["G001", "F002"]                                   (string ล้วน ไม่มี size)
     * - [{"product_id":"G001","size":"M"}]                 (object ปกติ)
     * - null หรือค่าที่ไม่ใช่ array เลย (กันพังถ้า data เพี้ยน)
     *
     * @return Collection<int, array{product_id: string|null, size: string|null, product: Product|null}>
     */
    public function orderItems(): Collection
    {
        // item อาจเป็น null หรือค่าที่ไม่ใช่ array ได้ใน DB จึงอ่านเป็น mixed แล้วเช็คเองก่อน
        /** @var mixed $item */
        $item = $this->item;
        $rawItems = is_array($item) ? array_values($item) : [];

        $entries = collect($rawItems)->map(function (mixed $entry): array {
            if (is_array($entry)) {
                return [
                    'product_id' => $entry['product_id'] ?? null,
                    'size' => $entry['size'] ?? null,
                ];
            }

            // entry เป็น string ล้วน เช่น "G001" → ไม่มี size
            return ['product_id' => $entry, 'size' => null];
        });

        $productIds = $entries
            ->pluck('product_id')
            ->filter(fn (mixed $id): bool => is_string($id))
            ->all();

        $products = Product::whereIn('product_id', $productIds)->get()->keyBy('product_id');

        return $entries
            ->values()
            ->map(function (array $entry) use ($products): array {
                $productId = $entry['product_id'] ?? null;
                $productId = is_string($productId) ? $productId : null;

                return [
                    'product_id' => $productId,
                    'size' => is_string($entry['size'] ?? null) ? $entry['size'] : null,
                    'product' => $productId !== null ? $products->get($productId) : null,
                ];
            });
    }
}
