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
     * @return Collection<int, array{product_id: mixed, size: mixed, product: Product|null}>
     */
    public function orderItems(): Collection
// <<<<<<< HEAD
{
    $entries = collect($this->item ?? [])
        ->map(function ($entry) {
            // รองรับ item แบบ ["G001"] (string ล้วน ไม่มี size)
            if (is_string($entry)) {
                return ['product_id' => $entry, 'size' => null];
            }

            return $entry;
        });

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
// =======
//     {
//         // item อาจเป็น null หรือค่าที่ไม่ใช่ array ได้ใน DB จึงอ่านเป็น mixed แล้วเช็คเอง
//         /** @var mixed $item */
//         $item = $this->item;
//         $rawItems = is_array($item) ? array_values($item) : [];

//         // ดึง product_id ไม่ว่าจะเก็บเป็น ["G001"] หรือ [["product_id" => "G001"]]
//         $productIds = collect($rawItems)->map(function (mixed $entry): mixed {
//             if (is_array($entry)) {
//                 return $entry['product_id'] ?? null;
//             }

//             return $entry;
//         })->filter()->values()->all();

//         $products = Product::whereIn('product_id', $productIds)->get()->keyBy('product_id');

//         /** @var Collection<int, array{product_id: mixed, size: mixed, product: Product|null}> $result */
//         $result = collect($rawItems)->map(function (mixed $entry) use ($products): array {
//             $productId = null;
//             $size = null;

//             if (is_array($entry)) {
//                 $productId = $entry['product_id'] ?? null;
//                 $size = $entry['size'] ?? null;
//             } else {
//                 $productId = $entry;
//             }

//             return [
//                 'product_id' => $productId,
//                 'size' => $size,
//                 'product' => (is_string($productId) || is_int($productId)) ? $products->get((string) $productId) : null,
//             ];
//         })->values();

//         return $result;
//     }
// >>>>>>> origin/main
 }
