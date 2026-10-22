<?php

use Illuminate\Support\Facades\Facade;

return [
    'name' => env('APP_NAME', 'Twilio Voice'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'aliases' => Facade::defaultAliases()->merge([
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Controller' => Illuminate\Routing\Controller::class,
        'Input' => Illuminate\Support\Facades\Request::class,
    ])->toArray(),
];
