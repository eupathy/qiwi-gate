$(document).ready(function () {

	$('.refund-button').click(function () {
		var $btn = $(this);
		var id = $btn.data('id');
		var tableRefund = $('#table-refund-' + id + ' > .container');

		$.get('billsTable/getRefund/' + id,
			function (data) {
				tableRefund.empty().append(data);
			}
		);

		$('#table-refund-' + id).toggle();
	});
	$('.actionBtn').click(function () {
		var $btn = $(this);
		var url = $btn.data('url');
		var id = $btn.data('id');
		var shop = $btn.data('shop');
		$.post(url, {shop: shop, transaction: id},
			function (data) {
				var title = (true == data['error']) ? 'Ошибка' : 'Сообщение';
				$('#message').dialog({
					title: title, show: 'fade', hide: 'fade', modal: true, close: function () {
						location.reload();
					}
				}).html(data['message']);
			}
		);
	});
});
