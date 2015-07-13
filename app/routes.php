<?php

// POST URL to handle form submission and make outbound call
Route::post('/call', function()
{
    // Get form input
    //$number -> $numero de telefono
    //$carpeta -> nombre de la carpeta donde esta el sonido que va a reproducir twilio al llamar
    //$sonido -> nombre del sonido que va a reproducir twilio al llamar
    $number = Input::get('phoneNumber');
    $carpeta = Input::get('carpeta');
    $sonido = Input::get('sonido');


    // Set URL para llamadas salientes - esto debe estar en un servidor publico URL, digase un www.tuempresa.com
    // la variable $host obtiene el dominio de su aplicacion 
    $host = parse_url(Request::url(), PHP_URL_HOST);
    $url = 'http://' . $host . '/outbound'.'/'.$carpeta.'/'.$sonido;

    // Crear authenticated REST client usando las credenciales de tu cuenta twilio; Api key y  Api secret
    // <project root dir>/.env.php
   $client = new Services_Twilio('TWILIO_ACCOUNT_SID', 'TWILIO_AUTH_TOKEN');

    try {
        // Place an outbound call
        $call = $client->account->calls->create(
            '+14693514813', // Telefono de twilio en tu cuenta (ver tu cuenta para verlo)
            $number, // Numero de telefono de la persona a quien va dirigido la llamada
            $url  // url de la REST api de tu aplicacion (basicamente una ruta, que llamada al XML que se le enviara a twilio)
        );
    } catch (Exception $e) {
        // Error en la operacion
        return $e;
    }

    // return a JSON response
    return array('message' => 'Call incoming!',
		'carpeta'=>$carpeta,
		'sonido' =>$sonido
		);
});

// POST URL to handle form submission and make outbound call
// Esta funcion serializa en XML la data que twilio va a recibir para realizar una operacion (En este caso una llamada)
Route::post('/outbound/{carpeta}/{sonido}', function($carpeta, $sonido)
{
    // string del mensaje en el caso de que quieras que twilio lea un texto mediante la voz (solo si tu quieres)
    $sayMessage = 'Su codigo de verificacion es: 54321';

    $twiml = new Services_Twilio_Twiml();
    //descomentar la linea de abajo para reproducir el texto escrito arriba
    //$twiml->say($sayMessage, array('voice' => 'alice'));
    //La linea de abajo hace que se reproduzca el sonido de la url
    $twiml->play('http://tuempresa.com/sounds/'.$carpeta.'/'.$sonido.'.mp3');
    // $response->dial('+16518675309');

    $response = Response::make($twiml, 200);
    $response->header('Content-Type', 'text/xml');
    return $response;
});
