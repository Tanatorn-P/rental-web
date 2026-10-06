@extends('layouts.customer')

@section('title', $categoryName)
@section('nav-occasions', 'active')

@section('content')

    <!-- Page Header -->
    <div class="page-head">

        <div>
            <div class="eyebrow">BROWSE BY OCCASION</div>

            <h1 class="page-title">
                ชุดสำหรับ {{ $categoryName }}
            </h1>

            <p class="page-subtitle">
                เลือกชุดที่ต้องการเพื่อดูรายละเอียดและตรวจสอบวันว่าง
            </p>
        </div>

        <a
            href="{{ route('dress.find') }}"
            class="btn btn-secondary"
        >
            ← กลับไปเลือกหมวดหมู่
        </a>

    </div>


    <!-- Products -->
    @if ($products->count() > 0)

        <div class="products-section">

            <div class="section-heading">
                <div class="eyebrow">AVAILABLE DRESSES</div>

                <h2>
                    {{ $products->count() }} ชุดที่พบ
                </h2>
            </div>


            <div class="product-grid">

                @foreach ($products as $product)

                    <div class="product-card">

                        <!-- Product Images -->
                        <div class="product-image">

                            @foreach ($product->image ?? [] as $index => $image)

                                <img
                                    src="{{ asset($image) }}"
                                    alt="{{ $product->product_name }}"
                                    class="product-slide {{ $index === 0 ? 'active' : '' }}"
                                >

                            @endforeach


                            @if (count($product->image ?? []) > 1)

                                <button
                                    type="button"
                                    class="slider-button slider-prev"
                                >
                                    ‹
                                </button>

                                <button
                                    type="button"
                                    class="slider-button slider-next"
                                >
                                    ›
                                </button>


                                <div class="slider-dots">

                                    @foreach ($product->image ?? [] as $index => $image)

                                        <span
                                            class="slider-dot {{ $index === 0 ? 'active' : '' }}"
                                        ></span>

                                    @endforeach

                                </div>

                            @endif

                        </div>


                        <!-- Product Information -->
                        <div class="product-info">

                            <h3>
                                {{ $product->product_name }}
                            </h3>

                            <p class="product-id">
                                รหัสสินค้า: {{ $product->product_id }}
                            </p>

                            <p class="product-size">
                                ไซส์ {{ $product->size }}
                            </p>


                            <div class="product-bottom">

                                <div>

                                    <span class="price-label">
                                        ราคาเช่า
                                    </span>

                                    <strong>
                                        {{ $product->rental_fee }} บาท
                                    </strong>

                                </div>


                                <a
                                    href="{{ route('dress.product', $product->product_id) }}"
                                    class="detail-button"
                                >
                                    ดูรายละเอียด
                                </a>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    @else

        <div class="card card-pad">

            <p class="muted">
                ไม่พบชุดในหมวดหมู่นี้
            </p>

        </div>

    @endif


    <!-- Image Slider -->
    <script>

        document.querySelectorAll('.product-card').forEach(function (card) {

            const slides = card.querySelectorAll('.product-slide');
            const dots = card.querySelectorAll('.slider-dot');

            const prevButton = card.querySelector('.slider-prev');
            const nextButton = card.querySelector('.slider-next');

            if (slides.length <= 1) {
                return;
            }

            let current = 0;


            function showSlide(index) {

                current = (index + slides.length) % slides.length;

                slides.forEach(function (slide, i) {

                    slide.classList.toggle(
                        'active',
                        i === current
                    );

                });


                dots.forEach(function (dot, i) {

                    dot.classList.toggle(
                        'active',
                        i === current
                    );

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

    </script>

@endsection