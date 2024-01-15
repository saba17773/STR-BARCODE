<?php $this->layout("layouts/base", ['title' => 'Greentire Incoming']); ?>

<style>
	td {
		padding: 10px;
		width: 33.33%;
	}
</style>

<div class="head-space"></div>

<div class="panel panel-default form-center">
  <div class="panel-heading">Greentire Incoming</div>
  <div class="panel-body">

    <form id="form_gt_incoming">

		<div class="form-group">
			<label for="building_code">Building MC.</label>
			<input type="text"  id="building_code" class="form-control inputs input-lg" name="building_code"  required autocomplete="off" >
		</div>

		<div class="form-group">
			<label for="greentire_code">Greentire Code</label>
			<input type="text" id="greentire_code" class="form-control inputs input-lg" name="greentire_code" required autocomplete="off" autofocus>
		</div>

	<!-- 	<div class="form-group">
			<label for="weight">Weight (KG.)</label>
			<input type="number" min="1" max="99" id="weight" class="form-control inputs input-lg" name="weight" required>
		</div> -->

		<div class="form-group">
			<label for="barcode">Barcode</label>
			<input type="text" id="barcode" class="form-control inputs input-lg" name="barcode" required autocomplete="off">
		</div>

		<div class="form-group">
			<button type="button" id="end_build" class="btn btn-danger btn-lg btn-block">End</button>
		</div>
	</form>
  </div>
</div>

<!-- Modal -->
<div class="modal" id="modal_numpad" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">ใส่น้ำหนัก</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div id="show_number" class="text-center" style="font-size: 2em; font-weight: bold;"></div>
        <table width="100%">
        	<tr>
        		<td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">1</button></td>
        		<td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">2</button></td>
        		<td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">3</button></td>
        	</tr>
        	<tr>
        		<td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">4</button></td>
        		<td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">5</button></td>
        		<td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">6</button></td>
        	</tr>
        	<tr>
        		<td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">7</button></td>
        		<td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">8</button></td>
        		<td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">9</button></td>
        	</tr>
        	<tr>
        		<td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">0</button></td>
        		<td><button class="btn btn-danger btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">C</button></td>
        		<td><button class="btn btn-success btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-ok"></span></button></td>
        	</tr>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
	jQuery(document).ready(function($) {
		var temp_weight_value = '0';
		// $('#weight').mask('00.00', {reverse: false});
		// $('#building_code').focus();

		// $('#modal_numpad').on('hidden.bs.modal', function() {
		// 	$('#barcode').val('').focus();
		// });

		// $('#weight').focus(function(event) {
		// 	$('#modal_numpad').modal({backdrop: 'static'});
		// });

		// $('.btn-cal').on('click', function() {
		// 	temp_weight_value += $(this).text();
		// 	if ($(this).text() === 'C' || (parseFloat(temp_weight_value)/100) > 99.99) {
		// 		temp_weight_value = '0';
		// 	}
		// 	$('#show_number').text(parseFloat(temp_weight_value) / 100);
		// 	$('#weight').val(parseFloat(temp_weight_value) / 100);
		// });

		// $('#barcode').focus(function(event) {
		// 	var weight_val = parseInt($('#weight').val());
		// 	if (typeof weight === 'undefined' || weight_val >= 999) {
		// 		$("#weight").val('').focus();
		// 		$(".i_num_pad").show();
		// 	}
		// });

		// $('#greentire_code').keydown(function(event) {
		// 	if (event.which === 13) {
		// 		$(".i_num_pad").show();
		// 	}
		// });
		var sessionBuild = '<?php echo $_SESSION['build_mc']; ?>';
		if (sessionBuild==""){
			alert("กรุณาใส่เครื่องก่อน");
			window.location = base_url + '/addUser';
		}
		$('input[name=building_code]').prop('readonly', true);
		$('input[name=building_code]').val('<?php echo $_SESSION['build_mc']; ?>'); 

		$('#modal_alert').on('hidden.bs.modal', function() {
			$(onFocus).focus();
		});

		$('#end_build').on('click', function() {
			$('input[name=building_code]').prop('readonly', true);
			$('input[name=greentire_code]').prop('readonly', false);
			$('input[name=barcode]').prop('readonly', false);
			$('input.inputs').val('');
			$('#greentire_code').focus();
			$('input[name=building_code]').val('<?php echo $_SESSION['build_mc']; ?>');
		});

		$('#barcode').keydown(function(event) {
			if (event.which === 13) {

				$('input[name=building_code]').prop('readonly', true);
				$('input[name=greentire_code]').prop('readonly', true);
				$('input[name=barcode]').prop('readonly', true);
				temp_weight_value = '0';
				$('#show_number').text('');

				$('#modal_loading').modal({backdrop: 'static'});

				gojax_f('post', base_url + '/api/greentire/receive', '#form_gt_incoming')
				.done(function(data) {
					$('#modal_loading').modal('hide');
					if (data.status == 200) {
						
						gojax_f('post', base_url + '/api/greentire/buildtrans', '#form_gt_incoming')
						.done(function(data) {
							if (data.status == 404) 
							{
								alert(data.message);
							}
						});
						gojax_f('post', base_url + '/api/greentire/total_ratetrans', '#form_gt_incoming')
						.done(function(data) {
							if (data.status == 404) 
							{
								alert(data.message);
							}
						});
						// window.location = base_url + '/greentire/incoming?success='+$('input[name=barcode]').val();

						$('#top_alert').show();
						$('#top_alert_message').text('Barcode ล่าสุด '+$('input[name=barcode]').val());
						$('#modal_alert').modal('hide');

						$('input[name=building_code]').prop('readonly', true);
						$('input[name=greentire_code]').prop('readonly', true);
						$('input[name=barcode]').prop('readonly', false);

						$('input[name=barcode]').val('').focus();

						//insert BuildTrans
						// $('#modal_loading').modal('hide');
						

					} 
					else 
					{
						$('input[name=building_code]').prop('readonly', true);
						$('input[name=greentire_code]').prop('readonly', false);
						$('input[name=barcode]').prop('readonly', false);


						$('#top_alert').hide();
						$('#modal_alert').modal({backdrop: 'static'});
						$('#modal_alert_message').text(data.message);
						$('input.inputs').val('');
							// $('input[name=barcode]').val('').focus();
						onFocus = '#greentire_code';
						$('#greentire_code').focus();
						$('input[name=building_code]').val('<?php echo $_SESSION['build_mc']; ?>');
					}
				});



					
					// .fail(function() {

					// 	$('input[name=building_code]').prop('readonly', false);
					// 	$('input[name=greentire_code]').prop('readonly', false);
					// 	$('input[name=barcode]').prop('readonly', false);

					// 	$('#modal_alert').modal({backdrop: 'static'});
					// 	$('#modal_alert_message').text('ไม่สามารถทำรายการได้');
					// 	$('#building_code').focus();

					// });

				

				
			}
		});
	});
</script>
