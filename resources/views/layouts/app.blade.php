<?php use Illuminate\Support\Facades\Auth; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DressDay')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --ivory:#F8F5F0;--beige:#E9E0D5;--charcoal:#292725;--gray:#706C67;
            --mauve:#9B7C83;--line:#ded7cf;--white:#fffdf9;
            --available:#4F8A67;--pending:#C08A3E;--approved:#54799E;--preparing:#80649B;
            --rented:#5A78A0;--returned:#77736E;--danger:#B05A5A;
        }
        *{box-sizing:border-box} body{margin:0;font-family:"Noto Sans Thai","DM Sans",sans-serif;color:var(--charcoal);background:var(--ivory)}
        button,input,select,textarea{font:inherit} button{cursor:pointer}
        .dd-app{width:100%;min-height:100vh;display:flex}
        .sidebar{width:230px;flex-shrink:0;background:#f0e9e1;border-right:1px solid var(--line);padding:24px 14px;display:flex;flex-direction:column}
        .brand{display:flex;gap:11px;align-items:center;padding:2px 10px 28px}
        .brand-mark{width:34px;height:34px;border-radius:10px;background:var(--charcoal);display:grid;place-items:center;color:white;font-weight:700}
        .brand h1{font-size:19px;margin:0;font-weight:700}.brand p{font-size:11px;color:var(--gray);margin:2px 0 0}
        .nav-label{font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:#8a847e;font-weight:700;padding:10px 10px 6px}
        .nav-item{display:block;border:0;background:transparent;width:100%;text-align:left;padding:10px 11px;border-radius:9px;color:#5e5954;font-size:13px;margin:2px 0;text-decoration:none}
        .nav-item:hover{background:#e5dad0}.nav-item.active{background:#dbc6ca;color:var(--charcoal);font-weight:700}
        .sidebar-bottom{margin-top:auto;border-top:1px solid var(--line);padding-top:12px}
        .main-shell{flex:1;min-width:0}
        .topbar{height:64px;background:rgba(248,245,240,.92);border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 30px}
        .crumb{font-size:13px;color:var(--gray)}.crumb strong{color:var(--charcoal)}
        .avatar{width:34px;height:34px;border-radius:50%;background:#3c3936;color:white;display:grid;place-items:center;font-size:12px;font-weight:700}
        .content{padding:28px 30px 50px;max-width:1400px;margin:auto}
        .page-head{display:flex;justify-content:space-between;align-items:end;gap:20px;margin-bottom:22px;flex-wrap:wrap}
        .eyebrow{color:var(--mauve);font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase}
        .page-title{font-size:26px;font-weight:700;margin:5px 0 4px}.page-subtitle{font-size:13px;color:var(--gray);margin:0}
        .btn{border:1px solid transparent;border-radius:9px;padding:9px 13px;font-size:13px;font-weight:700;display:inline-flex;gap:6px;align-items:center}
        .btn-primary{background:var(--mauve);color:#fff}.btn-secondary{background:var(--white);border-color:var(--line);color:var(--charcoal)}
        .btn-danger{background:var(--danger);color:#fff}.btn-sm{padding:6px 10px;font-size:12px}
        .card{background:var(--white);border:1px solid var(--line);border-radius:14px;box-shadow:0 4px 16px rgba(40,35,30,.05)}
        .card-pad{padding:20px}.section-title{font-size:16px;font-weight:700;margin:0}.muted{color:var(--gray)}
        .grid{display:grid;gap:16px}.stats-grid{grid-template-columns:repeat(5,minmax(0,1fr))}
        .stat{padding:16px}.stat .num{font-size:24px;font-weight:700;margin:8px 0 2px}.stat .label{font-size:12px;color:var(--gray)}
        .status{font-size:11px;font-weight:700;padding:4px 9px;border-radius:999px;display:inline-block}
        .status.pending{color:#9b691e;background:#fbf0dc}.status.approved{color:#3f668d;background:#e6edf5}
        .status.preparing{color:#674a82;background:#eee8f3}.status.rented{color:#46648a;background:#e7edf5}
        .status.available{color:#36724e;background:#e4f1e8}.status.returned,.status.completed{color:#36724e;background:#e4f1e8}
        .status.rejected,.status.not_ready{color:#934444;background:#f8e7e7}
        .table-wrap{overflow:auto}.data-table{width:100%;border-collapse:collapse;min-width:600px}
        .data-table th{text-align:left;background:#eee7df;color:#675f58;font-size:11px;text-transform:uppercase;padding:11px 13px}
        .data-table td{font-size:12px;padding:13px;border-bottom:1px solid #eee8e1}
        .priority{font-size:10px;font-weight:700;padding:4px 7px;border-radius:5px}
        .priority.high{color:#994343;background:#fae7e7}.priority.medium{color:#8e6624;background:#fbefdb}.priority.low{color:#467452;background:#e4f1e8}
        .task{display:grid;grid-template-columns:auto 1fr auto;gap:11px;padding:12px 0;border-bottom:1px solid #ebe5df;align-items:center}
        .task:last-child{border:0}.task p{margin:1px 0 0;font-size:11px;color:var(--gray)}
        .schedule-item{display:flex;gap:12px;padding:12px 0;border-bottom:1px solid #ebe5df}.schedule-item:last-child{border:0}
        .schedule-time{font-weight:700;font-size:12px;width:40px}
        .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        .field label{font-size:12px;font-weight:700;display:block;margin-bottom:5px}
        .field input,.field select,.field textarea{width:100%;border:1px solid var(--line);border-radius:9px;padding:9px 10px;font-size:13px}
        .field textarea{min-height:80px}
        .alert{padding:12px 14px;border-radius:9px;font-size:13px;margin-bottom:16px}
        .alert-success{background:#e4f1e8;color:#36724e}.alert-error{background:#f8e7e7;color:#934444}
        @media(max-width:800px){.sidebar{display:none}.stats-grid{grid-template-columns:repeat(2,1fr)}.form-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <div class="dd-app">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">D</div>
                <div><h1>DressDay</h1><p>Rental Management</p></div>
            </div>
            <p class="nav-label">Staff Operations</p>
            <a class="nav-item @yield('nav-dashboard')" href="{{ route('staff.dashboard') }}">Dashboard</a>
            <a class="nav-item @yield('nav-queue')" href="{{ route('staff.queue.index') }}">Reservation Queue</a>
            <a class="nav-item @yield('nav-pickup')" href="{{ route('staff.pickup.index') }}">Pickup</a>
            <a class="nav-item @yield('nav-return')" href="{{ route('staff.return.index') }}">Return</a>
            <a class="nav-item @yield('nav-inspection')" href="{{ route('staff.inspection.index') }}">Inspection</a>
            <a class="nav-item @yield('nav-maintenance')" href="{{ route('staff.maintenance.index') }}">Maintenance</a>

            <div class="sidebar-bottom">
                <?php $currentStaff = Auth::guard('web')->user(); ?>
                <p class="nav-label" style="padding-top:0"><?= htmlspecialchars($currentStaff->fullname ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <form action="{{ $currentStaff && $currentStaff->isAdmin() ? route('admin.logout') : route('staff.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-item" style="width:100%">ออกจากระบบ</button>
                </form>
            </div>
        </aside>
        <main class="main-shell">
            <header class="topbar">
                <div class="crumb"><strong>Staff</strong> / @yield('page-name', 'Dashboard')</div>
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