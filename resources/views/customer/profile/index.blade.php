@extends('layouts.app')

@section('crumb', 'Profile')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">ACCOUNT</div>
        <h1 class="page-title">โปรไฟล์ของฉัน</h1>
        <p class="page-subtitle">จัดการข้อมูลส่วนตัวและสัดส่วนสำหรับลองชุด</p>
    </div>
</div>

<div class="grid" style="grid-template-columns: 1fr 2fr; gap: 20px;">
    <!-- Profile Card -->
    <div class="card card-pad" style="text-align: center;">
        <div class="avatar" style="width: 80px; height: 80px; font-size: 28px; margin: 0 auto 16px;">
            {{ mb_substr($customer->fullname ?? 'US', 0, 2) }}
        </div>
        <h2 style="font-size: 18px; margin: 0 0 4px;">{{ $customer->fullname ?? 'ผู้ใช้งาน' }}</h2>
        <p class="muted" style="font-size: 12px; margin: 0;">รหัสลูกค้า: {{ $customer->customer_id ?? 'CUST-001' }}</p>
    </div>

    <!-- Profile Form -->
    <div class="card card-pad">
        <form action="{{ Route::has('profile.update') ? route('profile.update') : '#' }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="field">
                    <label>ชื่อ-นามสกุล</label>
                    <input type="text" name="fullname" value="{{ old('fullname', $customer->fullname ?? '') }}" required>
                </div>
                <div class="field">
                    <label>เบอร์โทรศัพท์</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}">
                </div>
            </div>

            <div class="field">
                <label>ที่อยู่จัดส่ง / ติดต่อ</label>
                <textarea name="address">{{ old('address', $customer->address ?? '') }}</textarea>
            </div>

            <div class="field">
                <label>บัญชีธนาคาร (สำหรับรับเงินประกันคืน)</label>
                <input type="text" name="bank_account" value="{{ old('bank_account', $customer->bank_account ?? '') }}">
            </div>

            <h3 style="font-size: 14px; font-weight: 700; margin: 20px 0 10px;">ข้อมูลสัดส่วน (นิ้ว)</h3>
            <div class="form-grid" style="grid-template-columns: repeat(4, 1fr);">
                <div class="field">
                    <label>รอบอก (Bust)</label>
                    <input type="number" step="0.1" name="bust" value="{{ old('bust', $customer->bust ?? '') }}">
                </div>
                <div class="field">
                    <label>ไหล่ (Shoulder)</label>
                    <input type="number" step="0.1" name="shoulder" value="{{ old('shoulder', $customer->shoulder ?? '') }}">
                </div>
                <div class="field">
                    <label>รอบเอว (Waist)</label>
                    <input type="number" step="0.1" name="waist" value="{{ old('waist', $customer->waist ?? '') }}">
                </div>
                <div class="field">
                    <label>สะโพก (Hips)</label>
                    <input type="number" step="0.1" name="hips" value="{{ old('hips', $customer->hips ?? '') }}">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">แก้ไขข้อมูลส่วนตัว</button>
        </form>
    </div>
</div>
@endsection