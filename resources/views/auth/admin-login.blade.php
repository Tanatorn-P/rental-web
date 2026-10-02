@extends('layouts.guest')
@section('title', 'Admin Login')

@section('content')
    <h2>เข้าสู่ระบบแอดมิน</h2>

    <form action="{{ route('admin.login') }}" method="POST">
        @csrf

        <div class="field">
            <label for="fullname">ชื่อ-นามสกุล</label>
            <input type="text" id="fullname" name="fullname" required autofocus />
        </div>

        <div class="field">
            <label for="password">รหัสผ่าน</label>
            <input type="password" id="password" name="password" required />
        </div>

        <button type="submit" class="btn btn-primary btn-block">เข้าสู่ระบบ</button>
    </form>
@endsection
