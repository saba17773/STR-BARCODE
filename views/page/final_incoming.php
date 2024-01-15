<?php $this->layout("layouts/base", ["title" => "Final Incoming"]); ?>

<div class="head-space"></div>

<div class="panel panel-default form-center">
	<div class="panel-heading">Final Incoming</div>
	<div class="panel-body">
		<form id="formFinalIncoming">

			<!-- <div class="form-group">
				<label for="gate">Gate</label>
				<select name="gate" id="gate" class="form-control inputs input-lg" required></select>
			</div> -->

			<div class="form-group">
				<label for="barcode">Barcode</label>
				<input type="text" name="barcode" id="barcode" class="form-control inputs input-lg" placeholder="Barcode" required autocomplete="off">
			</div>

		</form>

	</div>
</div>

<div class="alert alert-success hide" role="alert" id="showItem" style="margin-top: 20px;">
	<h1 class="text-center" id="txtItemId" style="font-size: 5em; font-weight: bold;">-- Not found. --</h1>
	<h1 class="text-center" id="txtItemName" style="font-size: 5em; font-weight: bold;">-- Not found. --</h1>
</div>

<script>

	jQuery(document).ready(function($) {
		
		// getGate();

		// $('#gate').on('change', function(event) {
		// 	event.preventDefault();
		// 	$('#barcode').val('').focus();
		// });
		// 
		$('#barcode').val('').focus();

		$('#modal_alert').on('hidden.bs.modal', function() {
			$('#barcode').val('').focus();
		});

		$('form#formFinalIncoming').on('submit', function(event) {
			event.preventDefault();

			if (!!$('#barcode').val()) {
				$('#barcode').prop('readonly', true);
				gojax('post', base_url+'/api/final/save', {
					// gate: $('#gate').val(),
					barcode: $('#barcode').val() 
				})
				.done(function(data) {
					if (data.status == 200) {
						// window.location = '?success='+data.message;
						$('#top_alert').show();
            $('#top_alert_message').text(data.curecode + ', '+ data.batch +' => ' + $('#barcode').val());
            $('#modal_alert').modal('hide');

						gojax('get', base_url+'/api/barcode/'+ $('#barcode').val())
						.done(function(data) {
							$.each(data, function(index, val) {
								$('#showItem').removeClass('hide');
								$('#txtItemId').html(val.ItemID);
								$('#txtItemName').html(val.NameTH);
							});
						});

					} else {
						// window.location = '?error='+data.message+'&barcode='+$('#barcode').val();
						$('#top_alert').hide();
						$('#modal_alert').modal({backdrop: 'static'});
						$('#modal_alert_message').text(data.message);
						$('#barcode').val($('#barcode').val());

						$('.modal-content').css({
							'background': data.color
						});

						$('#modal_alert_message').css({
							'color': data.font_color
						});
					}
					$('#barcode').prop('readonly', false);
					$('#barcode').val('').focus();
				});
			}
		});

	});

	function getGate() {
		gojax('get', base_url+'/api/gate/all')
			.done(function(data) {
				$('#gate').html('<option value="">= กรุณาเลือก =</option>');
				$.each(data, function(index, val) {
					$('#gate').append('<option value="'+val.ID+'">'+val.Description+'</option>')
				});
			});
	}

</script>
