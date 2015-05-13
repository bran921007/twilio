<?php


use Stripe\Stripe;
class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		return View::make('hello');
	}

	public function payment()
	{

		\Stripe\Stripe::setApiKey('sk_test_IXxOuQruNYUMBapGxGLXnqf6');
		$token = Input::get('token');
		$amount = Input::get('amount');
		try {
		$charge = \Stripe\Charge::create(array('card' => $token, 'amount' => $amount, 'currency' => 'usd'));
	}catch (Error\Card $e){
		return Response::json(array('status' => 'failed'));
	}
		return Response::json(array('status' => 'success'));
	}

}
