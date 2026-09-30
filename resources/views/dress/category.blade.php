<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>{{ $categoryName }}</title>
</head>

<body>

    <h1>ชุดสำหรับ {{ $categoryName }}</h1>

    <a href="{{ route('dress.find') }}">
        กลับไปเลือกหมวดหมู่
    </a>

    <hr>

    @if ($products->count() > 0)

        @foreach ($products as $product)

            <div>

                @foreach ($product->image ?? [] as $image)

                    <img
                        src="{{ asset($image) }}"
                        alt="{{ $product->product_name }}"
                        width="200"
                    >

                @endforeach

                <h2>{{ $product->product_name }}</h2>

                <p>รหัสสินค้า: {{ $product->product_id }}</p>

                {{-- <p>ขนาด: {{ $product->size }}</p> --}}

                <p>ราคาเช่า: {{ $product->rental_fee }} บาท</p>

                <p>เงินมัดจำ: {{ $product->deposit }} บาท</p>

                {{-- <p>ระยะเวลาเช่า: {{ $product->rental_duration_days }} วัน</p> --}}

               <a href="{{ route('dress.product', $product->product_id) }}">
            <button type="button">
                ดูรายละเอียด
            </button>
             <a href="{{ route('dress.product', $product->product_id) }}">
            <button type="button">
                ตรวจสอบวันว่าง
            </button>
             </a>
                <hr>

            </div>

        @endforeach

    @else

        <p>ไม่พบชุดในหมวดหมู่นี้</p>

    @endif

</body>

</html>