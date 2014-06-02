<?php


namespace FintechFab\QiwiGate\Queue;


use Illuminate\Queue\Jobs\Job;
use Log;

class SendCallback
{


	public function fire(Job $job, array $data)
	{

		if (empty($data['url'])) {
			$job->delete();

			return;
		}


		$ch = curl_init($data['url']);
		unset($data['url']);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		$result = curl_exec($ch);

		if (!empty($result) && strpos($result, '<result_code>0</result_code>') !== false) {
			Log::info('Успешно вызван callback с ответом 0', $data);
			$job->delete();
		}

		$cnt = $job->attempts();

		if ($cnt > 50) {
			$job->delete();
		}

		Log::info('Перевыставлена задача, попытка номер ' . $cnt, $data);
		$job->release(60 * $cnt);

	}

} 