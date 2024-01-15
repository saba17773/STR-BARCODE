<?php $this->layout("layouts/handheld", ["title" => "Final Incoming"]); ?>

<div class="head-space"></div>


<div class="panel panel-default form-center">
	<div class="panel-body">
		
		<form id="formFinalIncoming" style="padding: 10px;">

			<div style="padding: 10px; text-align: center;"> 
				Final Incoming | <a href="/user/logout">Logout</a>
			</div>
			
			<div id="top" style="padding: 10px;"></div>

			<div class="form-group" style="text-align: center;">
				<div style="margin-bottom: 10px; font-weight: bold;">Barcode</div>
				<input type="text" name="barcode" id="barcode" 
				class="form-control inputs input-lg" 
				placeholder="" required autocomplete="off">
			</div>
	
		</form>
		<div id="_success" style="color: green; font-weight: bold; display: none;">Successful</div>
	</div>
</div>

<div class="alert alert-success hide" role="alert" id="showItem" style="margin-top: 20px;">
	<h1 class="text-center" id="txtItemId" style="font-size: 5em; font-weight: bold;">-- Not found. --</h1>
	<h1 class="text-center" id="txtItemName" style="font-size: 5em; font-weight: bold;">-- Not found. --</h1>
</div>

<div id="show_result"></div>

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
			/* Act on the event */
		});

		$('#show-error').click(function(event) {
			$('#show-error').hide();
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
					// alert(data.message);
					if (data.status == 200) {
						// window.location = '?success='+data.message;
						// $('#top_alert').show();
						$('#top').text('Barcode ล่าสุด '+ $('#barcode').val());
						// $('#modal_alert').modal('hide');
						$('#barcode').val('').focus();
						$('#_success').show();

						gojax('get', base_url+'/api/barcode/'+ $('#barcode').val())
						.done(function(data) {
							$.each(data, function(index, val) {
								// $('#showItem').removeClass('hide');
								// $('#txtItemId').html(val.ItemID);
								// $('#txtItemName').html(val.NameTH);
							});
						});

					} else {
						// window.location = '?error='+data.message+'&barcode='+$('#barcode').val();
						// $('#top_alert').hide();
						// $('#modal_alert').modal({backdrop: 'static'});
						// $('#modal_alert_message').text(data.message);
						// $('#barcode').val($('#barcode').val());
						// alert(data.message);
						// $('#result').text(data.message).show();
						$('#_success').hide();
						$('#show-error').show();
						$('#show-error-text').text(data.message);
						$('#barcode').val('').blur();
					}
					$('#barcode').prop('readonly', false);
					// $('#barcode').val('').focus();
				});
			} else {
				// 
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
