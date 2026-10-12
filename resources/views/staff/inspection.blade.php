@extends('layouts.staff')
@section('title', 'Inspection')
@section('page-name', 'Inspection')
@section('nav-inspection', 'active')

@section('content')
    <div class="page-head">
        <div><p class="eyebrow">Inspection</p><h2 class="page-title">ตรวจสภาพชุด</h2></div>
    </div>

    <div class="card card-pad" style="margin-bottom:18px">
        <form action="{{ route('staff.inspection.index') }}" method="GET" class="field" style="display:flex;gap:10px;align-items:end">
            <div style="flex:1">
                <label for="order_id">ค้นหาด้วย Order ID</label>
                <input type="text" id="order_id" name="order_id" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="เว้นว่างเพื่อดูทั้งหมด">
            </div>
            <button type="submit" class="btn btn-secondary">ค้นหา</button>
            <?php if ($search) { ?>
                <a href="{{ route('staff.inspection.index') }}" class="btn btn-secondary">ล้างตัวกรอง</a>
            <?php } ?>
        </form>
    </div>

     <?php if ($orders->isNotEmpty()) { ?>
        <div class="grid" style="grid-template-columns:1fr 1fr">
            <?php foreach ($orders as $order) { ?>
                <?php
                    $firstItem = $order->orderItems()->first();
                    $firstImage = ($firstItem && $firstItem['product']) ? ($firstItem['product']->image[0] ?? null) : null;
                ?>
                <div class="card card-pad order-card">
                    <?php if ($firstImage) { ?>
                        <img src="<?= asset($firstImage) ?>" alt="" data-lightbox class="order-card-image" style="cursor:zoom-in">
                    <?php } else { ?>
                        <div class="order-card-image-placeholder">รูปภาพ</div>
                    <?php } ?>

                    <span class="status preparing"><?= \App\Helpers\OrderStatusHelper::label($order->order_status) ?></span>
                    <h3 style="margin:10px 0 2px">Order #<?= $order->order_id ?></h3>
                    <p class="muted" style="font-size:13px"><?= htmlspecialchars($order->customer->fullname ?? '-', ENT_QUOTES, 'UTF-8') ?></p>

                    <div class="order-card-items">
                        <?php foreach ($order->orderItems() as $item) { ?>
                            <p><?= htmlspecialchars(($item['product']->product_name ?? $item['product_id']) . ' (' . ($item['size'] ?? $item['product']->size ?? '-') . ')', ENT_QUOTES, 'UTF-8') ?></p>
                        <?php } ?>
                    </div>

                    <form action="{{ route('staff.inspection.store', $order) }}" method="POST" style="margin-top:12px">
                        @csrf
                        <label style="font-size:13px"><input type="radio" name="is_ready" value="1" required> พร้อมใช้งานทั้งหมด</label>
                        <label style="font-size:13px;margin-left:12px"><input type="radio" name="is_ready" value="0" required> มีบางชิ้นเสียหาย</label>
                        <div class="field" style="margin-top:10px">
                            <label for="reject_reason_<?= $order->order_id ?>">รายงานเหตุผล (บังคับถ้าเสียหาย)</label>
                            <textarea id="reject_reason_<?= $order->order_id ?>" name="reject_reason" placeholder="ระบุปัญหาที่พบ"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm" style="margin-top:8px">บันทึกผลตรวจ</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="card card-pad"><p class="muted">ไม่มีชุดรอตรวจสภาพ</p></div>
    <?php } ?>
@endsection