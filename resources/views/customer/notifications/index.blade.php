@extends('layouts.app')

@section('crumb', 'Notifications')

@section('content')
    <div class="page-head">
        <div>
            <div class="eyebrow">NOTIFICATIONS</div>
            <h1 class="page-title">การแจ้งเตือนทั้งหมด</h1>
            <p class="page-subtitle">ติดตามการอัปเดตสถานะการจองและการเช่าของคุณ</p>
        </div>
    </div>

    <div class="card card-pad">
        @forelse ($notifications as $noti)
            <div
                class="task"
                style="
                    padding: 14px 0;
                    border-bottom: 1px solid var(--line);
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                "
            >
                <div>
                    <strong>การจอง #{{ $noti->order_id }}</strong>
                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--charcoal)">
                        @include('partials.notification-text', ['order' => $noti])
                    </p>
                    <a
                        href="{{ Route::has('rentals.show') ? route('rentals.show', $noti->order_id) : '#' }}"
                        style="font-size: 12px; color: var(--mauve); text-decoration: none; font-weight: 700"
                    >ดูรายละเอียด</a>
                </div>
                <span class="muted" style="font-size: 11px">
                    {{ $noti->updated_at ? $noti->updated_at->locale('th')->diffForHumans() : '-' }}
                </span>
            </div>
        @empty
            <p class="muted" style="text-align: center; padding: 20px 0">ไม่มีการแจ้งเตือนในขณะนี้</p>
        @endforelse
    </div>
@endsection