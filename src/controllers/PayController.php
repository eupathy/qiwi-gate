<?php

namespace FintechFab\QiwiGate\Controllers;

use DB;
use Exception;
use FintechFab\QiwiGate\Models\Bill;
use FintechFab\QiwiGate\Queue\SendCallback;
use Input;
use View;

class PayController extends BaseController
{
	public $layout = 'payment';

	/**
	 * @return View
	 */
	public function index()
	{

		$bill_id = Input::get('transaction');
		$provider_id = Input::get('shop');
		$bill = Bill::getBill($bill_id, $provider_id);

		if ($bill && $bill->isWaiting()) {
			return $this->make('index', array('bill' => $bill));
		}

		if (!$bill) {
			$error = 'Счет не найден.';

			return $this->make('error', array('message' => $error));
		}

		$error = 'Счёт не может быть оплачен.';

		if ($bill->isExpired()) {
			$error = 'Счёт просрочен.';
		}

		return $this->make('error', array('message' => $error));

	}

	public function postPay()
	{

		$bill_id = Input::get('transaction');
		$provider_id = Input::get('shop');
		$bill = Bill::getBill($bill_id, $provider_id);

		//Предполагаем ошибку статуса
		$error = 'Ошибка оплаты, проверьте статус.';

		if ($bill) {

			try{

				$result = DB::connection('ff-qiwi-gate')->transaction(function() use ($bill_id){
					if (Bill::doPay($bill_id)) {
						SendCallback::jobBillToQueue($bill_id);
						return array(
							'message' => 'Счёт успешно оплачен.',
						);
					}
					return null;
				});

				if($result){
					return $result;
				}

			}catch(Exception $e){

			}

		} else {
			//Меняем ошибку на Счёт не найден
			$error = 'Счет не найден.';
		}

		return array(
			'error'   => true,
			'message' => $error,
		);

	}

}