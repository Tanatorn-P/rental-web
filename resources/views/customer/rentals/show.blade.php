@extends('layouts.app')

@section('crumb', 'My Rentals')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">CURRENT RENTAL</div>
        <h1 class="page-title">การเช่าปัจจุบัน</h1>
        <p class="page-subtitle">
            @if($rental)
                รหัสการเช่า: ORD{{ str_pad($rental->order_id, 6, '0', STR_PAD_LEFT) }} • อัปเดตล่าสุด: {{ $rental->updated_at ? \Carbon\Carbon::parse($rental->updated_at)->format('d M Y H:i') : '-' }}
            @else
                ไม่พบรายการเช่าปัจจุบัน
            @endif
        </p>
    </div>
</div>

@if($rental)
<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 20px;">
    <!-- Timeline Status -->
    <div class="card card-pad">
        <h2 class="section-title" style="margin-bottom: 20px;">ติดตามสถานะ</h2>
        
        <div style="display: flex; justify-content: space-between; position: relative; margin: 30px 0;">
            @php
                $steps = ['pending' => 'รออนุมัติ', 'approved' => 'อนุมัติแล้ว', 'preparing' => 'เตรียมชุด', 'rented' => 'อยู่ระหว่างเช่า', 'returned' => 'คืนชุดสำเร็จ'];
                $currentStatus = $rental->order_status;
            @endphp

            @foreach($steps as $key => $label)
                @php
                    $bgColor = ($currentStatus == $key) ? 'var(--mauve)' : 'var(--line)';
                @endphp
                <div style="text-align: center; z-index: 1;">
                    <div style="width: 28px; height: 28px; border-radius: 50%; background: {{ $bgColor }}; color: white; display: grid; place-items: center; margin: auto; font-size: 12px; font-weight: 700;">
                        ✓
                    </div>
                    <div style="font-size: 11px; margin-top: 6px; font-weight: 700;">{{ $label }}</div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 24px; padding: 14px; background: #fbf7f2; border-radius: 10px;">
            <strong>คำแนะนำ:</strong>
            <p style="font-size: 12px; color: var(--gray); margin: 4px 0 0;">
                @if($rental->order_status == 'pending')
                    ทางร้านกำลังตรวจสอบคำขอจองของคุณ กรุณารอการอนุมัติ
                @elseif($rental->order_status == 'approved')
                    คำขอจองได้รับการอนุมัติแล้ว ร้านกำลังจัดเตรียมชุดให้คุณ
                @elseif($rental->order_status == 'preparing')
                    ชุดของคุณถูกทำความสะอาดและซักอบรีดเรียบร้อย พร้อมสำหรับการมารับ
                @elseif($rental->order_status == 'rented')
                    ขณะนี้ชุดอยู่กับคุณ กรุณานำมาคืนภายในวันที่กำหนด
                @else
                    รายการเช่าเสร็จสมบูรณ์เรียบร้อยแล้ว
                @endif
            </p>
        </div>
    </div>

    <!-- ข้อมูลการรับ-คืน -->
    <div class="card card-pad">
        <h2 class="section-title" style="margin-bottom: 16px;">ข้อมูลการรับ-คืน</h2>
        <div style="font-size: 13px; line-height: 1.8;">
            <p><strong>วันรับชุด:</strong><br>{{ optional($rental->pickup_date)->format('d M Y') }} - {{ $rental->pickup_time ?? '10:00' }}</p>
            <p><strong>วันคืนชุด:</strong><br>{{ optional($rental->return_date)->format('d M Y') }} - ก่อน {{ $rental->return_time ?? '17:00' }}</p>
            <p><strong>สถานที่รับ/คืนชุด:</strong><br>DressDay Studio Bangkok</p>
        </div>
    </div>
</div>
@else
<div class="card card-pad" style="text-align: center; padding: 40px;">
    <p class="muted">ไม่พบข้อมูลการเช่าในขณะนี้</p>
    <a href="{{ Route::has('dresses.find') ? route('dresses.find') : '#' }}" class="btn btn-primary" style="margin-top: 10px;">ค้นหาชุดเช่า</a>
</div>
@endif
@endsection