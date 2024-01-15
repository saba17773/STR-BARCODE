<?php $this->layout("layouts/base", ['title' => 'Unfoil']); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="margin: auto; max-width: 500px;">
	<div class="panel-heading">Unfoil</div>
	<div class="panel-body">
		<form id="formUnfoil">
			<div class="form-group">
				<label>Barcode</label>
				<input type="text" class="form-control" id="barcode" name="barcode" autofocus>
			</div>
		</form>
	</div>
</div>


<script>
	jQuery(document).ready(function($) {
		$('#formUnfoil').submit(function(e) {
			e.preventDefault();
			gojax('post', '/api/v1/unfoil/save', {
				barcode: $('#barcode').val()
			}).done(function(d) {
				alert(d.message);
				$('#barcode').val('').focus();
			});
		});
	});
</script>
