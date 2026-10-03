@extends('layouts.staff')
@section('title', 'Pickup')
@section('page-name', 'Pickup')
@section('nav-pickup', 'active')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Pickup management</p>
            <h2 class="page-title">ยืนยันการส่งมอบชุด</h2>
        </div>
    </div>

    <div class="card card-pad" style="margin-bottom: 18px">
        <form
            action="{{ route('staff.pickup.index') }}"
            method="GET"
            class="field"
            style="display: flex; gap: 10px; align-items: end"
        >
            <div style="flex: 1">
                <label for="order_id">ค้นหาด้วย Order ID</label>
                <input
                    type="text"
                    id="order_id"
                    name="order_id"
                    value="<?= htmlspecialchars($orderId ?? '', ENT_QUOTES, 'UTF-8') ?>"
                />
            </div>
            <button type="submit" class="btn btn-secondary">ค้นหา</button>
        </form>
    </div>

    <?php if ($orderId !== null && $order === null) { ?>
    <div class="card card-pad"><p class="muted">ไม่พบคำสั่งซื้อที่พร้อมส่งมอบ (ต้องเป็นสถานะ อนุมัติแล้ว)</p></div>
    <?php } ?>

    <?php if ($order !== null) { ?>
    <div class="card card-pad">
        <span class="status approved"><?= \App\Helpers\OrderStatusHelper::label($order->order_status) ?></span>        <h3 style="margin: 10px 0 2px">Order #<?= $order->order_id ?></h3>
        <p class="muted" style="font-size: 13px">
            <?= htmlspecialchars($order->customer->fullname ?? '-', ENT_QUOTES, 'UTF-8') ?>
        </p>

        <?php foreach ($order->orderItems() as $item) { ?>
        <p style="font-size: 13px; margin: 4px 0">
            <?= htmlspecialchars(($item['product']->product_name ?? $item['product_id']).' ('.($item['size'] ?? '-').')', ENT_QUOTES, 'UTF-8') ?>
        </p>
        <?php } ?>

        <p style="font-size: 13px">วันรับชุด: <?= $order->pickup_date->format('d M Y') ?> <?= $order->pickup_time ?></p>

        <form action="{{ route('staff.pickup.confirm', $order) }}" method="POST" style="margin-top: 14px">
            @csrf
            <label style="display: block; font-size: 13px; margin-bottom: 6px"><input type="checkbox" required /> ตรวจสอบชุดทั้งหมดแล้ว</label>
            <label style="display: block; font-size: 13px; margin-bottom: 6px"><input type="checkbox" required /> อุปกรณ์ครบ</label>
            <label style="display: block; font-size: 13px; margin-bottom: 14px"><input type="checkbox" required /> ยืนยันตัวตนลูกค้าแล้ว</label>
            <button type="submit" class="btn btn-primary">ยืนยันการส่งมอบ</button>
        </form>
    </div>
    <?php } ?>
@endsection
