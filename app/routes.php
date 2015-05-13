<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('index');
});

Route::get('/puto/{msg}', function($msg)
{

	$client = new Services_Twilio('AC070cab9c97c0d6bacd59425aa2b1ca64','242475a38db1ae1e9f745890711579b7');
	$message = $client->account->messages->sendMessage(
  '+14693514813', // From a valid Twilio number
  '+13156003423', // Text this number
  $msg
);

	print $message->sid;
});

// POST URL to handle form submission and make outbound call
Route::post('/call', function()
{
    // Get form input
    $number = Input::get('phoneNumber');

    // Set URL for outbound call - this should be your public server URL
    $host = parse_url(Request::url(), PHP_URL_HOST);
    $url = 'http://' . $host . '/outbound';

    // Create authenticated REST client using account credentials in
    // <project root dir>/.env.php
		$client = new Services_Twilio('AC070cab9c97c0d6bacd59425aa2b1ca64','242475a38db1ae1e9f745890711579b7');

    try {
        // Place an outbound call
        $call = $client->account->calls->create(
            '+14693514813', // A Twilio number in your account
            $number, // The visitor's phone number
            $url
        );
    } catch (Exception $e) {
        // Failed calls will throw
        return $e;
    }

    // return a JSON response
    return array('message' => 'Call incoming!');
});

// POST URL to handle form submission and make outbound call
Route::post('/outbound', function()
{
    // A message for Twilio's TTS engine to repeat
    $sayMessage = 'Thanks Ji jai jo.';

    $twiml = new Services_Twilio_Twiml();
    $twiml->say($sayMessage, array('voice' => 'alice'));
		$twiml->play('http://umadbro.io/sounds/sitcom/aplauso1.mp3');
    // $response->dial('+16518675309');

    $response = Response::make($twiml, 200);
    $response->header('Content-Type', 'text/xml');
    return $response;
});
