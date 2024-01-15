<?php $this->layout("layouts/base", ['title' => 'Unhold']); ?>

<div class="head-space"></div>

<div class="panel panel-default form-center hide" id="panel_barcode">
	<div class="panel-heading">Barcode</div>
	<div class="panel-body">
		<form id="formBarcode" onsubmit="return false;">
			<div id="barcode_result"></div>
			<div class="form-group">
				<label for="barcode">Barcode</label>
				<input type="text" id="barcode" name="barcode" class="form-control input-lg inputs" autocomplete="off">
			</div>
		</form>
	</div>
</div>


<div class="panel panel-default form-center" id="panel_auth">
	<div class="panel-heading">Unhold / Unrepair</div>
	<div class="panel-body">
		<form id="formAuthen">
			<div class="form-group">
				<label for="authen_code">Authorize Code</label>
				<input type="text" id="authen_code" name="authen_code" class="form-control input-lg inputs" autocomplete="off">
			</div>
			<div class="form-group">
				<label for="authen_pass">Password</label>
				<input type="password" id="authen_pass" name="authen_pass" class="form-control input-lg inputs" autocomplete="off">
			</div>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {

		$('#modal_alert').on('hidden.bs.modal', function() {
			$('#barcode').focus();
			$('#authen_code').focus();
			// if(qs('auth') === '1') {
			// 	$('#barcode').focus();
			// } else {
			// 	$('#authen_code').focus();
			// }
		});

		$('#authen_code').focus();

		$('#authen_pass').keydown(function(event) {
			/* Act on the event */
			if (event.which === 13) {
				var authen_code = $('#authen_code');
				var authen_pass = $('#authen_pass');
				if (!!authen_code.val() && !!authen_pass.val()) {
					$('#formAuthen').submit();
				} else {
					// alert('กรุณากรอกข้อมูลให้ครบถ้วน');
					// window.location = '?error=กรุณากรอกข้อมูลให้ครบถ้วน';
					$('#modal_alert').modal({
						backdrop: 'static'
					});
					$('#modal_alert_message').text('กรุณากรอกข้อมูลให้ครบถ้วน');
					$('#top_alert').hide();
					$('#authen_code').focus();
				}

			}
		});

		$('#formAuthen').on('submit', function(event) {

			event.preventDefault();

			gojax('post', base_url + '/api/authorize/type', {
					type: 'unhold_unrepair'
				})
				.done(function(data) {
					gojax('post', base_url + '/api/user/authorize', {
							code: $('#authen_code').val(),
							password: $('#authen_pass').val(),
							type: data.type
						})
						.done(function(data) {
							if (data.status == 200) {
								// $('#modal_authen').modal('hide');
								// $('#modal_barcode').modal({backdrop: 'static'});
								$('#panel_barcode').removeClass('hide');
								$('#panel_auth').addClass('hide');
								$('#barcode').val('').focus();
								// window.location = '?auth=1';

							} else {
								$('#formAuthen').trigger('reset');
								// alert(data.message);
								// window.location = '?error='+data.message;
								$('#modal_alert').modal({
									backdrop: 'static'
								});
								$('#modal_alert_message').text(data.message);
								$('#top_alert').hide();
								$('#authen_code').focus();
							}
						});
				});

		});

		$('#barcode').keydown(function(event) {
			if (event.which === 13) {
				// $('form#formBarcode').submit();
				gojax('post', base_url + '/api/unhold', {
						barcode: $('#barcode').val(),
						auth: $('#authen_code').val()
					})
					.done(function(data) {
						if (data.status == 200) {
							// window.location = '?auth=1&success='+data.message;
							$('#modal_alert').modal('hide');
							$('#top_alert').show();
							$('#top_alert_message').text('Barcode ล่าสุด ' + $('#barcode').val());
						} else {
							// window.location = '?auth=1&error='+data.message;
							$('#modal_alert').modal({
								backdrop: 'static'
							});
							$('#modal_alert_message').text(data.message);
							$('#top_alert').hide();

						}
						$('#barcode').val('').focus();
					});
			}
		});
	});
</script>