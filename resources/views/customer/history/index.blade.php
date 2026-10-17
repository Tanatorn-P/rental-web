@extends('layouts.app')

@section('crumb', 'History')

@section('content')
    <div class="page-head">
        <div>
            <div class="eyebrow">RENTAL HISTORY</div>
            <h1 class="page-title">ประวัติการเช่า</h1>
            <p class="page-subtitle">รายการเช่าชุดย้อนหลังทั้งหมดของคุณ</p>
        </div>
    </div>

    @php
        $statusLabels = [
            'returned' => ['คืนชุดแล้ว', 'returned', ''],
            'completed' => ['เสร็จสิ้น', 'completed', ''],
            'overdue' => ['คืนล่าช้า', 'pending', ''],
            'damaged' => ['ชุดเสียหาย', 'rejected', ''],
            'cancelled' => ['ยกเลิก', '', 'color:#6b6560;background:#eee9e3'],
            'rejected' => ['ไม่อนุมัติ', 'rejected', ''],
        ];
    @endphp

    <div class="card table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>รหัสการเช่า</th>
                    <th>รายการชุด</th>
                    <th>หมวดหมู่</th>
                    <th>ระยะเวลาการเช่า</th>
                    <th>สถานะ</th>
                    <th>หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($historyList as $item)
                    @php
                        $items = collect($item->orderItems());
                        $productNames = $items->map(fn ($i) => $i['product']->product_name ?? 'ชุดเช่า')->implode(', ');
                        $firstCategory = $items->first()['product']->category ?? 'งานทั่วไป';

                        [$label, $class, $style] = $statusLabels[$item->order_status]
                            ?? [strtoupper($item->order_status), $item->order_status, ''];

                        // คืนแล้ว แต่ชุดยังอยู่ระหว่างตรวจสภาพ (พนักงานยังไม่ตรวจเสร็จ)
                        $inspecting = in_array($item->order_status, ['returned', 'overdue'], true)
                            && $items->contains(fn ($i) => ($i['product']->status ?? null) === 'inspection');

                        $amount = (float) $item->damage_price;
                    @endphp
                    <tr>
                        <td><strong>{{ $item->order_id }}</strong></td>
                        <td>{{ $productNames ?: 'ชุดเช่า' }}</td>
                        <td>{{ ucfirst($firstCategory) }}</td>
                        <td>
                            {{ optional($item->pickup_date)->format('d M') }} - {{ optional($item->return_date)->format('d M Y') }}
                        </td>
                        <td>
                            <span class="status {{ $class }}" @if ($style) style="{{ $style }}" @endif>
                                {{ $label }}
                            </span>
                            @if ($inspecting)
                                <div class="muted" style="font-size: 11px; margin-top: 4px">รอตรวจสภาพชุด</div>
                            @endif
                        </td>
                        <td style="font-size: 12px">
                            @if ($item->reject_reason)
                                {{ $item->reject_reason }}
                            @elseif ($item->order_status === 'overdue')
                                คืนชุดล่าช้ากว่ากำหนด
                            @else
                                -
                            @endif

                            @if ($amount > 0 && in_array($item->order_status, ['overdue', 'damaged'], true))
                                <br><strong>{{ $item->order_status === 'overdue' ? 'ค่าปรับ' : 'ค่าเสียหาย' }} ฿{{ number_format($amount) }}</strong>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--gray); padding: 30px">
                            ยังไม่มีประวัติการเช่าในระบบ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection