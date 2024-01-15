<?php

$this->layout("layouts/handheld", ['title' => 'Handheld Login']);

if (isset($_SESSION["user_login"])) {
	header("Location: " . APP_ROOT . "/curing");
}

?>

<style>
	body {
		font-size: 14px;
	}
</style>

<form id="form_hh_auth" style="padding: 10px; display: block; text-align: center;" onsubmit="return form_hh_submit()">

	<hr>
	<div style="text-align: center; font-weight: bold;">
		<label>
			<input type="radio" name="type_curing" value="1" checked> Curing TBR
		</label>
		<label>
			<input type="radio" name="type_curing" value="3"> Curing PCR
		</label>
		<label>
			<input type="radio" name="type_curing" value="2"> Reverse
		</label>
	</div>
	<hr>

	<label for="hh_username" style="font-weight: bold;">Username</label> <br />
	<input type="text" name="hh_username" id="hh_username" class="inputs" style="width:150px;margin-bottom: 5px;" autofocus>
	<br>
	<label for="hh_password" style="font-weight: bold;">Password</label> <br />
	<input type="password" name="hh_password" id="hh_password" class="inputs" style="width:150px; margin-bottom: 5px;">

</form>

<div id="result" style="text-align: center; margin: 0 auto; width: 200px; padding: 10px; display: none; color: red;"></div>

<script>
	jQuery(document).ready(function($) {

		$('input[type=radio]').on('click', function(event) {
			$('#hh_username').val('').focus();
		});

		$('#hh_password').keydown(function(event) {
			if (event.which === 13) {

				var type = $('input[name=type_curing]:checked').val();
				var username = $('input[name=hh_username]').val();
				var password = $('input[name=hh_password]').val();

				if (typeof type !== 'undefined' && !!username && !!password) {
					gojax_f('post', base_url + '/api/user/handheld/auth', '#form_hh_auth')
						.done(function(data) {
							if (data.status == 200) {
								if (data.location !== 3) {
									$('#result').text('You\'re don\'t have permission to access this section.').show();
									$('#form_hh_auth').trigger('reset');
									$('#hh_username').focus();
								} else {
									window.location = base_url + '/curing?type=' + $('input[name=type_curing]:checked').val();
								}
							} else {
								$('#result').text(data.message).show();
								$('#form_hh_auth').trigger('reset');
								$('#hh_username').focus();
							}
						});
				} else {
					$('#result').text('Please fill require data.').show();
				}

			}
		});
	});
</script>