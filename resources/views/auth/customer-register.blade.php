@extends('layouts.guest')
@section('title', 'สมัครสมาชิก')

@section('content')
    <h2>สมัครสมาชิก</h2>

    <form action="{{ route('customer.register') }}" method="POST">
        @csrf

        <div class="field">
            <label for="username">ชื่อผู้ใช้</label>
            <input type="text" id="username" name="username" value="<?= old('username') ?>" required />
            <?php if ($errors->has('username')) { ?><small><?= $errors->first('username') ?></small><?php } ?>
        </div>

        <div class="form-grid">
            <div class="field">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required />
                <?php if ($errors->has('password')) { ?><small><?= $errors->first('password') ?></small><?php } ?>
            </div>
            <div class="field">
                <label for="password_confirmation">ยืนยันรหัสผ่าน</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required />
            </div>
        </div>

        <div class="field">
            <label for="fullname">ชื่อ-นามสกุล</label>
            <input type="text" id="fullname" name="fullname" value="<?= old('fullname') ?>" required />
            <?php if ($errors->has('fullname')) { ?><small><?= $errors->first('fullname') ?></small><?php } ?>
        </div>

        <div class="field">
            <label for="phone">เบอร์โทรศัพท์</label>
            <input type="tel" id="phone" name="phone" value="<?= old('phone') ?>" placeholder="08XXXXXXXX" required />
            <?php if ($errors->has('phone')) { ?><small><?= $errors->first('phone') ?></small><?php } ?>
        </div>

        <div class="field">
            <label for="address">ที่อยู่</label>
            <input type="text" id="address" name="address" value="<?= old('address') ?>" />
        </div>

        <div class="field">
            <label for="bank_account">เลขบัญชีธนาคาร</label>
            <input type="text" id="bank_account" name="bank_account" value="<?= old('bank_account') ?>" />
        </div>

        <p style="font-size: 12px; font-weight: 700; margin: 16px 0 8px">ขนาดตัว (ซม.) — ใช้แนะนำไซส์ชุด</p>
        <div class="form-grid">
            <div class="field">
                <label for="bust">รอบอก</label>
                <input type="number" step="0.1" id="bust" name="bust" value="<?= old('bust') ?>" />
            </div>
            <div class="field">
                <label for="shoulder">ไหล่</label>
                <input type="number" step="0.1" id="shoulder" name="shoulder" value="<?= old('shoulder') ?>" />
            </div>
            <div class="field">
                <label for="waist">เอว</label>
                <input type="number" step="0.1" id="waist" name="waist" value="<?= old('waist') ?>" />
            </div>
            <div class="field">
                <label for="hips">สะโพก</label>
                <input type="number" step="0.1" id="hips" name="hips" value="<?= old('hips') ?>" />
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">เข้าสู่ระบบ</button>
    </form>

    <p class="switch-link">มีบัญชีอยู่แล้ว? <a href="{{ route('customer.login') }}">เข้าสู่ระบบ</a></p>
    <p class="switch-link"><a href="{{ route('home') }}">← กลับไปหน้าเลือกประเภทผู้ใช้</a></p>
@endsection
