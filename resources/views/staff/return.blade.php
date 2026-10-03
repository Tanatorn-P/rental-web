@extends('layouts.staff')
@section('title', 'Return')
@section('page-name', 'Return')
@section('nav-return', 'active')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Return management</p>
            <h2 class="page-title">รับคืนชุด</h2>
        </div>
    </div>

    <div class="card card-pad" style="margin-bottom: 18px">
        <form
            action="{{ route('staff.return.index') }}"
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
    <div class="card card-pad"><p class="muted">ไม่พบคำสั่งซื้อที่อยู่ในสถานะกำลังเช่า</p></div>
    <?php } ?>

    <?php if ($order !== null) { ?>
    <div class="card card-pad" style="max-width: 520px">
        <span class="status rented"><?= \App\Helpers\OrderStatusHelper::label($order->order_status) ?></span>        <h3 style="margin: 10px 0 2px">Order #<?= $order->order_id ?></h3>
        <p class="muted" style="font-size: 13px">
            <?= htmlspecialchars($order->customer->fullname ?? '-', ENT_QUOTES, 'UTF-8') ?>
        </p>

        <?php foreach ($order->orderItems() as $item) { ?>
        <p style="font-size: 13px; margin: 4px 0">
            <?= htmlspecialchars(($item['product']->product_name ?? $item['product_id']).' ('.($item['size'] ?? '-').')', ENT_QUOTES, 'UTF-8') ?>
        </p>
        <?php } ?>

        <p style="font-size: 13px">กำหนดคืน: <?= $order->return_date->format('d M Y') ?></p>

        <?php if ($lateDays > 0) { ?>
        <div class="alert alert-error">
            คืนล่าช้า <?= $lateDays ?> วัน · ค่าปรับโดยประมาณ ฿<?= number_format($penalty) ?>
        </div>
        <?php } ?>

        <form action="{{ route('staff.return.confirm', $order) }}" method="POST" class="field" style="margin-top: 10px">
            @csrf
            <label for="return_time">เวลาที่คืนจริง</label>
            <input type="time" id="return_time" name="return_time" required />
            <button type="submit" class="btn btn-primary" style="margin-top: 12px">ยืนยันการรับคืน</button>
        </form>
    </div>
    <?php } ?>
@endsection
