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

    <div class="grid" style="grid-template-columns: 1.5fr 1fr; gap: 18px;">
        {{-- ฟอร์มข้อมูลสินค้า --}}
        <section class="card card-pad">
            <form action="{{ isset($product) ? route('admin.products.update', $product->product_id) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($product))
                    @method('PUT')
                @endif

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">ชื่อสินค้า</label>
                    <input type="text" name="product_name" value="{{ old('product_name', $product->product_name ?? '') }}" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;" />
                </div>

                <div style="display: flex; gap: 12px; margin-bottom: 14px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-weight: 600; margin-bottom: 4px;">หมวดหมู่</label>
                        <input type="text" name="category" value="{{ old('category', $product->category ?? '') }}" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;" />
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-weight: 600; margin-bottom: 4px;">ราคาเช่า/วัน (บาท)</label>
                        <input type="number" name="rental_price" value="{{ old('rental_price', $product->rental_price ?? '') }}" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;" />
                    </div>
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">รูปภาพสินค้า</label>
                    <input type="file" name="image" style="margin-bottom: 8px;" />
                    @if(isset($product) && $product->image_url)
                        <div style="margin-top: 8px;">
                            <img src="{{ asset($product->image_url) }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px;" />
                        </div>
                    @endif
                </div>

                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 4px;">รายละเอียดสินค้า</label>
                    <textarea name="description" rows="4" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">{{ old('description', $product->description ?? '') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    {{ isset($product) ? 'บันทึกการแก้ไขข้อมูล' : 'บันทึกสินค้าใหม่' }}
                </button>
            </form>
        </section>

        {{-- ส่วนจัดการสต็อก (ปรับเพิ่ม-ลดจำนวน) --}}
        <section class="card card-pad">
            <h3 class="section-title">จัดการจำนวนสต็อกในคลัง</h3>
            
            @if(isset($product))
                <div style="background: #f8fafc; padding: 14px; border-radius: 8px; margin-bottom: 18px; text-align: center;">
                    <p class="muted" style="margin: 0;">จำนวนปัจจุบันในคลัง</p>
                    <h1 style="font-size: 36px; margin: 4px 0; color: #0f172a;">{{ $product->stock_quantity }} <span style="font-size: 16px;">ตัว</span></h1>
                </div>

                <form action="{{ route('admin.products.update-stock', $product->product_id) }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 4px;">การดำเนินการ</label>
                        <select name="stock_action" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
                            <option value="add">+ เพิ่มจำนวนสินค้าเข้าคลัง</option>
                            <option value="reduce">- ลดจำนวนสินค้าในคลัง</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 4px;">จำนวนที่ต้องการปรับเปลี่ยน</label>
                        <input type="number" name="amount" min="1" value="1" required style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;" />
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 4px;">หมายเหตุ / เหตุผล</label>
                        <input type="text" name="note" placeholder="เช่น ซื้อเพิ่ม, ชุดชำรุดตัดสต็อก" style="width: 100%; padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;" />
                    </div>

                    <button type="submit" class="btn btn-secondary" style="width: 100%;">อัปเดตสต็อกด่วน</button>
                </form>

                <hr style="margin: 20px 0; border: none; border-top: 1px solid #e2e8f0;" />

                <form action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" style="width: 100%; background: #ef4444; color: white;">ลบสินค้านี้ออกจากระบบ</button>
                </form>
            @else
                <div style="background: #f8fafc; padding: 14px; border-radius: 8px; text-align: center;">
                    <p class="muted" style="margin: 0;">กรุณาบันทึกข้อมูลสินค้าใหม่ในฟอร์มฝั่งซ้ายก่อนเพื่อกำหนดสต็อกเริ่มต้น</p>
                </div>
            @endif
        </section>
    </div>
@endsection