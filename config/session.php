<?php

return ['driver' => env('SESSION_DRIVER', 'file'), 'lifetime' => 120, 'expire_on_close' => false, 'encrypt' => false, 'files' => storage_path('framework/sessions'), 'cookie' => 'twilio_session', 'path' => '/', 'domain' => env('SESSION_DOMAIN'), 'secure' => env('SESSION_SECURE_COOKIE', true), 'http_only' => true, 'same_site' => 'lax', 'lottery' => [2, 100]];
