<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // กำหนดทิศทางเมื่อผู้ใช้ที่เข้าสู่ระบบอยู่แล้ว พยายามเข้าหน้า Guest (เช่น /customer/login)
        $middleware->redirectUsersTo(function (Request $request) {
            // ถ้าล็อกอินใน guard customer อยู่แล้ว ให้เด้งไป Dashboard ลูกค้า
            if (auth('customer')->check()) {
                return route('customer.dashboard');
            }
            // ถ้าล็อกอินใน guard web (staff/admin) อยู่แล้ว ให้เด้งไป Dashboard พนักงาน
            if (auth('web')->check()) {
                return route('staff.dashboard');
            }

            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
