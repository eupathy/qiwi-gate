$(document).ready(function () {
	$('button#payBill').click(function () {
		$('button').attr('disabled', true);
		$.post('', function (data) {
				$('button').attr('disabled', false);
				if (data['error']) {
					$('#error').html(data['message']);
					return;
				} else {
					$('#error').html('');
				}
				$('#message').dialog({
					title: 'Сообщение', show: 'fade', hide: 'fade', modal: true, close: function () {
						window.close();
					}
				}).html(data['message']);

			}
		);
	});
});
