@extends('layouts.app')
@section('title', 'DressDay')
@section('page-name', 'Return')
@section('nav-return', 'active')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Return management</p>
            <h1 class="page-title">ส่งคืนชุด</h1>
            <p class="muted" style="font-size: 13px; margin: 6px 0 0">
                แจ้งเลขพัสดุเมื่อส่งชุดคืนร้าน พนักงานจะตรวจรับและตรวจสภาพชุดต่อไป
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="card card-pad" style="margin-bottom: 18px">
        <form
            action="{{ route('return.index') }}"
            method="GET"
            class="field"
            style="display: flex; gap: 10px; align-items: end"
        >
            <div style="flex: 1">
                <label for="order_id">ค้นหาด้วย Order ID</label>
                <input
                    type="text"
                    id="order_id"
                    name="order_id"
                    value="{{ $search }}"
                    placeholder="เว้นว่างเพื่อดูทั้งหมด"
                >
            </div>
            <button type="submit" class="btn btn-secondary">ค้นหา</button>
            @if ($search)
                <a href="{{ route('return.index') }}" class="btn btn-secondary">ล้างตัวกรอง</a>
            @endif
        </form>
    </div>

    @if ($orders->isNotEmpty())
        <div class="grid" style="grid-template-columns: 1fr 1fr">
            @foreach ($orders as $order)
                @php
                    $daysLeft = (int) now()->startOfDay()->diffInDays($order->return_date, false);
                @endphp

                <div class="card card-pad">
                    <span class="status rented">กำลังเช่า</span>
                    <h3 style="margin: 10px 0 2px">Order #{{ $order->order_id }}</h3>

                    @foreach ($order->orderItems() as $item)
                        @php
                            $images = $item['product']?->image ?? [];
                        @endphp
                        <div style="display: flex; gap: 10px; align-items: center; margin: 8px 0">
                            @if (! empty($images[0]))
                                <img
                                    src="{{ asset($images[0]) }}"
                                    alt=""
                                    style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid var(--line)"
                                >
                            @else
                                <div
                                    style="width: 48px; height: 48px; border-radius: 8px; background: var(--beige); display: flex; align-items: center; justify-content: center; font-size: 10px; color: var(--gray)"
                                >
                                    ไม่มีรูป
                                </div>
                            @endif
                            <p style="font-size: 13px; margin: 0">
                                {{ ($item['product']->product_name ?? $item['product_id']) . ' (' . ($item['size'] ?? $item['product']->size ?? '-') . ')' }}
                            </p>
                        </div>
                    @endforeach

                    <p style="font-size: 13px; margin: 8px 0 0">
                        กำหนดคืนชุด: {{ $order->return_date->format('d M Y') }}
                        @if ($daysLeft < 0)
                            <span style="color: var(--danger); font-weight: 700">
                                (เลยกำหนดมาแล้ว {{ abs($daysLeft) }} วัน)
                            </span>
                        @elseif ($daysLeft === 0)
                            <span style="color: var(--mauve); font-weight: 700">(ครบกำหนดคืนวันนี้)</span>
                        @else
                            <span class="muted">(เหลืออีก {{ $daysLeft }} วัน)</span>
                        @endif
                    </p>

                    @if ($order->tracking_number)
                        <div class="alert alert-success" style="margin: 14px 0 0">
                            แจ้งส่งคืนแล้ว เลขพัสดุ: <strong>{{ $order->tracking_number }}</strong>
                            <br>รอพนักงานตรวจรับชุด
                        </div>
                    @else
                        <form action="{{ route('return.confirm', $order) }}" method="POST" style="margin-top: 14px">
                            @csrf
                            <div class="field">
                                <label for="tracking_{{ $order->order_id }}">เลขพัสดุ (Tracking Number)</label>
                                <input
                                    type="text"
                                    id="tracking_{{ $order->order_id }}"
                                    name="tracking_number"
                                    maxlength="50"
                                    placeholder="เช่น TH1234567890"
                                    required
                                >
                            </div>
                            <label style="display: block; font-size: 13px; margin-bottom: 6px">
                                <input type="checkbox" required> บรรจุชุดและอุปกรณ์ครบแล้ว
                            </label>
                            <label style="display: block; font-size: 13px; margin-bottom: 14px">
                                <input type="checkbox" required> ชุดอยู่ในสภาพเดิม ไม่มีความเสียหายที่ยังไม่ได้แจ้ง
                            </label>
                            <button type="submit" class="btn btn-primary btn-sm">ยืนยันการส่งคืน</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="card card-pad">
            <p class="muted">{{ $search ? 'ไม่พบ Order ที่ตรงกับคำค้นหา' : 'ไม่มีรายการที่ต้องส่งคืน' }}</p>
        </div>
    @endif
@endsection