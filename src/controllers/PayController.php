<?php

namespace FintechFab\QiwiGate\Controllers;

use FintechFab\QiwiGate\Models\Bill;
use FintechFab\QiwiGate\Models\Merchant;
use Input;
use Queue;
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

		$error = 'Счёт не может быть оплачен.';

		if (!$bill) {
			$error = 'Счет не найден.';
		}

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

			if (Bill::doPay($bill->bill_id)) {

				$merchant = Merchant::find($provider_id);
				Queue::connection('ff-qiwi-gate')->push('FintechFab\QiwiGate\Queue\SendCallback', array(
					'url'      => $merchant->callback_url,
					'bill_id'  => $bill_id,
					'status'   => Bill::C_STATUS_PAID,
					'error'    => 0,
					'amount'   => $bill->amount,
					'user'     => $bill->user,
					'prv_name' => $bill->prv_name,
					'ccy'      => $bill->ccy,
					'comment'  => $bill->comment,
					'command'  => 'bill',
				));

				return array(
					'message' => 'Счёт успешно оплачен.',
				);
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