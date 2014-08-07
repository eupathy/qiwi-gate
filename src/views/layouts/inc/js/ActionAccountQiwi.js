$(document).ready(function () {

	$('button#changeAccountData').click(function () {
		var username = $('#inputUsername').val();
		var callback = $('#inputCallback').val();
		var email = $('#inputEmail').val();
		var key = $('#inputKey').val();
		var password = $('#inputPassword').val();
		var confirmPassword = $('#inputConfirmPassword').val();
		var oldPassword = $('#inputOldPassword').val();
		$('button').attr('disabled', true);
		$.ajax({
			type: "POST",
			url: 'account/changeData',
			data: {username: username,
				callback: callback,
				email: email,
				key: key,
				password: password,
				confirmPassword: confirmPassword,
				oldPassword: oldPassword
			},
			success: function (data) {
				$('button').attr('disabled', false);
				if (data['errors']) {
					$('#errorUsername').html(data['errors']['username']);
					$('#errorCallback').html(data['errors']['callback']);
					$('#errorEmail').html(data['errors']['email']);
					$('#errorKey').html(data['errors']['key']);
					$('#errorPassword').html(data['errors']['password']);
					$('#errorConfirmPassword').html(data['errors']['confirmPassword']);
					$('#errorOldPassword').html(data['errors']['oldPassword']);
					return;
				}
				location.reload();
			}
		});
	});
});
