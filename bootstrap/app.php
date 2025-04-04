<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureValidSubscription;
use App\Http\Middleware\EnsureJobApplicationOwnership;
use App\Http\Middleware\EnsureReminderOwnership;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'subscription.check' => EnsureValidSubscription::class,
            'job.application.owner.check' => EnsureJobApplicationOwnership::class,
            'reminder.owner.check' => EnsureReminderOwnership::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
