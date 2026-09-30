<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Find a Dress</title>
</head>

<body>

    <h1>คุณกำลังหาชุดสำหรับโอกาสอะไร?</h1>

    <p>เลือกหมวดหมู่เพื่อดูชุดที่มีให้เช่า</p>

    <hr>

   @foreach ($categories as $category => $name)

    <div>
        <a href="{{ route('dress.category', $category) }}">
            {{ $name }}
        </a>
    </div>

    <hr>

@endforeach

</body>

</html>