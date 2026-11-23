<?php

use App\Http\Middleware\PreventAuthenticatedPageCaching;
use App\Http\Middleware\RoleRedirect;
use App\Http\Middleware\SetDbAuditUser;
use App\Http\Middleware\SwitchRoleDatabaseConnection;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('exam-tokens:rotate')->everyMinute();
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
            'role.redirect' => RoleRedirect::class,
        ]);

        // Otomatis mengganti koneksi DB berdasarkan role user yang login
        $middleware->web(append: [
            SwitchRoleDatabaseConnection::class,
            SetDbAuditUser::class,
            PreventAuthenticatedPageCaching::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();