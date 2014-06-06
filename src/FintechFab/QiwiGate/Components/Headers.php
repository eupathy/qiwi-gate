<?php
namespace FintechFab\QiwiGate\Components;


use FintechFab\QiwiGate\Models\Merchant;
use Request;

class Headers
{

	public static function GetMerchant()
	{
		$authBasicHeader = trim(Request::header('Authorization'));
		if ($authBasicHeader) {

			preg_match('/^Basic (.+)$/', $authBasicHeader, $matches);
			@list($login, $password) = explode(':', base64_decode($matches[1]));
			$merchantData = array(
				'login'    => $login,
				'password' => $password,
			);

			return $merchantData;
		}

		return false;
	}

	public static function CheckAuth()
	{
		$isSuccess = false;

		$merchantData = self::GetMerchant();

		if ($merchantData) {

			$merchant = Merchant::find($merchantData['login']);
			if ($merchant && $merchant->password == $merchantData['password']) {
				$isSuccess = true;
			}
		}

		return $isSuccess;
	}

} 