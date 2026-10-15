@extends('layouts.admin')

@section('title', 'ตรวจสอบข้อมูลสินค้าก่อนบันทึก')
@section('page-name', 'Confirm Product')
@section('nav-products', 'active')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Product Management</p>
            <h2 class="page-title">ตรวจสอบความถูกต้องก่อนบันทึก</h2>
            <p class="page-subtitle">กรุณาตรวจสอบข้อมูลและรูปภาพสินค้าด้านล่างนี้ให้เรียบร้อยก่อนกดยืนยันบันทึกจริง</p>
        </div>
    </div>

    <div class="card card-pad" style="max-width: 800px; margin: 0 auto;">
        <h3 class="section-title section-title-spaced">สรุปข้อมูลสินค้า (#{{ $pending['product_id'] }})</h3>

        <ul style="list-style: none; padding: 0; line-height: 2;">
            <li><strong>ชื่อสินค้า:</strong> {{ $pending['product_name'] }}</li>
            <li><strong>หมวดหมู่:</strong> {{ $pending['category'] }}</li>
            <li><strong>สถานะ:</strong> <span class="status {{ $pending['status'] }}">{{ strtoupper($pending['status']) }}</span></li>
            <li><strong>ค่ามัดจำ:</strong> ฿{{ number_format($pending['deposit'], 2) }}</li>
            <li><strong>ราคาเช่า/วัน:</strong> ฿{{ number_format($pending['rental_fee'], 2) }}</li>
            <li><strong>รายละเอียด:</strong> {{ $pending['description'] ?: '-' }}</li>
        </ul>

        <h4 style="margin-top: 20px; margin-bottom: 10px;">รูปภาพที่จะบันทึก ({{ count($pending['images']) }} รูป):</h4>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px;">
            @forelse($pending['images'] as $img)
                <div style="width: 100px; height: 100px; border: 1px solid #ddd; border-radius: 6px; overflow: hidden; background: #f8f9fa;">
                    <img src="{{ asset($img) }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            @empty
                <p class="muted">ไม่มีรูปภาพสินค้า</p>
            @endforelse
        </div>

        <!-- ฟอร์มสำหรับกดยืนยัน (Commit) หรือยกเลิก (Cancel) -->
        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <!-- ปุ่มยืนยันบันทึกจริง -->
            <form action="{{ route('admin.products.confirm.commit') }}" method="POST" style="flex: 1;">
                @csrf
                <button type="submit" class="btn btn-primary btn-block btn-lg" style="width: 100%;">
                    ยืนยันและบันทึกข้อมูลลงระบบ
                </button>
            </form>

            <!-- ปุ่มยกเลิก/กลับไปแก้ -->
            <form action="{{ route('admin.products.confirm.cancel') }}" method="POST" style="flex: 1;">
                @csrf
                <button type="submit" class="btn btn-secondary btn-block btn-lg" style="width: 100%; background: #6c757d; color: #fff;">
                    ยกเลิก / กลับไปแก้ไข
                </button>
            </form>
        </div>
    </div>
@endsection