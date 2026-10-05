@extends('layouts.customer')

@section('title', $product->product_name)

@section('content')

    <!-- Back -->
    <div class="detail-back">

        <a href="{{ url()->previous() }}" class="back-link">
            ← กลับ
        </a>

    </div>


    <!-- Product Detail -->
    <div class="detail-page">

        <!-- Images -->
        <div class="detail-gallery">

            <div class="detail-main-image">

                @foreach ($product->image ?? [] as $index => $image)

                    <img
                        src="{{ asset($image) }}"
                        alt="{{ $product->product_name }}"
                        class="detail-slide {{ $index === 0 ? 'active' : '' }}"
                    >

                @endforeach


                @if (count($product->image ?? []) > 1)

                    <button
                        type="button"
                        class="detail-slider-button detail-prev"
                    >
                        ‹
                    </button>

                    <button
                        type="button"
                        class="detail-slider-button detail-next"
                    >
                        ›
                    </button>


                    <div class="detail-dots">

                        @foreach ($product->image ?? [] as $index => $image)

                            <span
                                class="detail-dot {{ $index === 0 ? 'active' : '' }}"
                            ></span>

                        @endforeach

                    </div>

                @endif

            </div>

        </div>


        <!-- Product Information -->
        <div class="detail-info">

            <div class="eyebrow">
                DRESS DETAIL
            </div>


            <h1 class="detail-title">
                {{ $product->product_name }}
            </h1>


            <p class="detail-product-id">
                รหัสสินค้า: {{ $product->product_id }}
            </p>


            <div class="detail-price">

                <span>ราคาเช่า</span>

                <strong>
                    {{ $product->rental_fee }} บาท
                </strong>

            </div>


            <div class="detail-info-card">

                <div class="detail-row">

                    <span>ไซส์</span>

                    <strong>
                        {{ $product->size }}
                    </strong>

                </div>


                <div class="detail-row">

                    <span>เงินมัดจำ</span>

                    <strong>
                        {{ $product->deposit }} บาท
                    </strong>

                </div>


                <div class="detail-row">

                    <span>ระยะเวลาเช่า</span>

                    <strong>
                        {{ $product->rental_duration_days }} วัน
                    </strong>

                </div>


                <div class="detail-row">

                    <span>สถานะ</span>

                    <strong class="detail-status">
                        {{ $product->status }}
                    </strong>

                </div>

            </div>


            <!-- Description -->
            <div class="detail-description">

                <h2>
                    รายละเอียดชุด
                </h2>

                <p>
                    {{ $product->description }}
                </p>

            </div>


            <!-- Action -->
            <a
                href="{{ route('dress.availability', $product->product_id) }}"
                class="availability-button"
            >
                ตรวจสอบวันว่าง
            </a>

        </div>

    </div>


    <!-- Detail Slider -->
    <script>

        document.querySelectorAll('.detail-gallery').forEach(function (gallery) {

            const slides =
                gallery.querySelectorAll('.detail-slide');

            const dots =
                gallery.querySelectorAll('.detail-dot');

            const prevButton =
                gallery.querySelector('.detail-prev');

            const nextButton =
                gallery.querySelector('.detail-next');


            if (slides.length <= 1) {
                return;
            }


            let current = 0;


            function showSlide(index) {

                current =
                    (index + slides.length) % slides.length;


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


            prevButton.addEventListener('click', function () {

                showSlide(current - 1);

            });


            nextButton.addEventListener('click', function () {

                showSlide(current + 1);

            });


            dots.forEach(function (dot, i) {

                dot.addEventListener('click', function () {

                    showSlide(i);

                });

            });

        });

    </script>

@endsection