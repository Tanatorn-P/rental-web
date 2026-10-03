@extends('layouts.staff')
@section('title', 'Maintenance')
@section('page-name', 'Maintenance')
@section('nav-maintenance', 'active')

@section('content')
    <div class="page-head">
        <div><p class="eyebrow">Maintenance</p><h2 class="page-title">รายการที่ไม่พร้อมใช้งาน</h2></div>
    </div>

    <div class="card card-pad" style="margin-bottom:18px">
        <form action="{{ route('staff.maintenance.index') }}" method="GET" class="field" style="display:flex;gap:10px;align-items:end">
            <div style="flex:1">
                <label for="product_name">ค้นหาด้วยชื่อชุด</label>
                <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="เว้นว่างเพื่อดูทั้งหมด">
            </div>
            <button type="submit" class="btn btn-secondary">ค้นหา</button>
            <?php if ($search) { ?>
                <a href="{{ route('staff.maintenance.index') }}" class="btn btn-secondary">ล้างตัวกรอง</a>
            <?php } ?>
        </form>
    </div>

    <?php if ($products->isNotEmpty()) { ?>
        <div class="card table-wrap">
            <table class="data-table">
                <thead><tr><th>ชุด</th><th>รายงานเหตุผล</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($products as $row) { ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['product']->product_name, ENT_QUOTES, 'UTF-8') ?></strong></td>
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
        <div class="card card-pad"><p class="muted"><?= $search ? 'ไม่พบชุดที่ตรงกับคำค้นหา' : 'ไม่มีรายการที่ต้องจัดการ' ?></p></div>
    <?php } ?>
@endsection