<?php

use App\Helpers\OrderStatusHelper;
use Illuminate\Support\Facades\Auth;

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'DressDay')</title>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Noto+Sans+Thai:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
</head>
<body>
    <div class="dd-app">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">D</div>
                <div>
                    <h1>DressDay</h1>
                    <p>Rental Management</p>
                </div>
            </div>
            <p class="nav-label">Staff Operations</p>
            <a class="nav-item @yield('nav-dashboard')" href="{{ route('staff.dashboard') }}">Dashboard</a>
            <a class="nav-item @yield('nav-queue')" href="{{ route('staff.queue.index') }}">Reservation Queue</a>
            <a class="nav-item @yield('nav-pickup')" href="{{ route('staff.pickup.index') }}">Pickup</a>
            <a class="nav-item @yield('nav-return')" href="{{ route('staff.return.index') }}">Return</a>
            <a class="nav-item @yield('nav-inspection')" href="{{ route('staff.inspection.index') }}">Inspection</a>
            <a class="nav-item @yield('nav-maintenance')" href="{{ route('staff.maintenance.index') }}">Maintenance</a>
            <a class="nav-item @yield('nav-history')" href="{{ route('staff.history.index') }}">History</a>

            <div class="sidebar-bottom">
                <?php $currentStaff = Auth::guard('web')->user(); ?>
                <p class="nav-label" style="padding-top: 0">
                    <?= htmlspecialchars($currentStaff->fullname ?? '', ENT_QUOTES, 'UTF-8') ?>
                </p>
                <form
                    action="{{ $currentStaff && $currentStaff->isAdmin() ? route('admin.logout') : route('staff.logout') }}"
                    method="POST"
                >
                    @csrf
                    <button type="submit" class="nav-item" style="width: 100%">ออกจากระบบ</button>
                </form>
            </div>
        </aside>
        <main class="main-shell">
            <header class="topbar">
                <div class="crumb">
                    <strong>Staff</strong> /
                    @yield('page-name', 'Dashboard')
                </div>
                <div class="avatar">ST</div>
            </header>
            <div class="content">
                <?php if (session('success')) { ?>
                <div class="alert alert-success"><?= htmlspecialchars(session('success'), ENT_QUOTES, 'UTF-8') ?></div>
                <?php } ?>
                <?php if (session('error')) { ?>
                <div class="alert alert-error"><?= htmlspecialchars(session('error'), ENT_QUOTES, 'UTF-8') ?></div>
                <?php } ?>
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
