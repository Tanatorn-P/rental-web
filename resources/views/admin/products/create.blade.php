@extends('layouts.admin')
@section('title', isset($product) ? 'แก้ไขสินค้า' : 'เพิ่มสินค้าใหม่')
@section('page-name', isset($product) ? 'Edit Product' : 'Add Product')
@section('nav-products', 'active')

@section('content')
    <div class="page-head">
        <div>
            <h2 class="page-title">{{ isset($product) ? 'แก้ไขสินค้า #' . $product->product_id : 'เพิ่มสินค้าใหม่' }}</h2>
            <p class="page-subtitle">จัดการข้อมูลสินค้า ราคา รูปภาพ และจำนวนสต็อกในคลัง</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">ย้อนกลับ</a>
    </div>

    <div class="grid create-product-grid">
        {{-- ฟอร์มข้อมูลสินค้า --}}
        <section class="card card-pad">
            <form action="{{ isset($product) ? route('admin.products.update', $product->product_id) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($product))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label class="form-label">ชื่อสินค้า</label>
                    <input type="text" name="product_name" value="{{ old('product_name', $product->product_name ?? '') }}" required class="form-control" />
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label class="form-label">หมวดหมู่</label>
                        <input type="text" name="category" value="{{ old('category', $product->category ?? '') }}" required class="form-control" />
                    </div>
                    <div class="form-col">
                        <label class="form-label">ราคาเช่า/วัน (บาท)</label>
                        <input type="number" name="rental_price" value="{{ old('rental_price', $product->rental_price ?? '') }}" required class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">รูปภาพสินค้า</label>
                    <input type="file" name="image" class="file-input-spaced" />
                    @if(isset($product) && $product->image_url)
                        <div class="image-preview-wrap">
                            <img src="{{ asset($product->image_url) }}" class="image-preview-sm" />
                        </div>
                    @endif
                </div>

                <div class="form-group form-group-lg">
                    <label class="form-label">รายละเอียดสินค้า</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    {{ isset($product) ? 'บันทึกการแก้ไขข้อมูล' : 'บันทึกสินค้าใหม่' }}
                </button>
            </form>
        </section>

        {{-- ส่วนจัดการสต็อก (ปรับเพิ่ม-ลดจำนวน) --}}
        <section class="card card-pad">
            <h3 class="section-title">จัดการจำนวนสต็อกในคลัง</h3>

            @if(isset($product))
                <div class="stock-box stock-box-spaced">
                    <p class="muted stock-box-text">จำนวนปัจจุบันในคลัง</p>
                    <h1 class="stock-amount">{{ $product->stock_quantity }} <span class="stock-unit">ตัว</span></h1>
                </div>

                <form action="{{ route('admin.products.update-stock', $product->product_id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">การดำเนินการ</label>
                        <select name="stock_action" class="form-control">
                            <option value="add">+ เพิ่มจำนวนสินค้าเข้าคลัง</option>
                            <option value="reduce">- ลดจำนวนสินค้าในคลัง</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">จำนวนที่ต้องการปรับเปลี่ยน</label>
                        <input type="number" name="amount" min="1" value="1" required class="form-control" />
                    </div>

                    <div class="form-group form-group-lg">
                        <label class="form-label">หมายเหตุ / เหตุผล</label>
                        <input type="text" name="note" placeholder="เช่น ซื้อเพิ่ม, ชุดชำรุดตัดสต็อก" class="form-control" />
                    </div>

                    <button type="submit" class="btn btn-secondary btn-block">อัปเดตสต็อกด่วน</button>
                </form>

                <hr class="divider" />

                <form action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-block btn-delete">ลบสินค้านี้ออกจากระบบ</button>
                </form>
            @else
                <div class="stock-box">
                    <p class="muted stock-box-text">กรุณาบันทึกข้อมูลสินค้าใหม่ในฟอร์มฝั่งซ้ายก่อนเพื่อกำหนดสต็อกเริ่มต้น</p>
                </div>
            @endif
        </section>
    </div>
@endsection