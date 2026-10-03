@extends('layouts.staff')
@section('title', 'History')
@section('page-name', 'Completed History')
@section('nav-history', 'active')

@section('content')
    <div class="page-head">
        <div><p class="eyebrow">Completed history</p><h2 class="page-title">ประวัติออเดอร์ที่เสร็จสิ้น</h2></div>
    </div>
    <div class="card card-pad" style="margin-bottom:18px">
    <form action="{{ route('staff.history.index') }}" method="GET" class="field" style="display:flex;gap:10px;align-items:end">
        <div style="flex:1">
            <label for="order_id">ค้นหาด้วย Order ID</label>
            <input type="text" id="order_id" name="order_id" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="เว้นว่างเพื่อดูทั้งหมด">
        </div>
        <button type="submit" class="btn btn-secondary">ค้นหา</button>
        <?php if ($search) { ?>
            <a href="{{ route('staff.history.index') }}" class="btn btn-secondary">ล้างตัวกรอง</a>
        <?php } ?>
    </form>
    </div>
    <?php if ($orders->isNotEmpty()) { ?>
        <div class="card table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>Order ID</th><th>ลูกค้า</th><th>ชุด</th><th>วันคืน</th><th>สถานะ</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) { ?>
                        <tr>
                            <td><strong>#<?= $order->order_id ?></strong></td>
                            <td><?= htmlspecialchars($order->customer->fullname ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <?php foreach ($order->orderItems() as $item) { ?>
                                    <div><?= htmlspecialchars(($item['product']->product_name ?? $item['product_id']) . ' (' . ($item['size'] ?? '-') . ')', ENT_QUOTES, 'UTF-8') ?></div>
                                <?php } ?>
                            </td>
                            <td><?= $order->return_date->format('d M Y') ?></td>
                            <td><span class="status available"><?= \App\Helpers\OrderStatusHelper::label($order->order_status) ?></span></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:16px;display:flex;justify-content:center;gap:8px">
            <?php foreach ($orders->links()->elements[0] ?? [] as $page => $url) { ?>
                <a href="<?= $url ?>" class="btn btn-sm <?= $page == $orders->currentPage() ? 'btn-primary' : 'btn-secondary' ?>"><?= $page ?></a>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="card card-pad"><p class="muted">ยังไม่มีประวัติออเดอร์ที่เสร็จสิ้น</p></div>
    <?php } ?>
@endsection