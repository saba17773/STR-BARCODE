<?php $this->layout('layouts/handheldmobile', ['title' => 'Return']); ?>

<div class="head-space"></div>
<div class="panel panel-default form-center">
	<div class="panel-heading"><h1>Return</h1></div>
	<div class="panel-body">
		<form id="formFinalReturn">
			<div class="form-group">
				<label for="barcode">Barcode</label><br>
				<input type="text" name="barcode" id="barcode" class="form-control input-lg" autocomplete="off">
			</div>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		
		$('#barcode').val('').focus();

		$('#modal_alert').on('click', function() {
			$('#barcode').val('').focus();			
		});

		$('form#formFinalReturn').on('submit', function(event) {
			event.preventDefault();
			gojax('post', base_url+'/api/final/return/save', {
				barcode: $('#barcode').val()
			})
			.done(function(data) {
				if (data.status === 200) {
					alert("Barcode ล่าสุด"  + $('#barcode').val());
					// $('#top_alert').show();
					// $('#top_alert_message').text('Barcode ล่าสุด '+ $('#barcode').val());
					// $('#modal_alert').modal('hide');
					$('#barcode').val('').focus();
				} else {
					alert(data.message);
					// $('#top_alert').hide();
					// $('#modal_alert').modal({backdrop: 'static'});
					// $('#modal_alert_message').text(data.message);
					$('#barcode').val('').focus();
					$('#barcode').val();
				}
			});
		});

	});
</script>