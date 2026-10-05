@extends('layouts.customer')

@section('title', 'Find a Dress')
@section('nav-find', 'active')

@section('content')




    <div class="page-head">
    <div>
        <div class="eyebrow">OCCASION FINDER</div>

        <h1 class="page-title">
            คุณกำลังหาชุดสำหรับโอกาสอะไร?
        </h1>

        <p class="page-subtitle">
            เลือกหมวดหมู่เพื่อดูชุดที่มีให้เช่า
        </p>
    </div>
</div>
    <hr>

    <div class="filter-card">

    <div class="filter-title">
        ค้นหาชุดที่เหมาะกับคุณ
    </div>

    <form method="GET">
    <select name="size">
        <option value="">ทุกไซส์</option>

        @foreach ($sizes as $size)
            <option value="{{ $size }}" {{ $size == request('size') ? 'selected' : '' }}>
                {{ $size }}
            </option>
        @endforeach
    </select>

    <select name="price">
        <option value="">ทุกช่วงราคา</option>

        <option value="under500" {{ request('price') == 'under500' ? 'selected' : '' }}>
            ต่ำกว่า 500 บาท
        </option>

        <option value="500to1000" {{ request('price') == '500to1000' ? 'selected' : '' }}>
            500 - 1,000 บาท
        </option>

        <option value="over1000" {{ request('price') == 'over1000' ? 'selected' : '' }}>
            มากกว่า 1,000 บาท
        </option>
    </select>
    <label>
    วันที่รับชุด
</label>

<input
    type="date"
    name="pickup_date"
    value="{{ request('pickup_date') }}"
>

<label>
    วันที่คืนชุด
</label>

<input
    type="date"
    name="return_date"
    value="{{ request('return_date') }}"
>

    <button type="submit" class="btn-search">
            ค้นหาชุด
    </button>

    </form>
</div>


    <div class="category-section" id="occasions">

    <div class="section-heading">
        <div class="eyebrow">BROWSE BY OCCASION</div>
        <h2>เลือกหมวดหมู่</h2>
    </div>

    <div class="category-grid">

        @foreach ($categories as $category => $name)

            <a
                href="{{ route('dress.category', $category) }}"
                class="category-card"
            >
                <span>{{ $name }}</span>
                <span class="category-arrow">→</span>
            </a>

        @endforeach

    </div>

</div>

   
   @if ($products->count() > 0)

    <div class="products-section">

        <div class="section-heading">
            <div class="eyebrow">AVAILABLE DRESSES</div>
            <h2>ชุดที่พบ</h2>
        </div>

        <div class="product-grid">

            @foreach ($products as $product)

                <div class="product-card">

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

                    <div class="product-info">

                        <h3>{{ $product->product_name }}</h3>

                        <p class="product-id">
                            รหัสสินค้า: {{ $product->product_id }}
                        </p>

                        <p class="product-size">
                            ไซส์ {{ $product->size }}
                        </p>

                        <div class="product-bottom">

                            <div>
                                <span class="price-label">ราคาเช่า</span>
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

        <p>ไม่พบชุดที่ตรงกับไซส์ที่เลือก</p>

    @endif

    <hr>


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
                slide.classList.toggle('active', i === current);
            });

            dots.forEach(function (dot, i) {
                dot.classList.toggle('active', i === current);
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


