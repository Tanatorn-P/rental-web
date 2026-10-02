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
    <div class="center-screen">
        <div class="auth-card">
            <div class="brand" style="margin-bottom: 20px">
                <div class="brand-mark">D</div>
                <h1>DressDay</h1>
            </div>

            <?php if (session('success')) { ?>
            <div class="alert alert-success"><?= htmlspecialchars(session('success'), ENT_QUOTES, 'UTF-8') ?></div>
            <?php } ?>
            <?php if (session('error')) { ?>
            <div class="alert alert-error"><?= htmlspecialchars(session('error'), ENT_QUOTES, 'UTF-8') ?></div>
            <?php } ?>

            @yield('content')
        </div>
    </div>
</body>
</html>
