@extends('layouts.app')

@section('crumb', 'Dashboard')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">HELLODAY</div>
        <h1 class="page-title">สวัสดี, คุณ{{ $customer->fullname ?? 'ลูกค้า' }}</h1>
        <p class="page-subtitle">ยินดีต้อนรับสู่ระบบเช่าชุด DressDay</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ Route::has('reservations.index') ? route('reservations.index') : '#' }}" class="btn btn-secondary">ดูการจองของฉัน</a>
        <a href="{{ Route::has('dresses.find') ? route('dresses.find') : '#' }}" class="btn btn-primary">ค้นหาชุดสำหรับงานของฉัน</a>
    </div>
</div>

<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
    <!-- ส่วนการเช่าปัจจุบัน -->
    <div class="card card-pad">
        <h2 class="section-title" style="margin-bottom: 16px;">การเช่าของฉัน</h2>
        @if($activeRental)
        @php
        $item = collect($activeRental->orderItems())->first();
        $product = $item['product'] ?? null;
        $images = $product?->image ?? [];
        $firstImage = is_array($images) ? ($images[0] ?? null) : null;
        @endphp
        <div style="display: flex; gap: 18px; align-items: center;">
            <img src="{{ $firstImage ? asset($firstImage) : 'https://via.placeholder.com/120x150' }}"
                alt="Dress" style="width: 110px; height: 140px; object-fit: cover; border-radius: 10px;">
            <div style="flex: 1;">
                <span class="status {{ $activeRental->order_status }}">{{ strtoupper($activeRental->order_status) }}</span>
                <h3 style="font-size: 18px; margin: 8px 0 4px;">{{ $product->product_name ?? 'ชุดเช่า' }}</h3>
                <p class="muted" style="font-size: 12px; margin-bottom: 12px;">
                    ระยะเวลา: {{ optional($activeRental->pickup_date)->format('d M') }} - {{ optional($activeRental->return_date)->format('d M Y') }}
                </p>
                <a href="{{ Route::has('rentals.show') ? route('rentals.show', $activeRental->order_id) : '#' }}" class="btn btn-secondary btn-sm">ติดตามสถานะ</a>
            </div>
        </div>
        @else
        <p class="muted">ขณะนี้ไม่มีรายการเช่าที่อยู่ระหว่างดำเนินการ</p>
        @endif
    </div>

    <!-- ส่วนนับถอยหลังวันงาน -->
    <div class="card card-pad">
        <h2 class="section-title" style="margin-bottom: 16px;">งานเทศกาลที่กำลังจะถึง</h2>
        @forelse($upcomingEvents as $event)
        @php
        $daysLeft = now()->diffInDays($event->event_date, false);
        $firstItem = collect($event->orderItems())->first();
        $productCategory = $firstItem['product']->category ?? 'งานเลี้ยง';
        @endphp
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--line);">
            <div>
                <strong>{{ ucfirst($productCategory) }}</strong>
                <p class="muted" style="font-size: 11px; margin: 0;">{{ optional($event->event_date)->format('d M Y') }}</p>
            </div>
            <div style="font-weight: 700; color: var(--mauve); font-size: 16px;">
                {{ $daysLeft >= 0 ? $daysLeft . ' วัน' : 'ผ่านไปแล้ว' }}
            </div>
        </div>
        @empty
        <p class="muted" style="font-size: 12px;">ไม่มีงานเทศกาลเร็วๆ นี้</p>
        @endforelse
    </div>
</div>

<!-- ทางลัดหมวดหมู่ -->
<div class="card card-pad" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
        <h2 class="section-title">คุณกำลังหาชุดสำหรับอะไร?</h2>
        <a href="{{ Route::has('dresses.occasions') ? route('dresses.occasions') : '#' }}" style="font-size: 12px; color: var(--mauve); text-decoration: none; font-weight: 700;">ดูทั้งหมด</a>
    </div>
    <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px;">
        @foreach(['Wedding' => '💒', 'Graduation' => '🎓', 'Party' => '🎉', 'Formal' => '👠', 'Photoshoot' => '📸', 'Costume' => '🎭'] as $occ => $icon)
        <a href="{{ Route::has('dresses.occasions') ? route('dresses.occasions', ['category' => strtolower($occ)]) : '#' }}" class="card" style="padding: 14px; text-align: center; text-decoration: none; color: var(--charcoal);">
            <div style="font-size: 24px;">{{ $icon }}</div>
            <div style="font-size: 12px; font-weight: 700; margin-top: 6px;">{{ $occ }}</div>
        </a>
        @endforeach
    </div>
</div>

<!-- แจ้งเตือนล่าสุด -->
<div class="card card-pad">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
        <h2 class="section-title">แจ้งเตือนล่าสุด</h2>
        <a href="{{ Route::has('notifications.index') ? route('notifications.index') : '#' }}" style="font-size: 12px; color: var(--mauve); text-decoration: none; font-weight: 700;">ดูทั้งหมด</a>
    </div>
    @forelse($notifications as $noti)
    <div class="task">
        <div>
            <strong>การจอง #{{ $noti->order_id }}</strong>
            <p>{{ $noti->reject_reason ? 'คำขอจองถูกปฏิเสธ: '.$noti->reject_reason : 'สถานะการจองเปลี่ยนเป็น '.strtoupper($noti->order_status) }}</p>
        </div>
        <span class="muted" style="font-size: 11px;">{{ $noti->updated_at ? $noti->updated_at->diffForHumans() : '-' }}</span>
    </div>
    @empty
    <p class="muted" style="font-size: 12px;">ไม่มีการแจ้งเตือนใหม่</p>
    @endforelse
</div>
@endsection