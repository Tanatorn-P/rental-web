@extends('layouts.app')
@section('title', 'Inspection')
@section('page-name', 'Inspection')
@section('nav-inspection', 'active')

@section('content')
    <div class="page-head"><div><p class="eyebrow">Inspection</p><h2 class="page-title">ตรวจสภาพชุด</h2></div></div>

    <?php if ($orders->isNotEmpty()) { ?>
        <div class="grid" style="grid-template-columns:1fr 1fr">
            <?php foreach ($orders as $order) { ?>
                <div class="card card-pad">
                    <span class="status preparing">รอตรวจสภาพ</span>
                    <h3 style="margin:10px 0 2px">Order #<?= $order->id ?></h3>
                    <p class="muted" style="font-size:13px"><?= htmlspecialchars($order->customer->fullname ?? '-', ENT_QUOTES, 'UTF-8') ?></p>

                    <?php foreach ($order->orderItems() as $item) { ?>
                        <p style="font-size:13px;margin:4px 0"><?= htmlspecialchars(($item['product']->product_name ?? $item['product_id']) . ' (' . ($item['size'] ?? '-') . ')', ENT_QUOTES, 'UTF-8') ?></p>
                    <?php } ?>

                    <form action="{{ route('staff.inspection.store', $order) }}" method="POST" style="margin-top:12px">
                        @csrf
                        <label style="font-size:13px"><input type="radio" name="order_status" value="1" required> พร้อมใช้งานทั้งหมด</label>
                        <label style="font-size:13px;margin-left:12px"><input type="radio" name="order_status" value="0" required> มีบางชิ้นไม่พร้อม</label>
                        <div class="field" style="margin-top:10px">
                            <label for="reject_reason_<?= $order->id ?>">รายงานเหตุผล (บังคับถ้าไม่พร้อม)</label>
                            <textarea id="reject_reason_<?= $order->id ?>" name="reject_reason" placeholder="ระบุว่าชุดไหนมีปัญหาอะไร"></textarea>
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