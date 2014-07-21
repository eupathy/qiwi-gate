<?php

Route::group(array('before' => 'ff.qiwi.gate.auth.basic'), function () {


	Route::resource(
		'qiwi/gate/api/v2/prv/{provider_id}/bills',
		'FintechFab\QiwiGate\Controllers\RestBillController',
		array(
			'only' => array('show', 'update')
		)
	);

	Route::resource(
		'qiwi/gate/api/v2/prv/{provider_id}/bills/{bill_id}/refund',
		'FintechFab\QiwiGate\Controllers\RestRefundController',
		array(
			'only' => array('show', 'update')
		)
	);

});

Route::group(

	array(
		'before'    => 'ff.qiwi.gate.checkUser',
		'prefix'    => 'qiwi/gate',
		'namespace' => 'FintechFab\QiwiGate\Controllers'
	),

	function () {
		Route::get('order/external/main.action', array(
			'as' => 'qiwi_gate_payIndex',
			'uses' => 'PayController@index'
		));

		Route::post('order/external/main.action', array(
			'as' => 'qiwiGate_postPay',
			'uses' => 'PayController@postPay'
		));

		Route::get('account', array(
			'as' => 'qiwiGate_account',
			'uses' => 'AccountController@index'
		));

		Route::post('account', array(
			'as' => 'qiwiGate_postAccountReg',
			'uses' => 'AccountController@postRegistration'
		));

		Route::post('account/changeData', array(
			'as' => 'qiwiGate_postChangeData',
			'uses' => 'AccountController@postChangeData'
		));

		Route::get('account/billsTable', array(
			'as' => 'qiwiGate_billsTable',
			'uses' => 'AccountController@billsTable'
		));

		Route::get('account/billsTable/getRefund/{bill_id}', array(
			'as' => 'qiwiGate_GetRefundTable',
			'uses' => 'AccountController@GetRefundTable'
		));

		Route::post('account/billsTable/expireBill', array(
			'as' => 'qiwiGate_postExpireBill',
			'uses' => 'AccountController@postExpireBill'
		));

		Route::post('account/billsTable/cancelBill', array(
			'as' => 'qiwiGate_postCancelBill',
			'uses' => 'AccountController@postCancelBill'
		));

		Route::get('authError', array(
			'as' => 'qiwiGate_gateAuthError',
			'uses' => 'AccountController@authError',
		));

	}

);

Route::get('qiwi/gate/about', array(
	'as'   => 'qiwiGate_about',
	'uses' => 'FintechFab\QiwiGate\Controllers\AccountController@about',
));


