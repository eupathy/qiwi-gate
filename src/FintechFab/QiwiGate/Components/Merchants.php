<?php
namespace FintechFab\QiwiGate\Components;

use Config;
use FintechFab\QiwiGate\Models\Merchant;

class Merchants
{

	/**
	 * @param Merchant $merchant
	 * @param array    $data
	 *
	 * @return Merchant
	 */
	public static function NewMerchant(Merchant $merchant, $data)
	{

		$merchant->id = Config::get('ff-qiwi-gate::user_id');
		$merchant->username = $data['username'];
		$merchant->callback_url = $data['callback'];
		$merchant->password = $data['password'];
		$merchant->key = $data['key'] != null
			? $data['key']
			: md5(Config::get('ff-qiwi-gate::user_id') . $data['password'] . $data['username']);
		$merchant->save();

		return $merchant;

	}

	/**
	 * @param Merchant $merchant
	 * @param array    $data
	 *
	 * @return Merchant
	 */
	public static function ChangeMerchant(Merchant $merchant, $data)
	{
		$merchant->username = $data['username'];
		$merchant->callback_url = $data['callback'];
		$merchant->key = $data['key'];

		if (null != $data['password']) {
			$merchant->password = $data['password'];
		}
		$merchant->save();

		return $merchant;
	}

} 