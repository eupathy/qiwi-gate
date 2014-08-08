<?php
/**
 * @var Bill $bills
 */
use FintechFab\QiwiGate\Models\Bill;

?>

<script type="application/javascript">
	<?php require(__DIR__ . '/../layouts/inc/js/ActionAccountBillsTable.js') ?>
</script>

<table class="table table-striped table-hover" id="billsTable">
	<tr>
		<td><b>ID</b></td>
		<td><b>Пользователь</b></td>
		<td><b>Сумма</b></td>
		<td><b>Валюта</b></td>
		<td><b>Комментарий</b></td>
		<td><b>Срок действия</b></td>
		<td><b>Провайдер</b></td>
		<td><b>Статус</b></td>
		<td><b>Действия</b></td>
	</tr>
	<?php foreach ($bills as $bill): ?>
		<tr>
			<td><?= $bill->id ?></td>
			<td><?= $bill->user ?></td>
			<td><?= $bill->amount ?></td>
			<td><?= $bill->ccy ?></td>
			<td><?= $bill->comment ?></td>
			<td><?= $bill->lifetime ?></td>
			<td><?= $bill->prv_name ?></td>
			<td><?= $bill->status ?></td>
			<td>
				<?php if ($bill->status == 'paid') {
					echo Form::button('Возврат', array(
						'type'    => 'button',
						'class'   => 'btn btn-primary btn-sm refund-button',
						'data-id' => $bill->bill_id,
					));
				} elseif ($bill->status == 'waiting') {
					echo Form::button('Оплатить', array(
						'type'      => 'button',
						'class'     => 'btn btn-success btn-sm pay-button actionBtn',
						'data-url'  => URL::route('qiwiGate_postPay'),
						'data-shop' => $bill->merchant_id,
						'data-id'   => $bill->bill_id,
					));
					echo Form::button('Отменить', array(
						'type'      => 'button',
						'class'     => 'btn btn-danger btn-sm cancel-button actionBtn',
						'data-url'  => URL::route('qiwiGate_postCancelBill'),
						'data-shop' => $bill->merchant_id,
						'data-id'   => $bill->bill_id,
					));
					echo Form::button('Посрочить', array(
						'type'      => 'button',
						'class'     => 'btn btn-warning btn-sm expire-button actionBtn',
						'data-url'  => URL::route('qiwiGate_postExpireBill'),
						'data-shop' => $bill->merchant_id,
						'data-id'   => $bill->bill_id,
					));
				}
				?>
			</td>
		</tr>
		<tr id="table-refund-<?= $bill->bill_id ?>" style="display: none;">
			<td colspan="9" class="container text-right"></td>
		</tr>

	<?php endforeach ?>
</table>
<?= $bills->links() ?>
<div id="message" class="text-center"></div>

