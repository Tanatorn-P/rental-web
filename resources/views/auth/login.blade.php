@extends('layouts.guest')
@section('title', 'เข้าสู่ระบบ - DressDay')

@section('content')

{{-- Tab สำหรับเลือก Role --}}
<div style="display: flex; justify-content: center; margin-bottom: 24px;">
    <div class="login-tabs" style="display: inline-flex; background-color: #f2eee9; padding: 4px; border-radius: 12px; gap: 4px;">
        <button type="button" onclick="switchRole('customer')" id="tab-customer" 
            style="border: none; cursor: pointer; padding: 6px 16px; border-radius: 8px; font-size: 13px; font-weight: {{ ($role ?? 'customer') === 'customer' ? '600' : '500' }}; color: {{ ($role ?? 'customer') === 'customer' ? '#111' : '#777' }}; background-color: {{ ($role ?? 'customer') === 'customer' ? '#fff' : 'transparent' }}; box-shadow: {{ ($role ?? 'customer') === 'customer' ? '0 1px 4px rgba(0,0,0,0.06)' : 'none' }}; transition: all 0.2s ease;">
            Customer
        </button>
        <button type="button" onclick="switchRole('staff')" id="tab-staff" 
            style="border: none; cursor: pointer; padding: 6px 16px; border-radius: 8px; font-size: 13px; font-weight: {{ ($role ?? 'customer') === 'staff' ? '600' : '500' }}; color: {{ ($role ?? 'customer') === 'staff' ? '#111' : '#777' }}; background-color: {{ ($role ?? 'customer') === 'staff' ? '#fff' : 'transparent' }}; box-shadow: {{ ($role ?? 'customer') === 'staff' ? '0 1px 4px rgba(0,0,0,0.06)' : 'none' }}; transition: all 0.2s ease;">
            Staff
        </button>
        <button type="button" onclick="switchRole('admin')" id="tab-admin" 
            style="border: none; cursor: pointer; padding: 6px 16px; border-radius: 8px; font-size: 13px; font-weight: {{ ($role ?? 'customer') === 'admin' ? '600' : '500' }}; color: {{ ($role ?? 'customer') === 'admin' ? '#111' : '#777' }}; background-color: {{ ($role ?? 'customer') === 'admin' ? '#fff' : 'transparent' }}; box-shadow: {{ ($role ?? 'customer') === 'admin' ? '0 1px 4px rgba(0,0,0,0.06)' : 'none' }}; transition: all 0.2s ease;">
            Admin
        </button>
    </div>
</div>

    @if(session('error'))
    <?php if (session('error')) { ?>
        <div class="alert alert-error"><?= htmlspecialchars(session('error'), ENT_QUOTES, 'UTF-8') ?></div>
    <?php } ?>
    @endif


    {{-- ฟอร์ม Login หลัก --}}
    <?php if (session('success')) { ?>
        <div class="alert alert-success"><?= htmlspecialchars(session('success'), ENT_QUOTES, 'UTF-8') ?></div>
    <?php } ?>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <input type="hidden" name="role" id="role-input" value="{{ $role ?? 'customer' }}">

        {{-- ช่องกรอกชื่อผู้ใช้ (Customer ใช้ username) --}}
        <div id="field-username" class="field" style="display: {{ ($role ?? 'customer') === 'customer' ? 'block' : 'none' }};">
            <label for="username">ชื่อผู้ใช้ (Username)</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" {{ ($role ?? 'customer') === 'customer' ? 'required' : '' }} autofocus />
        </div>

        {{-- ช่องกรอกชื่อ-นามสกุล (Staff / Admin ใช้ fullname) --}}
        <div id="field-fullname" class="field" style="display: {{ ($role ?? 'customer') !== 'customer' ? 'block' : 'none' }};">
            <label for="fullname">ชื่อผู้ใช้ (Username)</label>
            <input type="text" id="fullname" name="fullname" value="{{ old('fullname') }}" {{ ($role ?? 'customer') !== 'customer' ? 'required' : '' }} autofocus />
        </div>

        <div class="field">
            <label for="password">รหัสผ่าน</label>
            <input type="password" id="password" name="password" required />
        </div>

        <button type="submit" id="submit-btn" class="btn btn-primary btn-block" style="width: 100%; margin-top: 12px;">
            เข้าสู่ระบบ ({{ ucfirst($role ?? 'customer') }})
        </button>
    </form>

    {{-- ข้อความ/ลิงก์ด้านล่างที่จะเปลี่ยนไปตาม Role --}}
    <div id="footer-link" style="text-align: center; margin-top: 16px;">
        @if(($role ?? 'customer') === 'customer')
            <p class="switch-link">ยังไม่มีบัญชี? <a href="{{ route('customer.register') }}">สมัครสมาชิก</a></p>
        @else
            <p class="switch-link">หากไม่สามารถเข้าสู่ระบบได้ กรุณาติดต่อผู้ดูแลระบบ</p>
        @endif
    </div>

    {{-- JavaScript สำหรับสลับ Tab โดยไม่ต้องรีเฟรชหน้า --}}
    <script>
        function switchRole(role) {
            // 1. อัปเดตค่า hidden input role
            document.getElementById('role-input').value = role;

            // 2. ปรับสไตล์ปุ่ม Tab ทั้ง 3 ปุ่ม (Active / Inactive)
            const roles = ['customer', 'staff', 'admin'];
            roles.forEach(r => {
                const btn = document.getElementById('tab-' + r);
                if (r === role) {
                    btn.style.fontWeight = '600';
                    btn.style.color = '#111';
                    btn.style.backgroundColor = '#fff';
                    btn.style.boxShadow = '0 1px 4px rgba(0,0,0,0.06)';
                } else {
                    btn.style.fontWeight = '500';
                    btn.style.color = '#777';
                    btn.style.backgroundColor = 'transparent';
                    btn.style.boxShadow = 'none';
                }
            });

            // 3. สลับช่อง input ระหว่าง username และ fullname พร้อมเปิด/ปิด required
            const fieldUsername = document.getElementById('field-username');
            const fieldFullname = document.getElementById('field-fullname');
            const inputUsername = document.getElementById('username');
            const inputFullname = document.getElementById('fullname');

            if (role === 'customer') {
                fieldUsername.style.display = 'block';
                fieldFullname.style.display = 'none';
                inputUsername.required = true;
                inputFullname.required = false;
            } else {
                fieldUsername.style.display = 'none';
                fieldFullname.style.display = 'block';
                inputUsername.required = false;
                inputFullname.required = true;
            }

            // 4. เปลี่ยนข้อความบนปุ่ม Submit
            const submitBtn = document.getElementById('submit-btn');
            const capitalizedRole = role.charAt(0).toUpperCase() + role.slice(1);
            submitBtn.innerText = 'เข้าสู่ระบบ (' + capitalizedRole + ')';

            // 5. เปลี่ยนลิงก์ช่วยเหลือด้านล่าง
            const footerLink = document.getElementById('footer-link');
            if (role === 'customer') {
                footerLink.innerHTML = '<p class="switch-link">ยังไม่มีบัญชี? <a href="{{ route("customer.register") }}">สมัครสมาชิก</a></p>';
            } else {
                footerLink.innerHTML = '<p class="switch-link">หากไม่สามารถเข้าสู่ระบบได้ กรุณาติดต่อผู้ดูแลระบบ</p>';
            }
        }
    </script>
@endsection