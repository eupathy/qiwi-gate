<?php
namespace FintechFab\QiwiGate\Controllers;


use Config;
use FintechFab\QiwiGate\Components\Merchants;
use FintechFab\QiwiGate\Components\Validators;
use FintechFab\QiwiGate\Models\Bill;
use FintechFab\QiwiGate\Models\Merchant;
use FintechFab\QiwiGate\Models\Refund;
use FintechFab\QiwiGate\Queue\SendCallback;
use Input;
use Validator;

class AccountController extends BaseController
{

	public $layout = 'account';

	/**
	 * Главная страница аккаунта QIWI
	 *
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

	public function about()
	{
		$this->make('about');
	}

	/**
	 * Страница ошибки авторизации
	 */
	public function authError()
	{
		$this->make('authError');
	}

	/**
	 * Регистрация в QIWI
	 *
	 * @return array
	 */
	public function postRegistration()
	{
		$data = Input::all();
		$errors = $this->getErrorFromRegData($data);

		if ($errors) {
			return $errors;
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
	 * Изменить данные в аккаунте
	 *
	 * @return array
	 */
	public function postChangeData()
	{
		$data = Input::all();

		//проверяем данные
		$errors = $this->getErrorFromChangeData($data);
		if ($errors) {
			return $errors;
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
		$merchant = Merchants::ChangeMerchant($currentMerchant, $data);
		if ($merchant) {
			$message = 'Данные изменены';

			return array('message' => $message);
		}
		$result['errors'] = array(
			'oldPassword' => 'Ошибка, попробуйте ещё раз.',
		);

		return $result;

	}

	/**
	 * Страница с таблицей счетов
	 *
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

	/**
	 * Если есть, отдаёт таблицу с возвратами
	 *
	 * @param $bill_id
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function GetRefundTable($bill_id)
	{
		$provider_id = Config::get('ff-qiwi-gate::user_id');
		$bill = Bill::getBill($bill_id, $provider_id);
		if (!$bill) {
			return 'Нет такого счёта';
		}
		$refunds = Refund::whereBillId($bill_id)->get();
		if (count($refunds) == 0) {
			return 'Возвратов нет';
		}

		return $this->make('refundsTable', array('refunds' => $refunds));
	}

	/**
	 * Отмена счёта
	 *
	 * @return array
	 */
	public function postCancelBill()
	{
		$bill_id = Input::get('transaction');
		$provider_id = Input::get('shop');
		$bill = Bill::getBill($bill_id, $provider_id);

		//предполагаем ошибку
		$error = 'Счёт не найден';

		if ($bill) {
			//Если счёт отмеён, то кидаем callback и отдаём успех
			if ($bill->doCancel($bill_id)) {
				$this->sendCallback($bill_id);

				return array(
					'message' => 'Счёт отменён',
				);
			}
			$error = 'Счёт не отменён. Проверьте статус.';
		}

		//Отдаём ошибку
		return array(
			'error'   => true,
			'message' => $error,
		);
	}

	public function postExpireBill()
	{
		$bill_id = Input::get('transaction');
		$provider_id = Input::get('shop');
		$bill = Bill::getBill($bill_id, $provider_id);

		//предполагаем ошибку
		$error = 'Счёт не найден';

		if ($bill) {
			//Если счёт просрочен, то кидаем callback и отдаём успех
			if ($bill->doExpire($bill_id)) {
				$this->sendCallback($bill_id);

				return array(
					'message' => 'Счёт просрочен',
				);
			}
			$error = 'Счёт не просрочен. Проверьте статус.';
		}

		//Отдаём ошибку
		return array(
			'error'   => true,
			'message' => $error,
		);
	}

	/**
	 * Проверка ввода при регистрации
	 *
	 * @param $data
	 *
	 * @return array
	 */
	private function getErrorFromRegData($data)
	{
		$data['username'] = e($data['username']);
		$validator = Validator::make($data, Validators::rulesForRegisterAcc(), Validators::messagesForErrors());
		$userMessages = $validator->messages();

		if ($validator->fails()) {
			$result['errors'] = array(
				'username'        => $userMessages->first('username'),
				'callback'        => $userMessages->first('callback'),
				'password'        => $userMessages->first('password'),
				'confirmPassword' => $userMessages->first('confirmPassword'),
			);

			return $result;
		}

		return null;
	}

	/**
	 * Проверка ввода при смене данных
	 *
	 * @param $data
	 *
	 * @return null
	 */
	private function getErrorFromChangeData($data)
	{
		$data['username'] = e($data['username']);
		$validator = Validator::make($data, Validators::rulesForChangeAccData(), Validators::messagesForErrors());
		$userMessages = $validator->messages();

		if ($validator->fails()) {
			$result['errors'] = array(
				'username'        => $userMessages->first('username'),
				'callback'        => $userMessages->first('callback'),
				'key' => $userMessages->first('key'),
				'password'        => $userMessages->first('password'),
				'confirmPassword' => $userMessages->first('confirmPassword'),
			);

			return $result;
		}

		return null;
	}

	/**
	 * Отправить callback
	 *
	 * @param $bill_id
	 */
	public function sendCallback($bill_id)
	{
		SendCallback::jobBillToQueue($bill_id);
	}

} 