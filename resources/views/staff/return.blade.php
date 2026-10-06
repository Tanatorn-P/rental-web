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

    <div class="card card-pad" style="margin-bottom:18px">
        <form action="{{ route('staff.return.index') }}" method="GET" class="field"
            style="display:flex;gap:10px;align-items:end">
            <div style="flex:1">
                <label for="order_id">ค้นหาด้วย Order ID</label>
                <input type="text" id="order_id" name="order_id"
                    value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="เว้นว่างเพื่อดูทั้งหมด">
            </div>
            <button type="submit" class="btn btn-secondary">ค้นหา</button>
            <?php if ($search) { ?>
            <a href="{{ route('staff.return.index') }}" class="btn btn-secondary">ล้างตัวกรอง</a>
            <?php } ?>
        </form>
    </div>

    <?php if ($orders->isNotEmpty()) { ?>
    <div class="grid" style="grid-template-columns:1fr 1fr">
        <?php    foreach ($orders as $row) { ?>
        <?php        $order = $row['order']; ?>
        <div class="card card-pad">
            <span class="status rented"><?= \App\Helpers\OrderStatusHelper::label($order->order_status) ?></span>
            <h3 style="margin:10px 0 2px">Order #<?= $order->order_id ?></h3>
            <p class="muted" style="font-size:13px">
                <?= htmlspecialchars($order->customer->fullname ?? '-', ENT_QUOTES, 'UTF-8') ?></p>

            <?php        foreach ($order->orderItems() as $item) { ?>
            <?php            $images = $item['product']->image ?? []; ?>
            <div style="display:flex;gap:10px;align-items:center;margin:8px 0">
                <?php            if (!empty($images[0])) { ?>
                <img src="<?= asset($images[0]) ?>" alt=""
                    style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid var(--line)">
                <?php            } else { ?>
                <div
                    style="width:48px;height:48px;border-radius:8px;background:var(--beige);display:flex;align-items:center;justify-content:center;font-size:10px;color:var(--gray)">
                    ไม่มีรูป</div>
                <?php            } ?>
                <p style="font-size:13px;margin:0">
                    <?= htmlspecialchars(($item['product']->product_name ?? $item['product_id']) . ' (' . ($item['size'] ?? '-') . ')', ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
            <?php        } ?>

            <p style="font-size:13px">กำหนดคืน: <?= $order->return_date->format('d M Y') ?></p>

            <?php        if ($row['lateDays'] > 0) { ?>
            <div class="alert alert-error">คืนล่าช้า <?= $row['lateDays'] ?> วัน · ค่าปรับโดยประมาณ
                ฿<?= number_format($row['penalty']) ?></div>
            <?php        } ?>

            <form action="{{ route('staff.return.confirm', $order) }}" method="POST" class="field" style="margin-top:10px">
                @csrf
                <label for="return_time_<?= $order->order_id ?>">เวลาที่คืนจริง</label>
                <input type="time" id="return_time_<?= $order->order_id ?>" name="return_time" required>
                <button type="submit" class="btn btn-primary btn-sm" style="margin-top:8px">ยืนยันการรับคืน</button>
            </form>
        </div>
        <?php    } ?>
    </div>
    <?php } else { ?>
    <div class="card card-pad">
        <p class="muted"><?= $search ? 'ไม่พบ Order ที่ตรงกับคำค้นหา' : 'ไม่มีรายการรอรับคืน' ?></p>
    </div>
    <?php } ?>
@endsection