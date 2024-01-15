<?php $this->layout("layouts/base", ['title' => 'Change Batch']); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 600px; margin: auto;">
	<div class="panel-heading">Change Batch</div>
	<div class="panel-body">

		<form id="form_change_batch">
			<div class="form-group">
				<label>Date</label>
				<input type="text" name="_date" onchange="return on_date_change()" class="form-control" autofocus required>
			</div>

			<div class="form-group">
				<label>Batch</label>
				<input type="text" name="_batch" class="form-control" readonly required>
			</div>

			<div class="form-group">
				<label>Barcode</label>
				<input type="text" name="_barcode" class="form-control" required>
			</div>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('input[name=_date]').datepicker({dateFormat: 'dd-mm-yy',showOn: "button",
      buttonText: "เลือกวันที่"});

		$('.ui-datepicker-trigger').css({
    	'margin': '10px 0px',
    	'font-size': '1.2em'
    });

		$('#modal_alert').on('hidden.bs.modal', function() {
			$(onFocus).focus();
		});

		$('input[name=_barcode]').keypress(function(event) {
			if (event.which === 13) {
				gojax('post', '/change_batch/save', {
					_date: $('input[name=_date]').val(),
					_batch: $('input[name=_batch]').val(),
					_barcode: $('input[name=_barcode]').val()
				}).done(function(data) {
					if (data.result === false) {
						$('#top_alert').hide();
						$('#modal_alert').modal({backdrop: 'static'});
						$('#modal_alert_message').text(data.message);
					} else {
						$('#top_alert').show();
						$('#modal_alert').modal('hide');
						$('#top_alert_message').text('Barcode ล่าสุด ' + $('input[name=_barcode]').val());
					}

					$('input[name=_barcode]').val('');
					$('input[name=_batch]').val('');
					$('input[name=_date]').val('').focus();
					onFocus = 'input[name=_date]';
				}).fail(function(data) {
					$('#top_alert').hide();
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text(data.message);

					$('input[name=_barcode]').val('');
					$('input[name=_batch]').val('');
					$('input[name=_date]').val('').focus();
				});
			}
		});
	});

	function on_date_change() {
		gojax('post', '/get_week', {
			datetime: $('input[name=_date]').val()
		}).done(function(data) {
			$('input[name=_batch]').val(data.week);
			$('input[name=_barcode]').val('').focus();
		}).fail(function(data) {
			$('input[name=_batch]').val('');
		});
	}
</script>