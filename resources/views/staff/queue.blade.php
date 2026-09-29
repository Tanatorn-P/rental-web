@extends('layouts.app')
@section('title', 'Reservation Queue')
@section('page-name', 'Reservation Queue')
@section('nav-queue', 'active')

@section('content')
    <div class="page-head">
        <div><p class="eyebrow">Reservation queue</p><h2 class="page-title">คำขอจองที่รอดำเนินการ</h2></div>
    </div>

    <?php if ($orders->isNotEmpty()) { ?>
        <div class="card table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>Order ID</th><th>ลูกค้า</th><th>ชุด</th><th>วันงาน</th><th>สถานะ</th><th>จัดการ</th></tr>
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
                            <td><?= $order->event_date->format('d M Y') ?></td>
                            <td><span class="status pending"><?= $order->order_status ?></span></td>
                            <td>
                                <form action="{{ route('staff.queue.approve', $order) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">อนุมัติ</button>
                                </form>
                                <form action="{{ route('staff.queue.reject', $order) }}" method="POST" style="display:inline-flex;gap:6px;align-items:center;margin-left:6px">
                                    @csrf
                                    <input type="text" name="reject_reason" placeholder="เหตุผล" required style="border:1px solid var(--line);border-radius:7px;padding:6px 8px;font-size:12px">
                                    <button type="submit" class="btn btn-danger btn-sm">ปฏิเสธ</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="card card-pad"><p class="muted">ไม่มีคำขอที่รอดำเนินการ</p></div>
    <?php } ?>
@endsection