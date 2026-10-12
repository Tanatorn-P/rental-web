<?php

namespace App\Support;

class SizeRecommender
{
    /**
     * ตารางไซส์ (ซม.) เรียงจากเล็กไปใหญ่ แต่ละไซส์คือ "ค่าสูงสุด" ที่ไซส์นั้นรองรับ
     * ตัวเลขเป็นค่าตั้งต้น ให้ร้านปรับตามตารางไซส์จริงของชุดที่นี่ที่เดียว
     *
     * @var array<string, array{bust: int, waist: int, hips: int}>
     */
    private const SIZE_CHART = [
        'S' => ['bust' => 84, 'waist' => 66, 'hips' => 90],
        'M' => ['bust' => 88, 'waist' => 70, 'hips' => 94],
        'L' => ['bust' => 92, 'waist' => 74, 'hips' => 98],
        'XL' => ['bust' => 96, 'waist' => 78, 'hips' => 102],
    ];

    /**
     * แนะนำไซส์: เลือกไซส์เล็กสุดที่ทุกขนาดที่มีข้อมูลไม่เกินค่าสูงสุดของไซส์นั้น
     * (ขนาดที่ใหญ่ที่สุดเป็นตัวกำหนด)
     * - ไม่มีข้อมูลขนาดตัวเลย -> null
     * - ใหญ่กว่าไซส์ใหญ่สุดในตาราง -> ไซส์ใหญ่สุด
     */
    public static function recommend(?float $bust, ?float $waist, ?float $hips): ?string
    {
        $measurements = array_filter(
            ['bust' => $bust, 'waist' => $waist, 'hips' => $hips],
            fn (?float $value): bool => $value !== null && $value > 0,
        );

        if ($measurements === []) {
            return null;
        }

        foreach (self::SIZE_CHART as $size => $limits) {
            $fits = true;

            foreach ($measurements as $key => $value) {
                if ($value > $limits[$key]) {
                    $fits = false;
                    break;
                }
            }

            if ($fits) {
                return $size;
            }
        }

        return (string) array_key_last(self::SIZE_CHART);
    }
}