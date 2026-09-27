<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DressDay — เข้าสู่ระบบ</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

</head>
<body>
    <div class="center-screen">
        <div class="card card-pad" style="max-width:420px;text-align:center">
            <div class="brand" style="justify-content:center;margin-bottom:8px">
                <div class="brand-mark">D</div>
                <h1>DressDay</h1>
            </div>
            <p class="muted" style="font-size:13px;margin:0 0 26px">เลือกประเภทผู้ใช้เพื่อเข้าสู่ระบบ</p>

            <a href="{{ route('customer.login') }}" class="role-btn">
                <strong>ลูกค้า</strong>
                <span>จองและติดตามการเช่าชุด</span>
            </a>

            <a href="{{ route('staff.login') }}" class="role-btn">
                <strong>พนักงาน</strong>
                <span>จัดการคำขอจอง รับ-คืนชุด ตรวจสภาพ</span>
            </a>

            <a href="{{ route('admin.login') }}" class="role-btn">
                <strong>แอดมิน</strong>
                <span>จัดการระบบและข้อมูลชุด</span>
            </a>
        </div>
    </div>
</body>
</html>