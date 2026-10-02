@extends('layouts.staff')
@section('title', 'Maintenance')
@section('page-name', 'Maintenance')
@section('nav-maintenance', 'active')

@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Maintenance</p>
            <h2 class="page-title">รายการที่ไม่พร้อมใช้งาน</h2>
        </div>
    </div>

    <?php if ($products->isNotEmpty()) { ?>
    <div class="card table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ชุด</th>
                    <th>รายงานเหตุผล</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $row) { ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($row['product']->product_name, ENT_QUOTES, 'UTF-8') ?></strong>
                    </td>
                    <td><?= htmlspecialchars($row['reject_reason'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <form action="{{ route('staff.maintenance.complete', $row['product']) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">จัดการเสร็จแล้ว (พร้อมใช้งาน)</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } else { ?>
    <div class="card card-pad"><p class="muted">ไม่มีรายการที่ต้องจัดการ</p></div>
    <?php } ?>
@endsection
