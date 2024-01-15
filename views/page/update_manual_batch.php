<?php $this->layout("layouts/base", ['title' => 'Change Batch']); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 600px; margin: auto;">
	<div class="panel-heading">Update Batch</div>
	<div class="panel-body">

		<form id="form_change_batch">
			<div class="form-group">
				<label>Select batch</label>
        <select name="_date" id="_date" required class="form-control">
          
        </select>
			</div>

			<div class="form-group">
				<label>New batch is</label>
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

    $('select[name=_date]').html('<option value="">== เลือก ==</option>');
    gojax('get', '/api/v2/batch/active').done(function(data) {
      $.each(data, function(i,v ) {
        $('select[name=_date]').append('<option value="'+v.FormatBatch+'">'+v.FormatBatch+'</option>');
      });
    });

    $('select[name=_date]').change(function() {
      $('input[name=_batch]').val($('select[name=_date]').val());
      $('input[name=_barcode]').val('').focus();
    });

		$('#modal_alert').on('hidden.bs.modal', function() {
			$(onFocus).focus();
		});

		$('input[name=_barcode]').keypress(function(event) {
			if (event.which === 13) {
				gojax('post', '/change_batch/save', {
					_date: $('select[name=_date]').val(),
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
					$('select[name=_date]').val('').focus();
					onFocus = 'select[name=_date]';
				}).fail(function(data) {
					$('#top_alert').hide();
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text(data.message);

					$('input[name=_barcode]').val('');
					$('input[name=_batch]').val('');
					$('select[name=_date]').val('').focus();
				});
			}
		});
	});

	function on_date_change() {
		gojax('post', '/get_week', {
			datetime: $('select[name=_date]').val()
		}).done(function(data) {
			$('input[name=_batch]').val(data.week);
			$('input[name=_barcode]').val('').focus();
		}).fail(function(data) {
			$('input[name=_batch]').val('');
		});
	}
</script>