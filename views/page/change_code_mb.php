<?php $this->layout('layouts/handheldmobile', ['title' => 'Change Code']) ?>

<div class="head-space"></div>

<div class="panel panel-default">
	<div class="panel-heading"><h1>Change Code</h1></div>
	<div class="panel-body">
		<form id="form_change_code">
			<div class="form-group">
				<label for="copy_barcode">Copy barcode</label><br>
				<input type="text" class="form-control inputs" name="copy_barcode" id="copy_barcode" autofocus autocomplete="off" >
			</div>
			<br>
			<div class="form-group">
				<label for="barcode">Barcode</label><br>
				<input type="text" class="form-control inputs" name="barcode" id="barcode" autocomplete="off" >
			</div>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {

		$('input[name=copy_barcode]').val('').focus();

		$("#barcode").keydown(function(event) {
			if (event.which === 13) {
				gojax('post', '/api/v1/building/change_code', {
					copy_barcode: $.trim($('#copy_barcode').val()),
					barcode: $.trim($('#barcode').val())
				}).done(function(data) {
					if (data.result === false) {
						 alert(data.message);
						// $('#top_alert').hide();
						// $('#modal_alert').modal({backdrop: 'static'});
						// $('#modal_alert_message').text(data.message);
					} else {
						alert("Barcode ใหม่ : " + $('#barcode').val());

						// $('#top_alert').show();
						// $('#modal_alert').modal('hide');
						// $('#top_alert_message').text('Barcode ใหม่ : ' + $('#barcode').val());
						// setTimeout(function(){
						// 	$('#top_alert').hide();
						//}, 2000);
					}
					$('#form_change_code').trigger('reset');
					$('#copy_barcode').focus();
				});
			}
		});
	});
</script>
