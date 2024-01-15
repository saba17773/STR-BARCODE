<?php $this->layout("layouts/base", ['title' => 'Foil']); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="margin: auto; max-width: 500px;">
	<div class="panel-heading">Foil</div>
	<div class="panel-body">
		<form id="formFoil">
			<div class="form-group">
				<label>Old barcode</label>
				<input type="text" name="old_barcpde" id="old_barcode" class="form-control inputs" autofocus>
			</div>

				<div class="form-group">
				<label>New barcode</label>
				<input type="text" name="new_barcode" id="new_barcode" class="form-control inputs">
			</div>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('#new_barcode').keydown(function(event) {
			if (event.which === 13) {
				gojax('post', '/api/v1/foil/save', {
					old_barcode : $('#old_barcode').val(),
					new_barcode: $('#new_barcode').val()
				}).done(function(d) {
					console.log(d);
					alert(d.message);
					$('#new_barcode').val('');
					$('#old_barcode').val('').focus();
				});
			}	
		});
	});
</script>
