<?php
namespace FintechFab\QiwiGate\Controllers;


use Config;
use FintechFab\QiwiGate\Components\Merchants;
use FintechFab\QiwiGate\Components\Validators;
use FintechFab\QiwiGate\Models\Bill;
use FintechFab\QiwiGate\Models\Merchant;
use Input;
use Validator;

class AccountController extends BaseController
{

	public $layout = 'account';

	/**
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		$user_id = Config::get('ff-qiwi-gate::user_id');
		$merchant = Merchant::find($user_id);
		if (!$merchant) {
			return $this->make('newMerchant', array('user_id' => $user_id));
		}

		return $this->make('index', array('merchant' => $merchant));
	}

	/**
	 * Страница ошибки авторизации
	 */
	public function authError()
	{
		$this->make('authError');
	}

	/**
	 * @return array
	 */
	public function postRegistration()
	{
		$data = Input::all();
		$validator = $this->validateData($data);

		if ($validator) {
			return $validator;
		}
		$newMerchant = new Merchant;
		$merchant = Merchants::NewMerchant($newMerchant, $data);

		if ($merchant) {
			$message = 'Вы успешно зарегистрировались.';

			return array('message' => $message);
		}
		$message = 'Ошибка, попробуйте ещё раз.';

		return array('message' => $message);

	}

	/**
	 * @return array
	 */
	public function postChangeData()
	{
		$data = Input::all();

		//проверяем данные
		$validator = $this->validateData($data);
		if ($validator) {
			return $validator;
		}

		//Проверяем текущий пароль
		$user_id = Config::get('ff-qiwi-gate::user_id');
		$currentMerchant = Merchant::find($user_id);
		if ($data['oldPassword'] != $currentMerchant->password) {
			$result['errors'] = array(
				'oldPassword' => 'Неверный пароль',
			);

			return $result;
		}

		//Изменяем данные
		$merchant = Merchants::NewMerchant($currentMerchant, $data);
		if ($merchant) {
			$message = 'Вы успешно зарегистрировались.';

			return array('message' => $message);
		}
		$result['errors'] = array(
			'oldPassword' => 'Ошибка, попробуйте ещё раз.',
		);

		return $result;

	}

	/**
	 * @return \Illuminate\View\View
	 */
	public function billsTable()
	{
		$user_id = Config::get('ff-qiwi-gate::user_id');
		$bills = Bill::whereMerchantId($user_id)
			->orderBy('id', 'desc')
			->paginate(10);
		if (!$bills) {
			return $this->make('newMerchant', array('user_id' => $user_id));
		}

		return $this->make('billsTable', array('bills' => $bills));
	}

	public function GetRefundTable($bill_id)
	{
		$user_id = Config::get('ff-qiwi-gate::user_id');
		$bill = Bill::whereMerchantId($user_id)->find($bill_id);
		if (!$bill) {
			return 'Нет такого счёта';
		}
		$refunds = $bill->refunds()->get();
		if (count($refunds) == 0) {
			return 'Возвратов нет';
		}

		return $this->make('refundsTable', array('refunds' => $refunds));
	}

	/**
	 * @param $data
	 *
	 * @return array|bool
	 */
	private function validateData($data)
	{
		$validator = Validator::make($data, Validators::rulesForAccount(), Validators::messagesForErrors());
		$userMessages = $validator->messages();

		if ($validator->fails()) {
			$result['errors'] = array(
				'id'              => '',
				'username'        => $userMessages->first('username'),
				'callback'        => $userMessages->first('callback'),
				'password'        => $userMessages->first('password'),
				'confirmPassword' => $userMessages->first('confirmPassword'),
			);

			return $result;
		}

		return false;
	}

} 