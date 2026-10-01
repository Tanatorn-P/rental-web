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

<div class="card table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>รหัสการเช่า</th>
                <th>รายการชุด</th>
                <th>หมวดหมู่</th>
                <th>ระยะเวลาการเช่า</th>
                <th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historyList as $item)
            @php
            $items = collect($item->orderItems());
            $productNames = $items->map(fn($i) => $i['product']->product_name ?? 'ชุดเช่า')->implode(', ');
            $firstCategory = $items->first()['product']->category ?? 'งานทั่วไป';
            @endphp
            <tr>
                <td><strong>{{ $item->order_id }}</strong></td>
                <td>{{ $productNames ?: 'ชุดเช่า' }}</td>
                <td>{{ ucfirst($firstCategory) }}</td>
                <td>{{ optional($item->pickup_date)->format('d M') }} - {{ optional($item->return_date)->format('d M Y') }}</td>
                <td>
                    <span class="status {{ $item->order_status }}">
                        {{ strtoupper($item->order_status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: var(--gray); padding: 30px;">
                    ยังไม่มีประวัติการเช่าในระบบ
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection