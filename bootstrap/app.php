<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias middleware Spatie Laravel Permission (wajib didaftarkan manual)
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        // Pengguna yang sudah login lalu membuka halaman tamu (login/register)
        // diarahkan sesuai perannya: admin/staf ke dashboard, lainnya ke beranda.
        $middleware->redirectUsersTo(
            fn (Request $request) => route($request->user()->rutePulang())
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
