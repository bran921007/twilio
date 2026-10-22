<?php

return ['twilio' => [
    'sid' => env('TWILIO_ACCOUNT_SID'),
    'token' => env('TWILIO_AUTH_TOKEN'),
    'from' => env('TWILIO_FROM_NUMBER'),
    'sound_url' => env('TWILIO_SOUND_URL', 'https://tuempresa.com/sounds'),
]];
