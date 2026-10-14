@extends('layouts.admin')

@section('title', 'แก้ไขรายละเอียดสินค้า')
@section('page-name', 'Edit Product')
@section('nav-products', 'active')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Product Management</p>
            <h2 class="page-title">แก้ไขรายละเอียดสินค้า #{{ $products->product_id ?? $products->id }}</h2>
            <p class="page-subtitle">อัปเดตข้อมูลชุด ราคา รูปภาพ และสถานะสินค้าในระบบ</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">← ย้อนกลับไปหน้ารายการสินค้า</a>
    </div>

    <form action="{{ route('admin.products.update', $products->product_id ?? $products->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid edit-product-grid">
            <!-- ฝั่งซ้าย: ฟอร์มข้อมูลหลักของสินค้า -->
            <div class="card card-pad">
                <h3 class="section-title section-title-spaced">ข้อมูลทั่วไป</h3>

                <!-- ชื่อสินค้า -->
                <div class="form-group-16">
                    <label for="product_name" class="form-label-6">
                        ชื่อสินค้า / ชื่อชุด <span class="required-mark">*</span>
                    </label>
                    <input type="text" id="product_name" name="product_name"
                           value="{{ old('product_name', $product->product_name ?? '') }}"
                           required
                           class="form-control-lg" />
                    @error('product_name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- หมวดหมู่ และ ราคาเช่า -->
                <div class="form-grid-2 form-group-16">
                    <div>
                        <label for="category" class="form-label-6">
                            หมวดหมู่ <span class="required-mark">*</span>
                        </label>
                        <select id="category" name="category" required class="form-control-lg">
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            <option value="ชุดราตรี" {{ old('category', $product->category ?? '') == 'ชุดราตรี' ? 'selected' : '' }}>ชุดราตรี</option>
                            <option value="ชุดไทย" {{ old('category', $product->category ?? '') == 'ชุดไทย' ? 'selected' : '' }}>ชุดไทย</option>
                            <option value="สูท" {{ old('category', $product->category ?? '') == 'สูท' ? 'selected' : '' }}>สูท</option>
                            <option value="ชุดแฟนซี" {{ old('category', $product->category ?? '') == 'ชุดแฟนซี' ? 'selected' : '' }}>ชุดแฟนซี</option>
                        </select>
                        @error('category')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rental_price" class="form-label-6">
                            ราคาเช่า / วัน (บาท) <span class="required-mark">*</span>
                        </label>
                        <input type="number" id="rental_price" name="rental_price" step="0.01"
                               value="{{ old('rental_price', $product->rental_price ?? '') }}"
                               required
                               class="form-control-lg" />
                        @error('rental_price')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- รายละเอียดสินค้า -->
                <div class="form-group-16">
                    <label for="description" class="form-label-6">รายละเอียดสินค้าเพิ่มเติม</label>
                    <textarea id="description" name="description" rows="5"
                              class="form-control-lg form-control-resize">{{ old('description', $product->description ?? '') }}</textarea>
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- ฝั่งขวา: สถานะ, สต็อก, และรูปภาพ -->
            <div class="stack-col-gap-20">

                <!-- สถานะและจำนวนคลัง -->
                <div class="card card-pad">
                    <h3 class="section-title section-title-spaced">สถานะและคลังสินค้า</h3>

                    <div class="form-group-16">
                        <label for="status" class="form-label-6">
                            สถานะสินค้า <span class="required-mark">*</span>
                        </label>
                        <select id="status" name="status" required class="form-control-lg">
                            <option value="available" {{ old('status', $product->status ?? '') == 'available' ? 'selected' : '' }}>พร้อมเช่า (Available)</option>
                            <option value="rented" {{ old('status', $product->status ?? '') == 'rented' ? 'selected' : '' }}>ถูกเช่าอยู่ (Rented)</option>
                            <option value="maintenance" {{ old('status', $product->status ?? '') == 'maintenance' ? 'selected' : '' }}>ซ่อมบำรุง/ส่งซัก (Maintenance)</option>
                        </select>
                        @error('status')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group-16">
                        <label for="stock_quantity" class="form-label-6">จำนวนในสต็อก (ตัว)</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" min="0"
                               value="{{ old('stock_quantity', $product->stock_quantity ?? 1) }}"
                               class="form-control-lg" />
                        @error('stock_quantity')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- การจัดการรูปภาพ -->
                <div class="card card-pad">
                    <h3 class="section-title section-title-spaced">รูปภาพสินค้า</h3>

                    @if(!empty($product->image_url))
                        <div class="image-preview-wrap-center">
                            <p class="muted image-preview-caption">รูปภาพปัจจุบัน:</p>
                            <img src="{{ asset($product->image_url) }}" alt="Product Image" class="image-preview" data-lightbox />
                        </div>
                    @endif

                    <div>
                        <label for="image" class="form-label-6">อัปโหลดรูปภาพใหม่ (ถ้าต้องการเปลี่ยน)</label>
                        <input type="file" id="image" name="image" accept="image/*" class="file-input-full" />
                        @error('image')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- ปุ่มบันทึก -->
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    💾 บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </div>
    </form>
@endsection