<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'DressDay Admin')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
</head>

<body>
    <div class="dd-app">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">D</div>
                <div>
                    <h1>DressDay</h1>
                    <p>Admin Management</p>
                </div>
            </div>
            <p class="nav-label">Admin Executive</p>
            <a class="nav-item @yield('nav-dashboard')" href="{{ route('admin.dashboard') }}">Dashboard & Analytics</a>
            <a class="nav-item @yield('nav-products')" href="{{ route('admin.products.index') }}">Product Catalog</a>
            <a class="nav-item @yield('nav-product-create')" href="{{ route('admin.products.create') }}">+ Add New Product</a>

            <div class="sidebar-bottom">
                @php $currentAdmin = Auth::guard('web')->user(); @endphp
                <p class="nav-label" style="padding-top: 0">
                    {{ $currentAdmin->fullname ?? 'Admin Staff' }}
                </p>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-item" style="width: 100%">ออกจากระบบ</button>
                </form>
            </div>
        </aside>

        <main class="main-shell">
            <header class="topbar">
                <div class="crumb">
                    <strong>Admin</strong> / @yield('page-name', 'Dashboard')
                </div>
                <div class="avatar" style="background: #1e293b; color: #fff;">AD</div>
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