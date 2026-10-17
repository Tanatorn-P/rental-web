@extends('layouts.app')

@section('crumb', 'Rental Tracking')

@section('content')
    <div class="page-head">
        <div>
            <div class="eyebrow">RENTAL TRACKING</div>
            <h1 class="page-title">ติดตามสถานะการเช่า</h1>
            <p class="page-subtitle">
                @if ($rental)
                    รหัสการเช่า: {{ $rental->order_id }} • อัปเดตล่าสุด: {{ $rental->updated_at ? \Carbon\Carbon::parse($rental->updated_at)->format('d M Y H:i') : '-' }}
                @else
                    ไม่พบรายการเช่าปัจจุบัน
                @endif
            </p>
        </div>
    </div>

    @if ($rental)
        @php
            $status = $rental->order_status;
            $isClosedWithoutRental = in_array($status, ['cancelled', 'rejected'], true);
            $isReturned = in_array($status, ['returned', 'overdue', 'damaged', 'completed'], true);

            // ขั้นตอนของออเดอร์ (confirmed/preparing นับเป็น "อนุมัติแล้ว")
            $steps = [
                'รออนุมัติ',
                'อนุมัติแล้ว',
                'กำลังเช่า',
                match ($status) {
                    'overdue' => 'คืนล่าช้า',
                    'damaged' => 'ชุดเสียหาย',
                    default => 'คืนชุดแล้ว',
                },
            ];

            $stepIndex = match ($status) {
                'pending' => 0,
                'approved', 'confirmed', 'preparing' => 1,
                'rented' => 2,
                'returned', 'overdue', 'damaged', 'completed' => 3,
                default => null,
            };

            $items = collect($rental->orderItems());
            $inspecting = $items->contains(fn ($i) => ($i['product']->status ?? null) === 'inspection');
            $daysLeft = (int) now()->startOfDay()->diffInDays($rental->return_date, false);
            $reason = $rental->reject_reason;
            $amount = (float) $rental->damage_price;

            // เวลา 00:00 = ยังไม่ได้ระบุเวลา (เกิดจากการจองในระบบ)
            $fmtTime = function ($time) {
                $t = $time ? substr((string) $time, 0, 5) : '';

                return ($t === '' || $t === '00:00') ? null : $t;
            };
            $pickupTime = $fmtTime($rental->pickup_time);
            $returnTime = $fmtTime($rental->return_time);
        @endphp

        <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 20px">
            <!-- Timeline Status -->
            <div class="card card-pad">
                <h2 class="section-title" style="margin-bottom: 20px">ติดตามสถานะ</h2>

                @if (! $isClosedWithoutRental)
                    <div style="position: relative; margin: 30px 0">
                        {{-- เส้นเชื่อมขั้นตอน (จากกึ่งกลางช่องแรกถึงช่องสุดท้าย) --}}
                        <div style="position: absolute; top: 13px; left: 12.5%; right: 12.5%; height: 2px; background: var(--line)"></div>
                        <div style="position: absolute; top: 13px; left: 12.5%; width: {{ 75 * ($stepIndex ?? 0) / 3 }}%; height: 2px; background: var(--mauve)"></div>

                        <div style="position: relative; display: grid; grid-template-columns: repeat(4, 1fr); text-align: center">
                            @foreach ($steps as $i => $label)
                                @php
                                    $done = $stepIndex !== null && $i <= $stepIndex;
                                    $danger = $i === 3 && in_array($status, ['overdue', 'damaged'], true);
                                    $bg = $done ? ($danger ? 'var(--danger)' : 'var(--mauve)') : 'var(--line)';
                                @endphp
                                <div>
                                    <div
                                        style="width: 28px; height: 28px; border-radius: 50%; background: {{ $bg }}; color: {{ $done ? 'white' : 'var(--gray)' }}; display: grid; place-items: center; margin: auto; font-size: 12px; font-weight: 700"
                                    >
                                        {{ $done ? '✓' : $i + 1 }}
                                    </div>
                                    <div style="font-size: 11px; margin-top: 6px; font-weight: {{ $i === $stepIndex ? 700 : 500 }}">
                                        {{ $label }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div style="margin-top: 24px; padding: 14px; background: #fbf7f2; border-radius: 10px">
                    <strong>{{ $isClosedWithoutRental ? 'สถานะ:' : 'คำแนะนำ:' }}</strong>
                    <p style="font-size: 12px; color: var(--gray); margin: 4px 0 0">
                        @if ($status === 'pending')
                            ทางร้านกำลังตรวจสอบคำขอจองและหลักฐานการชำระเงินของคุณ กรุณารอการอนุมัติ
                        @elseif (in_array($status, ['approved', 'confirmed', 'preparing'], true))
                            คำขอจองได้รับการอนุมัติแล้ว กรุณามารับชุดในวันที่ {{ optional($rental->pickup_date)->format('d M Y') }}
                        @elseif ($status === 'rented')
                            ขณะนี้ชุดอยู่กับคุณ กรุณานำมาคืนภายในวันที่กำหนด
                            @if ($daysLeft < 0)
                                <strong style="color: var(--danger)">(เลยกำหนดมาแล้ว {{ abs($daysLeft) }} วัน)</strong>
                            @elseif ($daysLeft === 0)
                                <strong style="color: var(--mauve)">(ครบกำหนดคืนวันนี้)</strong>
                            @else
                                (เหลืออีก {{ $daysLeft }} วัน)
                            @endif
                        @elseif ($status === 'returned')
                            ร้านรับคืนชุดเรียบร้อยแล้ว
                            @if ($inspecting)
                                ขณะนี้พนักงานกำลังตรวจสภาพชุด
                            @endif
                        @elseif ($status === 'overdue')
                            {{ $reason ?: 'คืนชุดล่าช้ากว่ากำหนด' }}
                            @if ($amount > 0)
                                <br><strong>ค่าปรับ ฿{{ number_format($amount) }}</strong>
                            @endif
                        @elseif ($status === 'damaged')
                            {{ $reason ?: 'ตรวจพบความเสียหายของชุด' }}
                            @if ($amount > 0)
                                <br><strong>ค่าเสียหาย ฿{{ number_format($amount) }}</strong>
                            @endif
                        @elseif ($status === 'cancelled')
                            การจองนี้ถูกยกเลิก{{ $reason ? ': '.$reason : '' }}
                        @elseif ($status === 'rejected')
                            คำขอจองไม่ได้รับการอนุมัติ{{ $reason ? ': '.$reason : '' }}
                        @elseif ($status === 'completed')
                            รายการเช่าเสร็จสมบูรณ์เรียบร้อยแล้ว
                        @else
                            สถานะปัจจุบัน: {{ strtoupper($status) }}
                        @endif
                    </p>
                </div>
            </div>

            <div style="display: grid; gap: 20px; align-content: start">
                <!-- รายการชุด -->
                <div class="card card-pad">
                    <h2 class="section-title" style="margin-bottom: 16px">รายการชุด</h2>
                    @foreach ($items as $it)
                        @php
                            $img = $it['product']?->image ?? [];
                        @endphp
                        <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 12px">
                            @if (! empty($img[0]))
                                <img
                                    src="{{ asset($img[0]) }}"
                                    alt=""
                                    style="width: 56px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid var(--line)"
                                />
                            @endif
                            <div style="font-size: 13px">
                                <strong>{{ $it['product']->product_name ?? $it['product_id'] }}</strong><br />
                                <span class="muted" style="font-size: 11px">
                                    ไซส์ {{ $it['size'] ?? $it['product']->size ?? '-' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- ข้อมูลการรับ-คืน -->
                <div class="card card-pad">
                    <h2 class="section-title" style="margin-bottom: 16px">ข้อมูลการรับ-คืน</h2>
                    <div style="font-size: 13px; line-height: 1.8">
                        <p>
                            <strong>วันรับชุด:</strong><br />
                            {{ optional($rental->pickup_date)->format('d M Y') }}
                            @if ($pickupTime)
                                - {{ $pickupTime }} น.
                            @else
                                <span class="muted">(เวลาตามที่ร้านนัดหมาย)</span>
                            @endif
                        </p>
                        <p>
                            <strong>วันคืนชุด:</strong><br />
                            {{ optional($rental->return_date)->format('d M Y') }}
                            @if ($isReturned && $returnTime)
                                - คืนจริงเวลา {{ $returnTime }} น.
                            @elseif ($returnTime)
                                - ก่อน {{ $returnTime }} น.
                            @else
                                <span class="muted">(ภายในเวลาทำการของร้าน)</span>
                            @endif
                        </p>
                        <p><strong>สถานที่รับ/คืนชุด:</strong><br />DressDay Studio Bangkok</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card card-pad" style="text-align: center; padding: 40px">
            <p class="muted">ไม่พบข้อมูลการเช่าในขณะนี้</p>
            <a
                href="{{ Route::has('dress.find') ? route('dress.find') : '#' }}"
                class="btn btn-primary"
                style="margin-top: 10px"
            >ค้นหาชุดเช่า</a>
        </div>
    @endif
@endsection