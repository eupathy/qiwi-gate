<?php


namespace FintechFab\QiwiGate\Queue;


use FintechFab\QiwiGate\Models\Bill;
use Illuminate\Queue\Jobs\Job;
use Log;
use Queue;

class SendCallback
{

	/**
	 * @param Job   $job
	 * @param array $data
	 */
	public function fire(Job $job, array $data)
	{

		if (empty($data['url'])) {
			$job->delete();

			return;
		}

		$ch = curl_init($data['url']);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $data['merchant_id'] . ':' . $data['merchant_pass']);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Api-Signature:' . $data['sign']));
		unset($data['url'], $data['merchant_id'], $data['merchant_pass'], $data['sign']);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		$result = curl_exec($ch);
		$httpError = curl_error($ch);
		Log::info('Ошибки curl:', array('httpError' => $httpError));

		if (!empty($result) && strpos($result, '<result_code>0</result_code>') !== false) {
			Log::info('Успешно вызван callback с ответом 0', array('result' => $result));
			$job->delete();
			die();
		}

		Log::info('Результат curl:', array('result' => $result));
		$cnt = $job->attempts();

		if ($cnt > 50) {
			$job->delete();
			die();
		}

		Log::info('Перевыставлена задача, попытка номер ' . $cnt, $data);
		$job->release(60 * $cnt);

	}

	/**
	 * Принимает id счёта и отправляет задачу в очередь
	 *
	 * @param integer $bill_id
	 */
	public static function jobBillToQueue($bill_id)
	{
		$bill = Bill::whereBillId($bill_id)->first();
		$command = 'bill';
		$error = 0;
		$key = $bill->merchant->key;
		$signData = $bill->amount . '|' . $bill->bill_id . '|' . $bill->ccy . '|' . $command . '|' .
			$bill->comment . '|' . $error . '|' . $bill->prv_name . '|' . $bill->status . '|' . $bill->user;
		Queue::connection('ff-qiwi-gate')->push('FintechFab\QiwiGate\Queue\SendCallback', array(
			'url'           => $bill->merchant->callback_url,
			'merchant_id'   => $bill->merchant->id,
			'merchant_pass' => $bill->merchant->password,
			'bill_id'       => $bill->bill_id,
			'status'        => $bill->status,
			'error'   => $error,
			'amount'        => $bill->amount,
			'user'          => $bill->user,
			'prv_name'      => $bill->prv_name,
			'ccy'           => $bill->ccy,
			'comment'       => $bill->comment,
			'command' => $command,
			'sign'    => base64_encode(hash_hmac('sha1', $signData, $key)),
		));
	}

} 