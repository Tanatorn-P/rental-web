@extends('layouts.admin')
@section('title', 'ภาพรวมระบบ')
@section('page-name', 'ภาพรวมระบบ')
@section('nav-dashboard', 'active')
@section('content')
    <!-- ส่วนหัวหน้าจอ (Header) -->
    <div class="page-head">
        <div>
            <h2 class="page-title">ภาพรวมระบบ</h2>
            <p class="page-subtitle">สถานะทรัพย์สิน ผู้ใช้ และกิจกรรม</p>
        </div>
        <div>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">เพิ่มสินค้าใหม่</a>
        </div>
    </div>

    <!-- บัตรสรุปสถานะ 5 ช่อง (Top Stats Bar) -->
    <div class="dashboard-stats">
        <div class="card stat-tile"> 
            <div class="stat-tile-value c-stone">{{ $totalProducts ?? 0 }}</div>
            <div class="stat-tile-label">จำนวนชุดทั้งหมด</div>
        </div>
        <div class="card stat-tile">
            <div class="stat-tile-value c-green">{{ $availableCount ?? 0 }}</div>
            <div class="stat-tile-label">พร้อมใช้งาน</div>
        </div>
        <div class="card stat-tile">
            <div class="stat-tile-value c-blue">{{ $inprogressCount ?? 0 }}</div>
            <div class="stat-tile-label">อยู่ในกระบวนการเช่า</div>
        </div>
        <div class="card stat-tile">
            <div class="stat-tile-value c-amber">{{ $withCustomerCount ?? 0 }}</div>
            <div class="stat-tile-label">อยู่กับลูกค้า</div>
        </div>
        <div class="card stat-tile">
            <div class="stat-tile-value c-red">{{ $notReadyCount ?? 0 }}</div>
            <div class="stat-tile-label">ไม่พร้อมให้เช่า</div>
        </div>
    </div>

    <!-- ส่วนกลาง: กราฟรายได้ & สินค้าเช่ายอดฮิต -->
    <div class="dashboard-grid-main">
        <!-- กราฟรายได้รายสัปดาห์ของเดือนที่เลือก-->
        <div class="card dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <h3 class="dashboard-card-title">รายได้</h3>
                    <p class="dashboard-card-subtitle">รายได้รวมค่าเช่าและค่าสินค้าจากออเดอร์ทั้งหมด แบ่งเป็นรายสัปดาห์</p>
                </div>
            </div>

            <div class="month-nav">
                <a href="{{ route('admin.dashboard', ['year' => $chart['prev']['year'], 'month' => $chart['prev']['month']]) }}" class="month-nav-btn" aria-label="เดือนก่อนหน้า">&lsaquo;</a>
                <span class="month-nav-label">{{ $chart['month_label'] }}</span>
                @if($chart['can_go_next'])
                    <a href="{{ route('admin.dashboard', ['year' => $chart['next']['year'], 'month' => $chart['next']['month']]) }}" class="month-nav-btn" aria-label="เดือนถัดไป">&rsaquo;</a>
                @else
                    <span class="month-nav-btn is-disabled" aria-label="เดือนถัดไป">&rsaquo;</span>
                @endif
            </div>

            @php $hasRevenue = collect($chart['values'])->contains(fn ($v) => $v > 0); @endphp

            @if($hasRevenue)
                <div class="chart-bars">
                    @foreach($chart['values'] as $i => $value)
                        @php $pct = $chart['max_value'] > 0 ? max(4, round(($value / $chart['max_value']) * 100)) : 4; @endphp
                        <div class="chart-bar-col">
                            <span class="chart-bar-value">{{ $value > 0 ? '฿'.number_format($value) : '' }}</span>
                            <div class="chart-bar-fill" style="--h: {{ $pct }}%;" title="{{ $chart['labels'][$i] }}: ฿{{ number_format($value) }}"></div>
                        </div>
                    @endforeach
                </div>
                <div class="chart-bar-labels">
                    @foreach($chart['labels'] as $label)
                        <span class="chart-bar-label-item">{{ $label }}</span>
                    @endforeach
                </div>
            @else
                <p class="chart-empty muted">ยังไม่มีข้อมูลรายได้ในเดือนนี้</p>
            @endif
        </div>

        <!-- รายการสินค้าเช่ายอดฮิต (Popular Items) -->
        <div class="card dashboard-card">
            <h3 class="dashboard-card-title dashboard-card-title-spaced">สินค้าที่มียอดเช่าสูงสุด</h3>
            @if(!empty($topProducts) && count($topProducts) > 0)
                <div class="popular-list">
                    @foreach($topProducts as $item)
                        <div class="popular-item">
                            <div class="popular-item-row">
                                <span>{{ $item->product_name }}</span>
                                <span>{{ $item->rent_count }} ครั้ง</span>
                            </div>
                            <div class="popular-item-bar-bg">
                                <div class="popular-item-bar-fill" style="--fill: {{ min(100, ($item->rent_count ?? 1) * 15) }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="muted">ยังไม่มีข้อมูลสถิติการเช่า</p>
            @endif
        </div>
    </div>

    <!-- ส่วนท้าย: ผู้ใช้งาน & รายการที่ต้องดำเนินการ -->
    <div class="dashboard-bottom-grid">
        <!-- จำนวนผู้ใช้งาน (Users) -->
        <div class="card dashboard-card">
            <h3 class="dashboard-card-title dashboard-card-title-spaced">ผู้ใช้งานในระบบ</h3>
            <div class="dashboard-users-row">
                <div>
                    <span class="dashboard-users-num">{{ $totalCustomers ?? 0 }}</span>
                    <span class="dashboard-users-label">ลูกค้าที่ใช้งาน</span>
                </div>
                <div>
                    <span class="dashboard-users-num">{{ $totalStaff ?? 0 }}</span>
                    <span class="dashboard-users-label">พนักงาน</span>
                </div>
            </div>
        </div>

        <!-- รายการที่ต้องดำเนินการ (Operational Attention) -->
        <div class="card dashboard-card">
            <h3 class="dashboard-card-title dashboard-card-title-spaced">คำสั่งเช่าที่ต้องดำเนินการ</h3>
            <div class="dashboard-pills">
                <span class="pill pill-red">
                    ● {{ $overdueCount ?? 0 }} เกินกำหนด
                </span>
                <span class="pill pill-amber">
                    ● {{ $pendingCount ?? 0 }} รออนุมัติ
                </span>
                <span class="pill pill-red">
                    ● {{ $inspectionCount ?? 0 }} รอตรวจรับ
                </span>
                <span class="pill pill-amber">
                    ● {{ $cleaningCount ?? 0 }} รอทำความสะอาด
                </span>
            </div>
        </div>
    </div>
@endsection