<?php $this->layout("layouts/base", ["title" => "Building Report By Machine"]); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
	<div class="panel-heading">Building Report By Machine</div>
	<div class="panel-body">
		<form action="<?php echo APP_ROOT; ?>/report/pdf/building-report-by-machine" method="post">
			<div class="form-group">
				<label for="date">Date</label>
				<input type="text" id="date_building" name="date_building" class=form-control required  placeholder="เลือกวันที่..." />
			</div>
			<div class="form-group">
				<label for="shift">Shift</label>
				<select name="shift" id="shift" class="form-control" required>
				  <option value="day">กลางวัน</option>
				  <option value="night">กลางคืน</option>
				</select>
			</div>
			<div class="form-group">
				<label for="machine">Machine</label><br>
				<select name="machine[]" id="machine" multiple="multiple" style="width: 400px;" required>
				</select>
			</div>
			<button type="submit" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-print"></span> Print</button>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$( "#date_building" ).datepicker({dateFormat: 'dd-mm-yy'});
		$('#machine').html("");
	  	gojax('get', '/api/building/all').done(function(data) {
			$.each(data, function(k, v) {
				$('#machine').append('<option value="'+ v.ID +'">'+v.Description+'</option>');
			});
			$('#machine').multipleSelect();
		});
	});
</script>
