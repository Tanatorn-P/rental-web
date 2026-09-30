@extends('layouts.app')

@section('crumb', 'My Reservations')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">MY RESERVATION ORDER</div>
        <h1 class="page-title">การจองของฉัน</h1>
        <p class="page-subtitle">ติดตามและตรวจสอบสถานะการจองชุดทั้งหมด</p>
    </div>
    <a href="{{ route('dresses.find') }}" class="btn btn-primary">ค้นหาชุด</a>
</div>

<div class="card table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>รหัสการจอง</th>
                <th>รายการชุด</th>
                <th>ระยะเวลาการเช่า</th>
                <th>สถานะ</th>
                <th>การกระทำ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $res)
                @php
                    $firstItem = collect($res->orderItems())->first();
                    $productName = $firstItem['product']->product_name ?? 'ชุดเช่า';
                @endphp
                <tr>
                    <td><strong>ORD{{ str_pad($res->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>{{ $productName }}</td>
                    <td>{{ optional($res->pickup_date)->format('d M') }} - {{ optional($res->return_date)->format('d M Y') }}</td>
                    <td>
                        <span class="status {{ $res->status }}">
                            {{ strtoupper($res->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('customer.rentals.show', $res->id) }}" class="btn btn-secondary btn-sm">ดูรายละเอียด</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--gray); padding: 30px;">
                        ยังไม่มีรายการจองในระบบ
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection