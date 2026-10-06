@extends('layouts.admin')
@section('title', 'Dashboard & Analytics')
@section('page-name', 'Dashboard')
@section('nav-dashboard', 'active')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Overview · {{ now()->format('d F Y') }}</p>
            <h2 class="page-title">Executive Dashboard</h2>
            <p class="page-subtitle">สรุปภาพรวมรายได้และสถิติการเช่าชุดในระบบ</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">+ เพิ่มสินค้าใหม่</a>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid grid">
        <div class="card stat">
            <div class="num">฿{{ number_format($totalRevenue ?? 0) }}</div>
            <div class="label">Total Revenue (รายได้รวม)</div>
        </div>
        <div class="card stat">
            <div class="num">{{ $totalOrders ?? 0 }}</div>
            <div class="label">Total Rentals (รายการเช่า)</div>
        </div>
        <div class="card stat">
            <div class="num">{{ $totalProducts ?? 0 }}</div>
            <div class="label">Total Products (จำนวนชุด)</div>
        </div>
        <div class="card stat">
            <div class="num">{{ $activeRentals ?? 0 }}</div>
            <div class="label">Active Rentals (กำลังถูกเช่า)</div>
        </div>
    </div>

    {{-- รายละเอียดสถิติย่อ --}}
    <div class="grid" style="grid-template-columns: 1.5fr 1fr; margin-top: 18px">
        <section class="card card-pad">
            <h3 class="section-title">สินค้าเช่ายอดฮิต (Top Rented Items)</h3>
            @if(!empty($topProducts) && count($topProducts) > 0)
                @foreach($topProducts as $item)
                    <div class="task">
                        <div>
                            <strong style="font-size: 14px">{{ $item->product_name }}</strong>
                            <p class="muted" style="margin: 2px 0 0">หมวดหมู่: {{ $item->category }} · เช่าไปแล้ว {{ $item->rent_count }} ครั้ง</p>
                        </div>
                        <span class="priority medium">฿{{ number_format($item->rental_price) }}/วัน</span>
                    </div>
                @endforeach
            @else
                <p class="muted">ยังไม่มีข้อมูลสถิติการเช่า</p>
            @endif
        </section>

        <section class="card card-pad">
            <h3 class="section-title">สรุปสถานะสินค้าในคลัง</h3>
            <div class="schedule-item">
                <div>
                    <strong>พร้อมใช้งาน (Available)</strong>
                    <p class="muted">{{ $availableCount ?? 0 }} รายการ</p>
                </div>
            </div>
            <div class="schedule-item">
                <div>
                    <strong>อยู่ระหว่างเช่า (Rented)</strong>
                    <p class="muted">{{ $rentedCount ?? 0 }} รายการ</p>
                </div>
            </div>
            <div class="schedule-item">
                <div>
                    <strong>ส่งซัก/ซ่อมบำรุง (Maintenance)</strong>
                    <p class="muted">{{ $maintenanceCount ?? 0 }} รายการ</p>
                </div>
            </div>
        </section>
    </div>
@endsection