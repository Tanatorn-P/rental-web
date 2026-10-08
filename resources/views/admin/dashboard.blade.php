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
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">+ เพิ่มสินค้าใหม่</a>
        </div>
    </div>

    <!-- บัตรสรุปสถานะ 6 ช่อง (Top Stats Bar) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; margin-bottom: 24px;">
        <div class="card" style="padding: 16px; background: #fff; border-radius: 14px; border: 1px solid #f5f5f4;">
            <div style="font-size: 26px; font-weight: 700; color: #1c1917;">{{ $totalProducts ?? 0 }}</div>
            <div style="font-size: 12px; color: #78716c; margin-top: 4px;">จำนวนชุดทั้งหมด</div>
        </div>
        <div class="card" style="padding: 16px; background: #fff; border-radius: 14px; border: 1px solid #f5f5f4;">
            <div style="font-size: 26px; font-weight: 700; color: #15803d;">{{ $availableCount ?? 0 }}</div>
            <div style="font-size: 12px; color: #78716c; margin-top: 4px;">พร้อมใช้งาน</div>
        </div>
        <div class="card" style="padding: 16px; background: #fff; border-radius: 14px; border: 1px solid #f5f5f4;">
            <div style="font-size: 26px; font-weight: 700; color: #2563eb;">{{ $reservedCount ?? $totalOrders ?? 0 }}</div>
            <div style="font-size: 12px; color: #78716c; margin-top: 4px;">อยู่ในรายการเช่า</div>
        </div>
        <div class="card" style="padding: 16px; background: #fff; border-radius: 14px; border: 1px solid #f5f5f4;">
            <div style="font-size: 26px; font-weight: 700; color: #b45309;">{{ $rentedCount ?? $activeRentals ?? 0 }}</div>
            <div style="font-size: 12px; color: #78716c; margin-top: 4px;">อยู่ระหว่างเช่า</div>
        </div>
        <div class="card" style="padding: 16px; background: #fff; border-radius: 14px; border: 1px solid #f5f5f4;">
            <div style="font-size: 26px; font-weight: 700; color: #dc2626;">{{ $repairCount ?? $maintenanceCount ?? 0 }}</div>
            <div style="font-size: 12px; color: #78716c; margin-top: 4px;">ไม่พร้อมใช้งาน</div>
        </div>
    </div>

    <!-- ส่วนกลาง: กิจกรรมการเช่า & สินค้าเช่ายอดฮิต -->
    <div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; margin-bottom: 24px;">
        <!-- กราฟกิจกรรมการเช่า (Rental Activity) -->
        <div class="card" style="padding: 20px; background: #fff; border-radius: 16px; border: 1px solid #f5f5f4;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h3 style="font-size: 18px; font-weight: 700; margin: 0; color: #1c1917;">กิจกรรมการเช่า</h3>
                    <p style="font-size: 13px; color: #78716c; margin: 2px 0 0 0;">จำนวนรายการเช่าที่ดำเนินการรายสัปดาห์</p>
                </div>
                <span style="background: #e0e7ff; color: #3730a3; font-size: 12px; padding: 4px 12px; border-radius: 20px; font-weight: 500;">● เดือนนี้</span>
            </div>

            <div style="display: flex; align-items: flex-end; justify-content: space-between; height: 180px; padding: 10px 20px 0 20px; border-bottom: 2px solid #f5f5f4;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; flex: 1;">
                    <div style="width: 60%; background: #e7e5e4; height: 90px; border-radius: 6px 6px 0 0;"></div>
                    <span style="font-size: 12px; color: #a8a29e;">W1</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; flex: 1;">
                    <div style="width: 60%; background: #a8a29e; height: 130px; border-radius: 6px 6px 0 0;"></div>
                    <span style="font-size: 12px; color: #a8a29e;">W2</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; flex: 1;">
                    <div style="width: 60%; background: #e7e5e4; height: 80px; border-radius: 6px 6px 0 0;"></div>
                    <span style="font-size: 12px; color: #a8a29e;">W3</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; flex: 1;">
                    <div style="width: 60%; background: #88626c; height: 160px; border-radius: 6px 6px 0 0;"></div>
                    <span style="font-size: 12px; color: #a8a29e;">W4</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; flex: 1;">
                    <div style="width: 60%; background: #d6c7c8; height: 120px; border-radius: 6px 6px 0 0;"></div>
                    <span style="font-size: 12px; color: #a8a29e;">W5</span>
                </div>
            </div>
        </div>

        <!-- รายการสินค้าเช่ายอดฮิต (Popular Items) -->
        <div class="card" style="padding: 20px; background: #fff; border-radius: 16px; border: 1px solid #f5f5f4;">
            <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 16px 0; color: #1c1917;">สินค้าที่มียอดเช่าสูงสุด</h3>
            @if(!empty($topProducts) && count($topProducts) > 0)
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach($topProducts as $item)
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 600; margin-bottom: 6px; color: #292524;">
                                <span>{{ $item->product_name }}</span>
                                <span>{{ $item->rent_count }} ครั้ง</span>
                            </div>
                            <div style="width: 100%; background: #f5f5f4; height: 8px; border-radius: 10px; overflow: hidden;">
                                <div style="width: {{ min(100, ($item->rent_count ?? 1) * 15) }}%; background: #88626c; height: 100%; border-radius: 10px;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="muted" style="color: #a8a29e; font-size: 14px;">ยังไม่มีข้อมูลสถิติการเช่า</p>
            @endif
        </div>
    </div>

    <!-- ส่วนท้าย: ผู้ใช้งาน & รายการที่ต้องดำเนินการ -->
    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 20px;">
        <!-- จำนวนผู้ใช้งาน (Users) -->
        <div class="card" style="padding: 20px; background: #fff; border-radius: 16px; border: 1px solid #f5f5f4;">
            <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 16px 0; color: #1c1917;">ผู้ใช้งานในระบบ</h3>
            <div style="display: flex; align-items: baseline; gap: 32px;">
                <div>
                    <span style="font-size: 32px; font-weight: 700; color: #1c1917;">{{ $totalCustomers ?? 0 }}</span>
                    <span style="font-size: 14px; color: #78716c; margin-left: 6px;">ลูกค้าที่ใช้งาน</span>
                </div>
                <div>
                    <span style="font-size: 32px; font-weight: 700; color: #1c1917;">{{ $totalStaff ?? 0 }}</span>
                    <span style="font-size: 14px; color: #78716c; margin-left: 6px;">พนักงาน</span>
                </div>
            </div>
        </div>

        <!-- รายการที่ต้องดำเนินการ (Operational Attention) -->
        <div class="card" style="padding: 20px; background: #fff; border-radius: 16px; border: 1px solid #f5f5f4;">
            <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 16px 0; color: #1c1917;">คำสั่งเช่าที่ต้องดำเนินการ</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <span style="background: #fee2e2; color: #991b1b; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                    ● {{ $overdueCount ?? 0 }} เกินกำหนด
                </span>
                <span style="background: #fef3c7; color: #92400e; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                    ● {{ $pendingCount ?? 0 }} รออนุมัติ
                </span>
                <span style="background: #fee2e2; color: #991b1b; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                    ● {{ $inspectionCount ?? 0 }} รอตรวจรับ
                </span>
                <span style="background: #fef3c7; color: #92400e; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                    ● {{ $cleaningCount ?? 0 }} รอทำความสะอาด
                </span>
            </div>
        </div>
    </div>
@endsection