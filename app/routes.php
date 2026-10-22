<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;

Route::post('/call', function (Request $request) {
    $input = $request->validate([
        'phoneNumber' => ['required', 'string', 'regex:/^\\+[1-9][0-9]{7,14}$/'],
        'carpeta' => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]+$/', 'max:100'],
        'sonido' => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]+$/', 'max:100'],
    ]);

    try {
        app(Client::class)->calls->create($input['phoneNumber'], config('services.twilio.from'), [
            'url' => route('outbound', ['carpeta' => $input['carpeta'], 'sonido' => $input['sonido']]),
        ]);
    } catch (TwilioException $exception) {
        return response()->json(['message' => 'The call provider could not accept the call.'], 502);
    }

    return response()->json([
        'message' => 'Call incoming!', 'carpeta' => $input['carpeta'], 'sonido' => $input['sonido'],
    ]);
});

Route::post('/outbound/{carpeta}/{sonido}', function (string $carpeta, string $sonido) {
    $twiml = new VoiceResponse();
    $twiml->play(rtrim(config('services.twilio.sound_url'), '/').'/'.$carpeta.'/'.$sonido.'.mp3');
    return response((string) $twiml, 200)->header('Content-Type', 'text/xml');
})->where(['carpeta' => '[a-zA-Z0-9_-]+', 'sonido' => '[a-zA-Z0-9_-]+'])
    ->middleware('twilio.signature')->name('outbound');
