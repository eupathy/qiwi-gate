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
	<div class="col-md-5">
		<h2>Описание работы</h2>

		<p>
			Для работы с гейтом требуется авторизированный пользователь в сессии. <br>Подробнее от этом
			<a href="https://github.com/fintech-fab/qiwi-gate#%D0%9F%D0%BE%D0%BB%D1%83%D1%87%D0%B5%D0%BD%D0%B8%D0%B5-id-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F-%D0%B4%D0%BB%D1%8F-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8">
				здесь. </a>
		</p>

		<p>
			Этот пакет обрабатывает запросы так же как и реальная система QIWI. Используйте его чтобы протестировать
			работу Вашего приложения с платежами. </p>

		<p>
			Callback отправляется на указанный <a href="<?= URL::route('qiwiGate_account') ?>">в личном кабинете</a>
			адрес. </p>

		<h4>Оплата счёта:</h4>

		<p>
			Оплатить счёт можно по ссылке:<br>
			<?= URL::route('qiwi_gate_payIndex') ?>
		</p>

		<p>
			Не забудьте в запросе указать GET параметры - <b>?shop=1&transaction=1</b> <br> где <b>shop</b> - это id
			провайдера и <b>transaction</b> - это номер счёта </p>

		<p>
			Личный кабинет провайдера:<br>
			<a href="<?= URL::route('qiwiGate_account') ?>"><?= URL::route('qiwiGate_account') ?></a>
		</p>

		<p>
			Таблица счетов:<br>
			<a href="<?= URL::route('qiwiGate_billsTable') ?>"><?= URL::route('qiwiGate_billsTable') ?></a>
		</p>
	</div>
	<div class="col-md-5">
		<h2>Взаимодействие с гейтом:</h2>

		<p>
			Сервер принимает REST запросы так же, как и обычный сервис QIWI. </p>

		<p><i><b>Подробнее о параметрах запросов смотрите в документации к QIWI.</b></i></p>

		<p>
			Для выставления счета необходимо послать PUT-запрос по адресу:<br>
			<?= URL::to('qiwi/gate/api/v2/prv/{provider_id}/bills/{bill_id}') ?><br> где <b>provider_id</b> - числовой
			идентификатор провайдера выставляющего счёт,<br> <b>bill_id</b> - уникальный идентификатор счета на стороне
			провайдера. </p>

		<p>
			Для запроса текущего статуса счета необходимо послать GET-запрос по адресу:
			<?= URL::to('qiwi/gate/api/v2/prv/{provider_id}/bills/{bill_id}') ?><br>
		</p>

		<p>
			Для отмены выставленноо счёта необходимо послать PATCH-запрос по адресу:
			<?= URL::to('qiwi/gate/api/v2/prv/{provider_id}/bills/{bill_id}') ?><br>
		</p>

		<p>
			Для осуществления возврата необходимо послать PUT-запрос по адресу:
			<?= URL::to('qiwi/gate/api/v2/prv/{provider_id}/bills/{bill_id}/refund/{refund_id}') ?><br> где
			<b>refund_id</b> - идентификатор отмены, уникальный в рамках отмен одного счета. </p>

		<p>
			Для проверки статуса платежа возврата по счёту необходимо послать GET-запрос по адресу:
			<?= URL::to('qiwi/gate/api/v2/prv/{provider_id}/bills/{bill_id}/refund/{refund_id}') ?><br>
		</p>
	</div>
</div>