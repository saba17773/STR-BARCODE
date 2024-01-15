<?php $this->layout("layouts/handheldmobile", ['title' => 'Change Batch']); ?>

<div class="head-space"></div>
<br>
<div class="panel panel-default">
	<div class="panel-heading"><h1>Change Batch</h1></div>
	<div class="panel-body">

		<form id="form_change_batch">
			<div class="form-group">
				<label>Date</label><br>
				<input type="text" name="_date" onchange="return on_date_change()" class="form-control" style="width: 200px;" autofocus >
			</div>
			<br>
			<div class="form-group">
				<label>Batch</label><br>
				<input type="text" name="_batch" class="form-control" style="width: 200px;" readonly >
			</div>
			<br>
			<div class="form-group">
				<label>Barcode</label><br>
				<input type="text" name="_barcode" class="form-control" >
			</div>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('input[name=_date]').datepicker({dateFormat: 'dd-mm-yy',showOn: "button",
      buttonText : "Enter Date"});

		$('.ui-datepicker-trigger').css({
    	'margin': '-5px 10px',
    	'font-size': '2em'
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
						 alert(data.message);

						// $('#top_alert').hide();
						// $('#modal_alert').modal({backdrop: 'static'});
						// $('#modal_alert_message').text(data.message);

					} else {
						alert(" Barcode ล่าสุด " + $('input[name=_barcode]').val());

						// $('#top_alert').show();
						// $('#modal_alert').modal('hide');
						// $('#top_alert_message').text('Barcode ล่าสุด ' + $('input[name=_barcode]').val());
					}

					$('input[name=_barcode]').val('');
					$('input[name=_batch]').val('');
					$('input[name=_date]').val('').focus();
					onFocus = 'input[name=_date]';
				})
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