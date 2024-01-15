<?php $this->layout("layouts/base", ['title' => 'Xray']); ?>

<form id="form_xray" class="form-center" onsubmit="return submit_xray_issue()">
	<h1>X-Ray</h1>
	<hr />
	<div class="form-group">
		<input type="text" name="barcode" class="form-control">
	</div>
	<button class="btn btn-primary btn-lg btn-block">Save</button>
</form>

<script>
	jQuery(document).ready(function($) {
		$('input[name=barcode]').focus();
	});

	function submit_xray_issue() {
		var barcode = $('input[name=barcode]');
		if (!!barcode.val()) {
			$.ajax({
				url : base_url + '/api/xray/issue',
				type : 'post',
				cache : false,
				data : $('form#form_xray').serialize()
			})
			.done(function(data) {
				console.log(data);
			});
		}
		return false;
	}
</script>
