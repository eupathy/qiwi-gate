<?php

namespace FintechFab\QiwiGate\Components;

use URL;

class Helper
{

	/**Возвращает ссылку для логина
	 *
	 * @return string
	 */
	public static function url2Sign()
	{
		$url = URL::route('registration') . '?' . http_build_query(array(
				'back' => urlencode(URL::current()),
			));

		return $url;
	}

} 