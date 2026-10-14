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
                    <p>จัดการระบบ</p>
                </div>
            </div>
            <p class="nav-label">รายการ</p>
            <a class="nav-item @yield('nav-dashboard')" href="{{ route('admin.dashboard') }}">สถิติรวม</a>
            <a class="nav-item @yield('nav-products')" href="{{ route('admin.products.index') }}">รายชื่อสินค้า</a>
            <a class="nav-item @yield('nav-product-create')" href="{{ route('admin.products.create') }}">เพิ่มสินค้าใหม่</a>
            <div class="sidebar-bottom">
                @php $currentAdmin = Auth::guard('web')->user(); @endphp
                <p class="nav-label nav-label-tight">
                    {{ $currentAdmin->fullname ?? 'Admin Staff' }}
                </p>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-item btn-block">ออกจากระบบ</button>
                </form>
            </div>
        </aside>

        <main class="main-shell">
            <header class="topbar">
                <div class="crumb">
                    <strong>แอดมิน</strong> / @yield('page-name', 'สถิติรวม')
                </div>
                <div class="avatar avatar-admin">AD</div>
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

    <div id="lightbox-overlay" class="lightbox-overlay" onclick="this.style.display='none'">
        <img id="lightbox-img" src="" class="lightbox-img" />
    </div>
    <script>
        document.addEventListener('click', function (e) {
            if (e.target.matches('img[data-lightbox]')) {
                document.getElementById('lightbox-img').src = e.target.src;
                document.getElementById('lightbox-overlay').style.display = 'flex';
            }
        });
    </script>
</body>

</html>