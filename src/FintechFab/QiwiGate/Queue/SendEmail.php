<?php


namespace FintechFab\QiwiGate\Queue;


use FintechFab\QiwiGate\Models\Bill;
use Illuminate\Mail\Message;
use Illuminate\Queue\Jobs\Job;
use Log;
use Mail;
use Queue;

class SendEmail
{
	private $email;
	private $billId;
	private $textStatus;

	/**
	 * @param Job   $job
	 * @param array $data
	 */
	public function fire(Job $job, array $data)
	{

		if (empty($data['email'])) {
			$job->delete();
			Log::info('Не указан url для отправки callback.');

			return;
		}
		switch ($data['status']) {
			case Bill::C_STATUS_EXPIRED:
				$data['textStatus'] = ' просрочен.';
				break;
			case Bill::C_STATUS_PAID:
				$data['textStatus'] = ' оплачен.';
				break;
			case Bill::C_STATUS_REJECTED:
				$data['textStatus'] = ' отменён.';
				break;
			default:
				$data['textStatus'] = null;
		}
		$this->email = $data['email'];
		$this->billId = $data['bill_id'];
		$this->textStatus = $data['textStatus'];
		Mail::send('ff-qiwi-gate::emails.sendCallbackMail', $data, function (Message $message) {
			$message->to($this->email)->subject('Счёт №' . $this->billId . $this->textStatus);
		});

		$cntSendFails = count(Mail::failures());

		if ($cntSendFails == 0) {
			$job->delete();
			Log::info('Почта отправлена.', $data);

			return;
		}
		$cnt = $job->attempts();

		if ($cnt > 50) {
			$job->delete();

			return;
		}

		Log::info('Перевыставлена задача по отправке почты, попытка номер ' . $cnt, $data);
		$job->release(60 * $cnt);

	}

	/**
	 * Принимает id счёта и отправляет задачу в очередь
	 *
	 * @param integer $bill_id
	 */
	public static function emailToQueue($bill_id)
	{
		$bill = Bill::whereBillId($bill_id)->first();
		$merchant = $bill->merchant;

		Queue::connection('ff-qiwi-gate')->push('FintechFab\QiwiGate\Queue\SendEmail', array(
			'email'       => $merchant->email,
			'merchant_id' => $merchant->id,
			'bill_id'     => $bill->bill_id,
			'status'      => $bill->status,
			'amount'      => $bill->amount,
			'user'        => $bill->user,
			'prv_name'    => $bill->prv_name,
			'ccy'         => $bill->ccy,
			'comment'     => $bill->comment,
		));
	}

} 