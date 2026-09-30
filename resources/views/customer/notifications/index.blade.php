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
                <th>งานเทศกาล</th>
                <th>ระยะเวลาการเช่า</th>
                <th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historyList as $item)
                @php
                    $first = collect($item->orderItems())->first();
                    $productName = $first['product']->product_name ?? 'ชุดเช่า';
                @endphp
                <tr>
                    <td><strong>ORD{{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>{{ $productName }}</td>
                    <td>{{ $item->item[0]['occasion'] ?? 'งานทั่วไป' }}</td>
                    <td>{{ optional($item->pickup_date)->format('d M') }} - {{ optional($item->return_date)->format('d M Y') }}</td>
                    <td>
                        <span class="status {{ $item->status }}">
                            {{ strtoupper($item->status) }}
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