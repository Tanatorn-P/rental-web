@extends('layouts.staff')
@section('title', 'Pickup')
@section('page-name', 'Pickup')
@section('nav-pickup', 'active')

@section('content')
    <div class="page-head">
        <div><p class="eyebrow">Pickup management</p><h2 class="page-title">ยืนยันการส่งมอบชุด</h2></div>
    </div>

    <div class="card card-pad" style="margin-bottom:18px">
        <form action="{{ route('staff.pickup.index') }}" method="GET" class="field" style="display:flex;gap:10px;align-items:end">
            <div style="flex:1">
                <label for="order_id">ค้นหาด้วย Order ID</label>
                <input type="text" id="order_id" name="order_id" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="เว้นว่างเพื่อดูทั้งหมด">
            </div>
            <button type="submit" class="btn btn-secondary">ค้นหา</button>
            <?php if ($search) { ?>
                <a href="{{ route('staff.pickup.index') }}" class="btn btn-secondary">ล้างตัวกรอง</a>
            <?php } ?>
        </form>
    </div>

    <?php if ($orders->isNotEmpty()) { ?>
        <div class="grid" style="grid-template-columns:1fr 1fr">
            <?php foreach ($orders as $order) { ?>
                <div class="card card-pad">
                    <span class="status approved"><?= \App\Helpers\OrderStatusHelper::label($order->order_status) ?></span>
                    <h3 style="margin:10px 0 2px">Order #<?= $order->order_id ?></h3>
                    <p class="muted" style="font-size:13px"><?= htmlspecialchars($order->customer->fullname ?? '-', ENT_QUOTES, 'UTF-8') ?></p>

                    <?php foreach ($order->orderItems() as $item) { ?>
                        <p style="font-size:13px;margin:4px 0"><?= htmlspecialchars(($item['product']->product_name ?? $item['product_id']).' ('.($item['size'] ?? '-').')', ENT_QUOTES, 'UTF-8') ?></p>
                    <?php } ?>

                    <p style="font-size:13px">วันรับชุด: <?= $order->pickup_date->format('d M Y') ?> <?= $order->pickup_time ?></p>

                    <form action="{{ route('staff.pickup.confirm', $order) }}" method="POST" style="margin-top:14px">
                        @csrf
                        <label style="display:block;font-size:13px;margin-bottom:6px"><input type="checkbox" required> ตรวจสอบชุดทั้งหมดแล้ว</label>
                        <label style="display:block;font-size:13px;margin-bottom:6px"><input type="checkbox" required> อุปกรณ์ครบ</label>
                        <label style="display:block;font-size:13px;margin-bottom:14px"><input type="checkbox" required> ยืนยันตัวตนลูกค้าแล้ว</label>
                        <button type="submit" class="btn btn-primary btn-sm">ยืนยันการส่งมอบ</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="card card-pad"><p class="muted"><?= $search ? 'ไม่พบ Order ที่ตรงกับคำค้นหา' : 'ไม่มีรายการรอส่งมอบ' ?></p></div>
    <?php } ?>
@endsection