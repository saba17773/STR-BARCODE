<?php $this->layout('layouts/handheld', ['title' => 'Check cure']); ?>

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

<div>
	<div style="text-align: center; padding: 10px; font-weight: bold;">Check Cure</div>
	<hr />
	<div style="text-align: center;">
		<label>Barcode</label> <br> <br>
		<input type="text" name="check_build" id="check_build" class="inputs" autocomplete="off">
	</div>
</div>

<script>
	jQuery(document).ready(function($) {

		$('#check_build').val('').focus();

		$('#close_error').on('click', function(event) {
			event.preventDefault();
			$("#display_result_red").hide();
			$('#check_build').val('').focus();
		});
		$('#check_build').keydown(function(e) {
			if (e.which === 13) {
				gojax('post', '/api/v1/build/check_cure', {
					barcode: $('#check_build').val()
				}).done(function(data) {
					if (data.result === true) {
						$('#display_result_green').show();
						$('#display_result_red').hide();
						$('#check_build').val('').focus();
						setTimeout(function(){
							$('#display_result_green').hide();
						}, 2000);

					} else {

						//alert(data.message);
						$('#display_result_red').show();
						$('#display_result_green').hide();
						$('#check_build').val('').blur();
					}

				});
			}
		});
	});
</script>
