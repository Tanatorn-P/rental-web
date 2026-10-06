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

        <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 20px;">
            <!-- ฝั่งซ้าย: ฟอร์มข้อมูลหลักของสินค้า -->
            <div class="card card-pad">
                <h3 class="section-title" style="margin-bottom: 16px;">ข้อมูลทั่วไป</h3>

                <!-- ชื่อสินค้า -->
                <div style="margin-bottom: 16px;">
                    <label for="product_name" style="display: block; font-weight: 600; margin-bottom: 6px;">
                        ชื่อสินค้า / ชื่อชุด <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" id="product_name" name="product_name" 
                           value="{{ old('product_name', $product->product_name ?? '') }}" 
                           required 
                           style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-family: inherit;" />
                    @error('product_name')
                        <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- หมวดหมู่ และ ราคาเช่า -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label for="category" style="display: block; font-weight: 600; margin-bottom: 6px;">
                            หมวดหมู่ <span style="color: #ef4444;">*</span>
                        </label>
                        <select id="category" name="category" required style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-family: inherit; background-color: #fff;">
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            <option value="ชุดราตรี" {{ old('category', $product->category ?? '') == 'ชุดราตรี' ? 'selected' : '' }}>ชุดราตรี</option>
                            <option value="ชุดไทย" {{ old('category', $product->category ?? '') == 'ชุดไทย' ? 'selected' : '' }}>ชุดไทย</option>
                            <option value="สูท" {{ old('category', $product->category ?? '') == 'สูท' ? 'selected' : '' }}>สูท</option>
                            <option value="ชุดแฟนซี" {{ old('category', $product->category ?? '') == 'ชุดแฟนซี' ? 'selected' : '' }}>ชุดแฟนซี</option>
                        </select>
                        @error('category')
                            <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rental_price" style="display: block; font-weight: 600; margin-bottom: 6px;">
                            ราคาเช่า / วัน (บาท) <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="number" id="rental_price" name="rental_price" step="0.01" 
                               value="{{ old('rental_price', $product->rental_price ?? '') }}" 
                               required 
                               style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-family: inherit;" />
                        @error('rental_price')
                            <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- รายละเอียดสินค้า -->
                <div style="margin-bottom: 16px;">
                    <label for="description" style="display: block; font-weight: 600; margin-bottom: 6px;">รายละเอียดสินค้าเพิ่มเติม</label>
                    <textarea id="description" name="description" rows="5" 
                              style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-family: inherit; resize: vertical;">{{ old('description', $product->description ?? '') }}</textarea>
                    @error('description')
                        <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- ฝั่งขวา: สถานะ, สต็อก, และรูปภาพ -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                <!-- สถานะและจำนวนคลัง -->
                <div class="card card-pad">
                    <h3 class="section-title" style="margin-bottom: 16px;">สถานะและคลังสินค้า</h3>

                    <div style="margin-bottom: 16px;">
                        <label for="status" style="display: block; font-weight: 600; margin-bottom: 6px;">
                            สถานะสินค้า <span style="color: #ef4444;">*</span>
                        </label>
                        <select id="status" name="status" required style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-family: inherit; background-color: #fff;">
                            <option value="available" {{ old('status', $product->status ?? '') == 'available' ? 'selected' : '' }}>พร้อมเช่า (Available)</option>
                            <option value="rented" {{ old('status', $product->status ?? '') == 'rented' ? 'selected' : '' }}>ถูกเช่าอยู่ (Rented)</option>
                            <option value="maintenance" {{ old('status', $product->status ?? '') == 'maintenance' ? 'selected' : '' }}>ซ่อมบำรุง/ส่งซัก (Maintenance)</option>
                        </select>
                        @error('status')
                            <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label for="stock_quantity" style="display: block; font-weight: 600; margin-bottom: 6px;">จำนวนในสต็อก (ตัว)</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" min="0" 
                               value="{{ old('stock_quantity', $product->stock_quantity ?? 1) }}" 
                               style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-family: inherit;" />
                        @error('stock_quantity')
                            <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- การจัดการรูปภาพ -->
                <div class="card card-pad">
                    <h3 class="section-title" style="margin-bottom: 16px;">รูปภาพสินค้า</h3>

                    @if(!empty($product->image_url))
                        <div style="margin-bottom: 12px; text-align: center;">
                            <p class="muted" style="font-size: 12px; margin-bottom: 6px;">รูปภาพปัจจุบัน:</p>
                            <img src="{{ asset($product->image_url) }}" alt="Product Image" style="max-width: 100%; max-height: 180px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;" data-lightbox />
                        </div>
                    @endif

                    <div>
                        <label for="image" style="display: block; font-weight: 600; margin-bottom: 6px;">อัปโหลดรูปภาพใหม่ (ถ้าต้องการเปลี่ยน)</label>
                        <input type="file" id="image" name="image" accept="image/*" style="width: 100%; font-size: 13px;" />
                        @error('image')
                            <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- ปุ่มบันทึก -->
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px; font-weight: 600;">
                    💾 บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </div>
    </form>
@endsection