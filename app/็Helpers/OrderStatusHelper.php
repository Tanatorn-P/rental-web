<?php

namespace App\Helpers;

class OrderStatusHelper
{
    /**
     * @var array<string, string>
     */
    private const LABELS = [
        'pending' => 'รอดำเนินการ',
        'approved' => 'อนุมัติแล้ว',
        'rented' => 'กำลังเช่า',
        'returned' => 'คืนแล้ว',
        'overdue' => 'เลยกำหนดคืน',
        'damaged' => 'เสียหาย',
        'cancelled' => 'ยกเลิก',
    ];

    public static function label(string $status): string
    {
        return self::LABELS[$status] ?? $status;
    }
}