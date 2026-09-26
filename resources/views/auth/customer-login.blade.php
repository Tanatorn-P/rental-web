@extends('layouts.guest')
@section('title', 'เข้าสู่ระบบ')

@section('content')
    <h2>เข้าสู่ระบบลูกค้า</h2>

    <form action="{{ route('customer.login') }}" method="POST">
        @csrf

        <div class="field">
            <label for="username">ชื่อผู้ใช้</label>
            <input type="text" id="username" name="username" required autofocus>
        </div>

        <div class="field">
            <label for="password">รหัสผ่าน</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">เข้าสู่ระบบ</button>
    </form>

    <p class="switch-link">ยังไม่มีบัญชี? <a href="{{ route('customer.register') }}">สมัครสมาชิก</a></p>
@endsection