@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-name', 'Dashboard')
@section('nav-dashboard', 'active')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Today · {{ now()->format('d F Y') }}</p>
            <h2 class="page-title">Rental Operations</h2>
            <p class="page-subtitle">ภาพรวมงานปฏิบัติการที่ต้องดำเนินการวันนี้</p>
        </div>
        <a href="{{ route('staff.queue.index') }}" class="btn btn-secondary">ดูคิวการจอง</a>
    </div>

    <div class="grid stats-grid">
        <div class="card stat"><div class="num"><?= $pickupToday ?></div><div class="label">Pickup Today</div></div>
        <div class="card stat"><div class="num"><?= $returnToday ?></div><div class="label">Return Today</div></div>
        <div class="card stat"><div class="num"><?= $pendingApproval ?></div><div class="label">Pending Approval</div></div>
        <div class="card stat"><div class="num"><?= $notReady ?></div><div class="label">Not Ready</div></div>
        <div class="card stat"><div class="num"><?= $overdue ?></div><div class="label">Overdue</div></div>
    </div>

    <div class="grid" style="grid-template-columns:1.25fr 1fr;margin-top:18px">
        <section class="card card-pad">
            <h3 class="section-title">งานที่ต้องดำเนินการ</h3>

            <?php foreach ($pendingList as $order) { ?>
                <?php $itemNames = $order->orderItems()->pluck('product.product_name')->filter()->implode(', '); ?>
                <div class="task">
                    <span class="priority medium">MEDIUM</span>
                    <div>
                        <strong style="font-size:13px">Pending Approval · #<?= $order->id ?></strong>
                        <p><?= htmlspecialchars($order->customer->fullname ?? '-', ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($itemNames ?: '-', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <a href="{{ route('staff.queue.index') }}" class="btn btn-secondary btn-sm">ตรวจสอบ</a>
                </div>
            <?php } ?>

            <?php foreach ($overdueList as $order) { ?>
                <div class="task">
                    <span class="priority high">HIGH</span>
                    <div>
                        <strong style="font-size:13px">Overdue Return · #<?= $order->id ?></strong>
                        <p>เกินกำหนด <?= now()->diffInDays($order->return_date) ?> วัน</p>
                    </div>
                    <a href="{{ route('staff.return.index') }}?order_id=<?= $order->id ?>" class="btn btn-secondary btn-sm">จัดการ</a>
                </div>
            <?php } ?>

            <?php if ($pendingList->isEmpty() && $overdueList->isEmpty()) { ?>
                <p class="muted">ไม่มีงานที่ต้องดำเนินการตอนนี้</p>
            <?php } ?>
        </section>

        <section class="card card-pad">
            <h3 class="section-title">Today's Schedule</h3>
            <?php foreach ($todaySchedule as $item) { ?>
                <div class="schedule-item">
                    <span class="schedule-time"><?= htmlspecialchars($item['time'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                    <div>
                        <strong style="font-size:13px"><?= $item['type'] ?> · #<?= $item['order_id'] ?></strong>
                        <p class="muted" style="font-size:12px;margin:2px 0 0"><?= htmlspecialchars($item['customer_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
            <?php } ?>
            <?php if ($todaySchedule->isEmpty()) { ?><p class="muted">วันนี้ไม่มีนัดรับ-คืน</p><?php } ?>
        </section>
    </div>

    <?php if ($notReady > 0) { ?>
        <div class="card card-pad" style="margin-top:18px">
            <span class="status not_ready">Attention</span>
            <p style="margin:10px 0 0;font-size:13px">มีชุดไม่พร้อมใช้งาน <?= $notReady ?> รายการ — <a href="{{ route('staff.maintenance.index') }}">ดูรายการ</a></p>
        </div>
    <?php } ?>
@endsection