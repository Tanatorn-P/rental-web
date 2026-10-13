@extends('layouts.staff')
@section('title', 'Return')
@section('page-name', 'Return')
@section('nav-return', 'active')

@section('content')
    <div class="page-head">
        <div><p class="eyebrow">Return management</p><h2 class="page-title">รับคืนชุด</h2></div>
    </div>

    <div class="card card-pad" style="margin-bottom:18px">
        <form action="{{ route('staff.return.index') }}" method="GET" class="field" style="display:flex;gap:10px;align-items:end">
            <div style="flex:1">
                <label for="order_id">ค้นหาด้วย Order ID</label>
                <input type="text" id="order_id" name="order_id" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="เว้นว่างเพื่อดูทั้งหมด">
            </div>
            <button type="submit" class="btn btn-secondary">ค้นหา</button>
            <?php if ($search) { ?>
                <a href="{{ route('staff.return.index') }}" class="btn btn-secondary">ล้างตัวกรอง</a>
            <?php } ?>
        </form>
    </div>

    <?php if ($orders->isNotEmpty()) { ?>
        <div class="grid" style="grid-template-columns:1fr 1fr">
            <?php foreach ($orders as $row) { ?>
                <?php
                    $order = $row['order'];
                    $firstItem = $order->orderItems()->first();
                    $firstImage = ($firstItem && $firstItem['product']) ? ($firstItem['product']->image[0] ?? null) : null;
                ?>
                <div class="card card-pad order-card">
                    <?php if ($firstImage) { ?>
                        <img src="<?= asset($firstImage) ?>" alt="" data-lightbox class="order-card-image" style="cursor:zoom-in">
                    <?php } else { ?>
                        <div class="order-card-image-placeholder">รูปภาพ</div>
                    <?php } ?>

                    <span class="status rented"><?= \App\Helpers\OrderStatusHelper::label($order->order_status) ?></span>
                    <h3 style="margin:10px 0 2px">Order #<?= $order->order_id ?></h3>
                    <p class="muted" style="font-size:13px"><?= htmlspecialchars($order->customer->fullname ?? '-', ENT_QUOTES, 'UTF-8') ?></p>

                    <div class="order-card-items">
                        <?php foreach ($order->orderItems() as $item) { ?>
                            <p><?= htmlspecialchars(($item['product']->product_name ?? $item['product_id']) . ' (' . ($item['size'] ?? $item['product']->size ?? '-') . ')', ENT_QUOTES, 'UTF-8') ?></p>
                        <?php } ?>
                    </div>

                    <p style="font-size:13px">กำหนดคืน: <?= $order->return_date->format('d M Y') ?></p>

                    <?php if ($row['lateDays'] > 0) { ?>
                        <div class="alert alert-error">คืนล่าช้า <?= $row['lateDays'] ?> วัน · ค่าปรับโดยประมาณ ฿<?= number_format($row['penalty']) ?></div>
                    <?php } ?>

                    <form action="{{ route('staff.return.confirm', $order) }}" method="POST" style="margin-top:10px">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">ยืนยันการรับคืน (วันนี้ <?= now()->format('d M Y') ?>)</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="card card-pad"><p class="muted"><?= $search ? 'ไม่พบ Order ที่ตรงกับคำค้นหา' : 'ไม่มีรายการรอรับคืน' ?></p></div>
    <?php } ?>
@endsection