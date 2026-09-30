<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ตรวจสอบวันว่าง</title>
</head>

<body>

    <h1>ตรวจสอบวันว่าง</h1>

    <h2>{{ $product->product_name }}</h2>

    <p>รหัสสินค้า: {{ $product->product_id }}</p>

    <p>
        ระยะเวลาเช่า:
        {{ $product->rental_duration_days }} วัน
    </p>

    <hr>

  <form method="GET">

    <label>
        วันที่รับชุด
    </label>
    <input type="date" name="pickup_date" required>

    <br><br>

    <label>
        วันที่คืนชุด
    </label>
    <input type="date" name="return_date" required>

    <br><br>

    <button type="submit">
        ตรวจสอบวันว่าง
    </button>
    <a href="{{ url()->previous() }}">
        <button type="button">
            กลับ
        </button>
    </a>

</form>
@if ($pickupDate && $returnDate)

    <hr>

    @if ($isBooked)
        <h2>ไม่ว่าง</h2>
        <p>ชุดนี้ถูกจองในช่วงวันที่เลือกแล้ว</p>
    @else
        <h2>ว่าง</h2>
        <p>สามารถจองชุดนี้ได้ในช่วงวันที่เลือก</p>
    @endif

@endif

</body>

</html>