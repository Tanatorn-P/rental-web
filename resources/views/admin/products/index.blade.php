@extends('layouts.admin')
@section('title', 'Product Catalog')
@section('page-name', 'Product Catalog')
@section('nav-products', 'active')

@section('content')
    <div class="page-head">
        <div>
            <h2 class="page-title">รายการสินค้าทั้งหมด</h2>
            <p class="page-subtitle">จัดการ ค้นหา และตรวจสอบสถานะสินค้าในคลัง</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">+ เพิ่มสินค้าใหม่</a>
    </div>

    {{-- Filter Bar --}}
    <div class="card card-pad" style="margin-bottom: 9px">
        <form method="GET" action="{{ route('admin.products.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="ค้นหาชื่อชุด/รหัสสินค้า..." value="{{ request('search') }}" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc; flex: 1; min-width: 200px;" />
            
            <select name="category" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
                <option value="">-- ทุกหมวดหมู่ --</option>
                @foreach($categories = $products->pluck('category')->unique() as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>

            <select name="status" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
                <option value="">-- ทุกสถานะ --</option>
                @foreach($statuses = $products->pluck('status')->unique() as $status) 
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-secondary">ค้นหา</button>
            <a href="{{ route('admin.products.index') }}" class="btn" style="background: #e2e8f0; color: #333;">ล้างค่า</a>
        </form>
    </div>

    {{-- Product List Table --}}
    <div class="card card-pad">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="border-bottom: 2px solid #edf2f7; text-align: left;">
                    <th style="padding: 10px;">รูปภาพ</th>
                    <th style="padding: 10px;">ชื่อสินค้า / รหัส</th>
                    <th style="padding: 10px;">หมวดหมู่</th>
                    <th style="padding: 10px;">ราคาเช่า/วัน</th>
                    <th style="padding: 10px;">สถานะ</th>
                    <th style="padding: 10px; text-align: right;">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr style="border-bottom: 1px solid #edf2f7;">
                        <td style="padding: 10px;">
                            <img src="{{ asset($product->image[0] ?? 'images/placeholder.jpg') }}" 
                                style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px;" />
                        </td>
                        <td style="padding: 10px;">
                            <strong>{{ $product->product_name }}</strong>
                            <p class="muted" style="margin: 0; font-size: 12px;">#{{ $product->product_id }}</p>
                        </td>
                        <td style="padding: 10px;">{{ $product->category }}</td>
                        <td style="padding: 10px;">฿{{ number_format($product->rental_fee) }}</td>
                        <td style="padding: 10px;">
                            <span class="status {{ $product->status }}">{{ strtoupper($product->status) }}</span>
                        </td>
                        <td style="padding: 10px; text-align: right;">
                            <a href="{{ route('admin.products.edit', $product->product_id) }}" class="btn btn-secondary btn-sm">แก้ไข</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;" class="muted">ไม่พบข้อมูลสินค้าตรงตามเงื่อนไข</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection