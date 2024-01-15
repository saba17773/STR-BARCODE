<?php $this->layout("layouts/handheld", ["title" => "Movement Reverse"]); ?>

<style>
	.form-group {
		margin-bottom: 10px;
	}
</style>

<div class="head-space"></div>

<div class="panel panel-default hide" id="panel-ok" style="margin: auto; max-width: 500px;">
	<div class="panel-heading">Reverse</div>
	<div class="panel-body">
		<div class="form-group">
			<label for="barcodeForOK">Barcode</label>
			<input type="text" name="barcodeForOK" id="barcodeForOK" class="form-control inputs input-lg">
		</div>
	</div>
</div>

<div class="panel panel-default hide" id="panel-scrap" style="margin: auto; max-width: 500px;">
	<div class="panel-heading">Reverse</div>
	<div class="panel-body">

		<div class="form-group">
			<label for="defectFroScrap">Defect</label> <br>
			<select name="defectFroScrap" id="defectFroScrap" class="form-control input-lg" style="width: 150px;"></select>
		</div>

		<div class="form-group">
			<label for="barcodeForScrap">Barcode</label> <br>
			<input type="text" name="barcodeForScrap" id="barcodeForScrap" class="form-control inputs input-lg">
		</div>

	</div>
</div>

<div class="panel panel-default" id="panel-main" style="margin: auto; max-width: 500px;">
	<div class="panel-heading">Reverse</div>
	<div class="panel-body">
		<form id="formReverse">

			<div class="form-group">
				<label for="type">Type</label> <br>
				<select name="type" id="type" class="form-control input-lg">
					<option value="">= Select =</option>
					<option value="ok">OK</option>
					<option value="scrap">Hold</option>
				</select>
			</div>

			<div class="form-group">
				<label for="authorize">Authorize</label> <br>
				<input type="text" name="authorize" id="authorize" class="form-control input-lg inputs" required>
			</div>

			<div class="form-group">
				<label for="password">Password</label> <br>
				<input type="password" name="password" id="password" class="form-control input-lg inputs" required>
			</div>

		</form>
	</div>
</div>

<div id="result" style="text-align: center; padding: 10px;"></div>

<script>
	jQuery(document).ready(function($) {

		$('#type').on('change', function(event) {
			event.preventDefault();
			$('#authorize').val('').focus();
		});

		$('#defectFroScrap').on('change', function(event) {
			event.preventDefault();
			$('#barcodeForScrap').val('').focus();
		});

		$('#show-error').on('click', function() {

			$('#show-error').hide();

			if ($('#type').val() == 'ok') {

				$('input[name=barcodeForOK]').focus();
			} else if ($('#type').val() == 'scrap') {

				$('input[name=barcodeForScrap]').focus();
			} else {
				$('input[type=text]').focus();
			}

		});

		$('#barcodeForOK').keydown(function(event) {
			var barcode = $.trim($('#barcodeForOK').val());
			if (event.which === 13) {
				if (!!barcode) {
					gojax('post', base_url + '/api/movement/reverse/ok/save', {
							barcodeForOK: barcode,
							auth: $('#authorize').val()
						})
						.done(function(data) {
							// alert(data.message);
							if (data.status == 200) {
								$('#result').css('color', 'green').text(data.message);
							} else {
								$('#result').css('color', 'red').text(data.message);
								$('#show-error').show();
								$('#show-error-text').text(data.message);
							}

							$('#barcodeForOK').val('').focus();

						});
				} else {
					$('#result').css('color', 'red').text(data.message);
					$('#show-error').show();
					$('#show-error-text').text(data.message);
					$('#barcodeForOK').val('').focus();
				}
			}
		});

		$('#barcodeForScrap').on('keydown', function(event) {
			var barcode = $.trim($('#barcodeForScrap').val());
			if (event.which === 13) {
				if (!!barcode) {
					gojax('post', base_url + '/api/movement/reverse/scrap/save', {
							defect: $('#defectFroScrap').val(),
							barcode: $('#barcodeForScrap').val(),
							auth: $('#authorize').val()
						})
						.done(function(data) {
							if (data.status == 200) {
								$('#result').css('color', 'green').text(data.message);
							} else {
								$('#result').css('color', 'red').text(data.message);
								$('#show-error').show();
								$('#show-error-text').text(data.message);
							}

							$('#barcodeForScrap').val('').focus();
						});
				} else {
					$('#result').css('color', 'red').text(data.message);
					$('#show-error').show();
					$('#show-error-text').text(data.message);
					$('#barcodeForScrap').val('').focus();
				}
			}
		});

		$('#password').on('keydown', function(event) {
			var authorize = $.trim($('#authorize').val());
			var password = $.trim($('#password').val());
			var type = $.trim($('#type').val());
			if (event.which === 13) {
				if (!!authorize && !!password && !!type) {
					gojax('post', base_url + '/api/user/authorize', {
							code: authorize,
							password: password,
							type: 'MovementReverse'
						})
						.done(function(data) {
							if (data.status == 200) {
								if (type === 'ok') {
									$('#panel-ok').removeClass('hide');
									$('#panel-main').addClass('hide');
									$('#barcodeForOK').focus();
								} else if (type === 'scrap') {
									$('#panel-scrap').removeClass('hide');
									$('#panel-main').addClass('hide');
									$('#barcodeForScrap').focus();

									gojax('get', base_url + '/api/defect/reverse').done(function(data) {
										$('select[name=defectFroScrap]').html("<option value=''>= Select =</option>");
										$.each(data, function(index, val) {
											$('select[name=defectFroScrap]').append('<option value="' + val.ID + '">' + val.ID + ' - ' + val.Description + '</option>');
										});
									});
								}
								$('#result').text('');
							} else {
								$('#formReverse').trigger('reset');
								$('#result').css('color', 'red').text(data.message);
								$('#show-error').show();
								$('#show-error-text').text(data.message);
								$('#authorize').val('').focus();
							}
						});
				} else {
					$('#result').css('color', 'red').text('กรุณากรอกข้อมูล');
					$('#show-error').show();
					$('#show-error-text').text('กรุณากรอกข้อมูล');
					$('#barcodeForScrap').val('').focus();
				}
			}
		});

	});
</script>