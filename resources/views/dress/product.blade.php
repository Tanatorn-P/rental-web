<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>{{ $product->product_name }}</title>
</head>

<body>

    <h1>{{ $product->product_name }}</h1>

    @foreach ($product->image ?? [] as $image)

        <img
            src="{{ asset($image) }}"
            alt="{{ $product->product_name }}"
            width="300"
        >

    @endforeach

    <h2>รายละเอียดชุด</h2>

    <p>
        รหัสสินค้า: {{ $product->product_id }}
    </p>

    <p>
        ขนาด: {{ $product->size }}
    </p>

    <p>
        ราคาเช่า: {{ $product->rental_fee }} บาท
    </p>

    <p>
        เงินมัดจำ: {{ $product->deposit }} บาท
    </p>

    <p>
        ระยะเวลาเช่า: {{ $product->rental_duration_days }} วัน
    </p>

    <p>
        สถานะ: {{ $product->status }}
    </p>

    <h3>Description</h3>

    <p>
        {{ $product->description }}
    </p>

    <br>

    <a href="{{ url()->previous() }}">
        <button type="button">
            กลับ
        </button>
    </a>
    <a href="{{ route('dress.availability', $product->product_id) }}">
    ตรวจสอบวันว่าง
</a>

</body>

</html>