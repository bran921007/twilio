<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../app/routes.php')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: ['outbound/*']);
        $middleware->alias(['twilio.signature' => App\Http\Middleware\ValidateTwilioSignature::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Framework defaults render JSON errors for requests accepting JSON.
    })->create();
