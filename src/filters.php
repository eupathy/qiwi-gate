<?php


use FintechFab\QiwiGate\Components\Catalog;
use FintechFab\QiwiGate\Components\Headers;

Route::filter('ff.qiwi.gate.auth.basic', function () {

	$isSuccess = Headers::CheckAuth();
	if (!$isSuccess) {

		$result = array(
			'response' => array(
				'result_code' => Catalog::C_WRONG_AUTH,
			),
		);

		return Response::json(
			$result,
			Catalog::serverCode(Catalog::C_WRONG_AUTH)
		);
	}

});

Route::filter('ff.qiwi.gate.checkUser', function () {

	$routeError = 'qiwiGate_AuthError';
	$routeAction = Route::current()->getAction();
	$isErrorPage = $routeError == $routeAction['as'];

	$user = Config::get('ff-qiwi-gate::user_id');
	$user = (int)$user;

	if ($user <= 0) {

		if (!$isErrorPage) {
			return Redirect::route($routeError);
		}

	} elseif ($isErrorPage) {
		return Redirect::route('qiwiGate_account');
	}


});

