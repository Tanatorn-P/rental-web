<?php use Illuminate\Support\Facades\Auth; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DressDay')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/customer.css') }}" rel="stylesheet">
</head>
<body>
    <div class="customer-app">

        <aside class="customer-sidebar">
            <div class="customer-brand">
                <div class="customer-brand-mark">D</div>
                <div>
                    <h1>DressDay</h1>
                    <p>Rental Management</p>
                </div>
            </div>

            <p class="customer-menu-title">Customer</p>

            <nav class="customer-nav">
                <a class="customer-nav-item @yield('nav-find')" href="{{ route('dress.find') }}">Find a Dress</a>
                <a class="customer-nav-item @yield('nav-occasions')" href="{{ route('dress.find') }}#occasions">Occasions</a>
            </nav>

            <div class="customer-sidebar-bottom">
                <a class="customer-nav-item @yield('nav-profile')" href="#">Profile</a>
                <form action="{{ route('customer.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="customer-nav-item customer-logout-button">Logout</button>
                </form>
            </div>
        </aside>

        <main class="customer-main">
            <header class="customer-topbar">
                <div class="customer-breadcrumb"><strong>Customer</strong> / @yield('page-name', 'Find a Dress')</div>
                <div class="customer-avatar">
                    {{ strtoupper(mb_substr(Auth::guard('customer')->user()->fullname ?? 'CU', 0, 2)) }}
                </div>
            </header>

            <div class="customer-content">
                @if (session('success'))
                    <div class="customer-alert customer-alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="customer-alert customer-alert-error">{{ session('error') }}</div>
                @endif

                @yield('content')
            </div>
        </main>

    </div>
</body>
</html>