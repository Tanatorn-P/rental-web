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
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">เพิ่มสินค้าใหม่</a>
    </div>

    <!-- Filter Bar -->
    <div class="card card-pad admin-filter-card">
        <form method="GET" action="{{ route('admin.products.index') }}" class="filter-bar">
            <input type="text" name="search" class="filter-input" placeholder="ค้นหาชื่อชุด/รหัสสินค้า" value="{{ request('search') }}" />

            <select name="category" class="filter-select">
                <option value="" style="font-size: 14px; color: #6c757d;">ทุกหมวดหมู่ </option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>

            <select name="status" class="filter-select">
                <option value="" style="font-size: 14px; color: #6c757d;">ทุกสถานะ</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-secondary">ค้นหา</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-clear">ล้างค่า</a>
        </form>
    </div>

    {{-- Product List Table --}}
    <div class="card card-pad">
        <table class="product-table">
            <thead>
                <tr>
                    <th>รูปภาพ</th>
                    <th>ชื่อสินค้า / รหัส</th>
                    <th>หมวดหมู่</th>
                    <th>ราคาเช่า/วัน</th>
                    <th>สถานะ</th>
                    <th class="align-right">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <img src="{{ asset($product->image[0] ?? 'images/placeholder.jpg') }}"
                                class="product-thumb" data-lightbox
                                alt="{{ $product->product_name }}" />
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product->product_id) }}" class="product-name-link">
                                <strong>{{ $product->product_name }}</strong>
                            </a>
                            <p class="muted product-id-muted">#{{ $product->product_id }}</p>
                        </td>
                        <td>{{ $product->category }}</td>
                        <td>฿{{ number_format($product->rental_fee) }}</td>
                        <td>
                            <span class="status {{ $product->status }}">{{ strtoupper($product->status) }}</span>
                        </td>
                        <td class="align-right">
                            <a href="{{ route('admin.products.edit', $product->product_id) }}" class="btn btn-secondary btn-sm">แก้ไข</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted empty-row-cell">ไม่พบข้อมูลสินค้าตรงตามเงื่อนไข</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection