<<<<<<< HEAD
<?php
use Illuminate\Support\Facades\Auth;
use App\Helpers\OrderStatusHelper;
=======
// <?php 
// use Illuminate\Support\Facades\Auth; 
// $isCustomer = Auth::guard('customer')->check();
// $customerUser = $isCustomer ? Auth::guard('customer')->user() : null;
// $staffUser = Auth::guard('web')->user();
// >>>>>>> origin/main
// ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DressDay')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <div class="dd-app">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">D</div>
                <div>
                    <h1>DressDay</h1>
                    <p>{{ $isCustomer ? 'Customer Portal' : 'Rental Management' }}</p>
                </div>
            </div>

            @if($isCustomer)
                {{-- ================= เมนูสำหรับ CUSTOMER ================= --}}
                <p class="nav-label">Customer Operations</p>
                <a class="nav-item @yield('nav-dashboard')" href="{{ route('customer.dashboard') }}">Dashboard</a>
                <a class="nav-item @yield('nav-reservations')" href="{{ route('reservations.index') }}">การจองของฉัน</a>
                <a class="nav-item @yield('nav-rentals')" href="{{ route('rentals.show') }}">ติดตามสถานะการเช่า</a>
                <a class="nav-item @yield('nav-history')" href="{{ route('history.index') }}">ประวัติการเช่า</a>
                <a class="nav-item @yield('nav-notifications')" href="{{ route('notifications.index') }}">การแจ้งเตือน</a>
                <a class="nav-item @yield('nav-profile')" href="{{ route('profile.index') }}">โปรไฟล์ของฉัน</a>

                <div class="sidebar-bottom">
                    <p class="nav-label" style="padding-top:0">{{ $customerUser->fullname ?? 'ลูกค้า' }}</p>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-item" style="width:100%; color: var(--danger);">ออกจากระบบ</button>
                    </form>
                </div>
            @else
                {{-- ================= เมนูสำหรับ STAFF ================= --}}
                <p class="nav-label">Staff Operations</p>
                <a class="nav-item @yield('nav-dashboard')" href="{{ route('staff.dashboard') }}">Dashboard</a>
                <a class="nav-item @yield('nav-queue')" href="{{ route('staff.queue.index') }}">Reservation Queue</a>
                <a class="nav-item @yield('nav-pickup')" href="{{ route('staff.pickup.index') }}">Pickup</a>
                <a class="nav-item @yield('nav-return')" href="{{ route('staff.return.index') }}">Return</a>
                <a class="nav-item @yield('nav-inspection')" href="{{ route('staff.inspection.index') }}">Inspection</a>
                <a class="nav-item @yield('nav-maintenance')" href="{{ route('staff.maintenance.index') }}">Maintenance</a>

                <div class="sidebar-bottom">
                    <p class="nav-label" style="padding-top:0">{{ $staffUser->fullname ?? '' }}</p>
                    <form action="{{ $staffUser && $staffUser->isAdmin() ? route('admin.logout') : route('staff.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-item" style="width:100%; color: var(--danger);">ออกจากระบบ</button>
                    </form>
                </div>
            @endif
        </aside>

        <main class="main-shell">
            <header class="topbar">
                <div class="crumb">
                    <strong>{{ $isCustomer ? 'Customer' : 'Staff' }}</strong> / @yield('page-name', 'Dashboard')
                </div>
                <div class="avatar">
                    @if($isCustomer)
                        {{ mb_substr($customerUser->fullname ?? 'CU', 0, 2) }}
                    @else
                        ST
                    @endif
                </div>
            </header>
            <div class="content">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>