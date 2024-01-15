<?php $this->layout("layouts/base", ['title' => 'Barcode Printing']); ?>

<div class="head-space"></div>

<div class="panel panel-default form-center">
	<div class="panel-heading">Barcode Printing </div>
  <div class="panel-body">
    <form id="form_barcode_printing"
		method="post"
		action="<?php echo APP_ROOT; ?>/api/barcode/printing"
		onsubmit="return submit_barcode_printing()"
		target="_blank">

		<div class="form-group">
			<label for="start">Start Barcode</label>
			<input class="form-control input-lg" type="text" name="start" id="start" readonly>
		</div>
		<div class="form-group">
			<label for="qty">QTY</label>
			<input class="form-control input-lg" onkeyup="return generateEndBarcode()" type="text" name="qty" id="qty" required>
		</div>
		<div class="form-group">
			<label for="end">End Barcode</label>
			<input class="form-control input-lg" type="text" name="end" id="end" readonly>
		</div>
		<button type="submit" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-print"></span> Print</button>

	</form>
  </div>
</div>

<script>
	jQuery(document).ready(function($) {

		$('form#form_barcode_printing').trigger('reset');
		$('input[name=qty]').focus();
		$('input[name=qty]').mask('0000');
		close_button();

		setInterval(function(){
			$.ajax({
				url: base_url + '/api/barcode/printing/last',
				type: 'get',
				cache : false,
				dataType: 'json'
			})
			.done(function(data) {
				$('input[name=start]').val(data.code);
				generateEndBarcode();
			});
		}, 1000);

		$('input[name=qty]').focus();

	});

	function generateEndBarcode() {
		var end = $('input[name=end]');
		var qty = $('input[name=qty]');
		var start = $('input[name=start]').val().substring(1);

		if (!!qty.val() && qty.val() <= 3000) {
			end.val('<?php echo BARCODE_PREFIX; ?>' + (parseInt(start)+parseInt(qty.val()) - 1) );
			open_button();
		} else {
			close_button();
			$('input[name=qty]').val('');
			end.val('');
		}
	}

	function submit_barcode_printing() {
		var qty = $('input[name=qty]').val();

		close_button();
		if (!!qty && qty != 0 && parseInt(qty) <= 3000) {
			open_button();
			$('input[name=qty]').val('').focus();
			return true;
		} else {
			open_button();
			$('input[name=qty]').val(3000).focus();
			return false;
		}
	}

	function post_printing() {
		return $.ajax({
			url: base_url + '/api/barcode/printing',
			type: 'post',
			// dataType: 'json',
			data: $('form#form_barcode_printing').serialize()
		});
	}
</script>
