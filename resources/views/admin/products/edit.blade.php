@extends('layouts.admin')

@section('title', isset($product) && $product ? 'แก้ไขรายละเอียดสินค้า' : 'เพิ่มสินค้าใหม่')
@section('page-name', isset($product) && $product ? 'Edit Product' : 'Add Product')
@section('nav-products', 'active')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Product Management</p>
            <h2 class="page-title">
                {{ isset($product) && $product ? 'แก้ไขสินค้า #' . ($product->product_id ?? $product->id) : 'เพิ่มสินค้าใหม่' }}
            </h2>
            <p class="page-subtitle">จัดการข้อมูลสินค้า ราคา รูปภาพ และสถานะสินค้าในระบบ</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">← ย้อนกลับ</a>
    </div>

    <form action="{{ isset($product) && $product ? route('admin.products.confirm', $product->product_id) : route('admin.products.confirm') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid edit-product-grid">
            <!-- ฝั่งซ้าย ฟอร์มข้อมูลหลักของสินค้า -->
            <section class="card card-pad">
                <h3 class="section-title section-title-spaced">ข้อมูลทั่วไป</h3>

                <div class="form-group">
                    <label class="form-label">ชื่อสินค้า / ชื่อชุด <span class="required-mark">*</span></label>
                    <input type="text" name="product_name" value="{{ old('product_name', $product->product_name ?? '') }}" required class="form-control" />
                    @error('product_name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- หมวดหมู่ และ สถานะ -->
                <div class="form-row form-group">
                    <div class="form-col">
                        <label class="form-label">หมวดหมู่ <span class="required-mark">*</span></label>
                        <select name="category" required class="form-control">
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            @foreach($categories ?? [] as $cat)
                                <option value="{{ $cat }}" {{ old('category', $product->category ?? '') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-col">
                        <label class="form-label">สถานะสินค้า <span class="required-mark">*</span></label>
                        <select name="status" required class="form-control">
                            @foreach($statuses ?? ['available', 'preparing', 'rented', 'inspection', 'not_ready'] as $status)
                                <option value="{{ $status }}" {{ old('status', $product->status ?? '') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-row form-group">
                    <div class="form-col">
                        <label class="form-label">ค่ามัดจำ (บาท) <span class="required-mark">*</span></label>
                        <input type="number" step="0.01" name="deposit" value="{{ old('deposit', $product->deposit ?? '') }}" required class="form-control" />
                        @error('deposit')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-col">
                        <label class="form-label">ราคาเช่า/วัน (บาท) <span class="required-mark">*</span></label>
                        <input type="number" step="0.01" name="rental_fee" value="{{ old('rental_fee', $product->rental_fee ?? '') }}" required class="form-control" />
                        @error('rental_fee')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group form-group-lg">
                    <label class="form-label">รายละเอียดสินค้าเพิ่มเติม</label>
                    <textarea name="description" rows="5" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <!-- รูปภาพสไลเดอร์ และอัปโหลด -->
            <div class="stack-col-gap-20">
                <div class="card card-pad">
                    <h3 class="section-title section-title-spaced">รูปภาพแกลเลอรี</h3>
                    
                    @if(isset($product) && !empty($product->image) && is_array($product->image) && count($product->image) > 0)
                        <div class="detail-gallery" style="position: relative; width: 100%; height: 220px; overflow: hidden; border-radius: 8px; background: #f8f9fa; border: 1px solid #eee;">
                            @foreach ($product->image as $index => $img)
                                <img src="{{ asset($img) }}" 
                                    alt="{{ $product->product_name }}" 
                                    class="detail-slide {{ $index === 0 ? 'active' : '' }}" 
                                    style="display: {{ $index === 0 ? 'block' : 'none' }}; width: 100%; height: 100%; object-fit: contain;">
                            @endforeach

                            @if (count($product->image) > 1)
                                <button type="button" class="detail-slider-button detail-prev" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: #fff; border: none; border-radius: 50%; width: 28px; height: 28px; cursor: pointer; z-index: 5;">‹</button>
                                <button type="button" class="detail-slider-button detail-next" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: #fff; border: none; border-radius: 50%; width: 28px; height: 28px; cursor: pointer; z-index: 5;">›</button>

                                <div class="detail-dots" style="position: absolute; bottom: 8px; width: 100%; text-align: center; z-index: 5;">
                                    @foreach ($product->image as $index => $img)
                                        <span class="detail-dot {{ $index === 0 ? 'active' : '' }}" style="display: inline-block; width: 7px; height: 7px; margin: 0 2px; background: rgba(255,255,255,0.7); border-radius: 50%; cursor: pointer;"></span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- กรอบแสดงเมื่อไม่มีภาพ -->
                        <div style="width: 100%; height: 220px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border: 1px dashed #ccc; border-radius: 8px; color: #6c757d; font-size: 16px;">
                            ไม่มีภาพ
                        </div>
                    @endif
                </div>

                <!-- อัปโหลดรูปภาพใหม่ -->
                <div class="card card-pad">
                    <h3 class="section-title section-title-spaced">จัดการไฟล์รูปภาพ</h3>
                    <div class="form-group">
                        <label class="form-label">อัปโหลดรูปภาพเพิ่มเติม (เลือกสะสมทีละไฟล์ได้)</label>
                        
                        <!-- ซ่อน input จริงไว้ -->
                        <input type="file" id="product_images_input" name="images[]" multiple accept="image/*" style="display: none;" />
                        
                        <button type="button" id="btn_select_images" class="btn btn-secondary btn-sm">
                            เลือกรูปภาพเพิ่ม
                        </button>

                        <!-- แสดงรายชื่อไฟล์ที่ถูกเลือก -->
                        <div id="selected_files_list" style="margin-top: 10px; font-size: 14px;">
                            <p class="muted" id="no_file_text" style="margin: 0; font-style: italic;">ยังไม่ได้เลือกไฟล์ใหม่</p>
                        </div>

                        @error('images.*')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- ปุ่มบันทึก -->
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    {{ isset($product) && $product ? 'บันทึกการแก้ไขข้อมูล' : 'บันทึกสินค้าใหม่' }}
                </button>
            </div>
        </div>
    </form>

    <!-- Script Slider -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.detail-gallery').forEach(function (gallery) {
                const slides = gallery.querySelectorAll('.detail-slide');
                const dots = gallery.querySelectorAll('.detail-dot');
                const prevButton = gallery.querySelector('.detail-prev');
                const nextButton = gallery.querySelector('.detail-next');

                if (slides.length <= 1) return;

                let current = 0;

                function showSlide(index) {
                    current = (index + slides.length) % slides.length;

                    slides.forEach(function (slide, i) {
                        slide.style.display = (i === current) ? 'block' : 'none';
                        slide.classList.toggle('active', i === current);
                    });

                    dots.forEach(function (dot, i) {
                        dot.style.background = (i === current) ? '#fff' : 'rgba(255,255,255,0.7)';
                        dot.classList.toggle('active', i === current);
                    });
                }

                if (prevButton) {
                    prevButton.addEventListener('click', function () {
                        showSlide(current - 1);
                    });
                }

                if (nextButton) {
                    nextButton.addEventListener('click', function () {
                        showSlide(current + 1);
                    });
                }

                dots.forEach(function (dot, i) {
                    dot.addEventListener('click', function () {
                        showSlide(i);
                    });
                });
            });
        });
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const fileInput = document.getElementById('product_images_input');
        const selectBtn = document.getElementById('btn_select_images');
        const fileListContainer = document.getElementById('selected_files_list');
        const noFileText = document.getElementById('no_file_text');

        // ใช้ DataTransfer สำหรับเก็บและสะสมไฟล์
        let dataTransfer = new DataTransfer();

        selectBtn.addEventListener('click', function() {
            fileInput.click(); // สั่งเปิดหน้าต่างเลือกไฟล์
        });

        fileInput.addEventListener('change', function(e) {
            // นำไฟล์ที่เพิ่งเลือกใหม่ ไปบวกเพิ่มเข้าใน DataTransfer
            for (let i = 0; i < fileInput.files.length; i++) {
                dataTransfer.items.add(fileInput.files[i]);
            }

            // อัปเดตไฟล์ใน input จริงให้เป็นชุดสะสมล่าสุด
            fileInput.files = dataTransfer.files;
            
            // แสดงผลรายการไฟล์ที่เลือก
            renderFileList();
        });

        function renderFileList() {
            fileListContainer.innerHTML = '';

            if (dataTransfer.files.length === 0) {
                fileListContainer.innerHTML = '<p class="muted" id="no_file_text" style="margin: 0; font-style: italic;">ยังไม่ได้เลือกไฟล์ใหม่</p>';
                return;
            }

            // สร้างลิสต์แสดงชื่อไฟล์พร้อมปุ่มกากบาท
            Array.from(dataTransfer.files).forEach((file, index) => {
                const itemDiv = document.createElement('div');
                itemDiv.style.display = 'flex';
                itemDiv.style.alignItems = 'center';
                itemDiv.style.justifyContent = 'space-between';
                itemDiv.style.background = '#f8f9fa';
                itemDiv.style.padding = '6px 10px';
                itemDiv.style.marginBottom = '4px';
                itemDiv.style.borderRadius = '4px';

                const fileNameSpan = document.createElement('span');
                fileNameSpan.textContent = `${file.name}`;
                fileNameSpan.style.overflow = 'hidden';
                fileNameSpan.style.textOverflow = 'ellipsis';
                fileNameSpan.style.whiteSpace = 'nowrap';
                fileNameSpan.style.maxWidth = '80%';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.textContent = '✕ ลบ';
                removeBtn.style.background = '#dc3545';
                removeBtn.style.color = '#fff';
                removeBtn.style.border = 'none';
                removeBtn.style.padding = '2px 6px';
                removeBtn.style.borderRadius = '3px';
                removeBtn.style.cursor = 'pointer';
                removeBtn.style.fontSize = '12px';

                // ฟังก์ชันกดลบไฟล์เฉพาะรายการนั้นออกจากการสะสม
                removeBtn.addEventListener('click', function() {
                    dataTransfer.items.remove(index);
                    fileInput.files = dataTransfer.files;
                    renderFileList();
                });

                itemDiv.appendChild(fileNameSpan);
                itemDiv.appendChild(removeBtn);
                fileListContainer.appendChild(itemDiv);
            });
        }
    });
</script>
@endsection