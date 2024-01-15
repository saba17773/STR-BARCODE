<?php $this->layout("layouts/handheld", ["title" => "Final Incoming"]); ?>

<style>
	#display_result_red {
		z-index: 9999999;
		background: red;
		position: absolute;
		width: 100%;
		height: 100%;
		padding-left: 35%;
    padding-top: 20%;
		display: none;
	}

	#display_result_green {
		z-index: 9999999;
		background: green;
		position: absolute;
		width: 100%;
		height: 100%;
		display: none;
	}
</style>

<div id="display_result_red">
	<img src="/assets/images/error01.png" id="close_error" width="30%" alt="" style="text-align: center;">
</div>
<div id="display_result_green"></div>

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
				placeholder="Barcode" required autocomplete="off">
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

		$('#close_error').on('click', function(event) {
			event.preventDefault();
			$("#display_result_red").hide();
			$('#barcode').val('').focus();
		});
		
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
						// $('#top_alert').show();
						// $('#top').text('Barcode ล่าสุด '+ $('#barcode').val());
						// $('#modal_alert').modal('hide');
						// 
						$('#display_result_green').show();
						$('#display_result_red').hide();
						$('#barcode').val('').blur();
						setTimeout(function(){
							$('#display_result_green').hide();
						}, 2000);

						// gojax('get', base_url+'/api/barcode/'+ $('#barcode').val())
						// .done(function(data) {
						// 	$.each(data, function(index, val) {
						// 		$('#showItem').removeClass('hide');
						// 		$('#txtItemId').html(val.ItemID);
						// 		$('#txtItemName').html(val.NameTH);
						// 	});
						// });

					} else {
						// window.location = '?error='+data.message+'&barcode='+$('#barcode').val();
						// $('#top_alert').hide();
						// $('#modal_alert').modal({backdrop: 'static'});
						// $('#modal_alert_message').text(data.message);
						// $('#barcode').val($('#barcode').val());
						// alert(data.message);
						$('#display_result_red').show();
						$('#display_result_green').hide();
						$('#barcode').val('').blur();
						// $('#check_build').val('').focus();
					}
					$('#barcode').prop('readonly', false);
					// $('#barcode').val('').focus();
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
