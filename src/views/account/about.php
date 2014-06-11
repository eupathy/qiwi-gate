<?php

?>
<div class="row">
	<div class="col-md-5">
		<h2>О пакете QIWI gate</h2>

		<p>Это иммитация работы платёжного сервера QIWI, где есть основные методы работы со счетами:</p>

		<ul>
			<li>Выставление счёта по предоставленным данным</li>
			<li>Оплата счёта</li>
			<li>Отмена счёта</li>
			<li>Возвврат средств после оплаты</li>
		</ul>
	</div>

	<div class="col-md-5">

		<h2>Ссылки</h2>

		<p>Пакет на <a href="https://github.com/fintech-fab/qiwi-gate">GitHub</a></p>

		<p>Работает вместе с пакетами:</p>

		<ul>
			<li><a href="https://github.com/fintech-fab/qiwi-shop">Эмулятор интернет магазина</a></li>
			<li><a href="https://github.com/fintech-fab/qiwi-sdk">PHP SDK</a></li>
		</ul>
		<p>
			Для установки пакета необходим фреймоврк <a href="http://laravel.com">Laravel</a>. </p>
	</div>

</div>
<div class="row">
	<div class="col-md-8">
		<h2>Описание работы</h2>

		<p>
			Для работы с гейтом требуется авторизированный пользователь в сессии. Подробнее от этом
			<a href="https://github.com/fintech-fab/qiwi-gate#%D0%9F%D0%BE%D0%BB%D1%83%D1%87%D0%B5%D0%BD%D0%B8%D0%B5-id-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F-%D0%B4%D0%BB%D1%8F-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8">
				здесь. </a>
		</p>

		<p>
			Этот пакет обрабатывает запросы так же как и реальная система QIWI. Используйте его чтобы протестировать
			работу Вашего приложения с платежами. </p>

		<p>Callback отправляется на указанный <a href="http://fintech-fab.dev:8080/qiwi/gate/account">здесь</a> адрес
		</p>
	</div>
</div>