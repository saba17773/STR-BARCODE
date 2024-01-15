<?php $this->layout("layouts/base", ["title" => "Report Foil"]); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
	<div class="panel-heading">Report Foil</div>
	<div class="panel-body">
		<form id="form_report_foil" action="/report/foil/pdf" method="post">
			<div class="form-group">
				<label>Date</label>
				<input type="text" class="form-control" name="date" id="date" autofocus required>
			</div>

			<div class="form-group">
				<label>Shift</label>
				<select name="shift" id="shift" class="form-control" required>
					<option value="1">กลางวัน</option>
					<option value="2">กลางคืน</option>
				</select>
			</div>

			<div class="form-group">
				<label>Time</label> <br>
				<select name="selecttime[]" id="selecttime" multiple="multiple" style="width: 400px;" required>
				</select>
			</div>

			<button class="btn btn-lg btn-block btn-primary"><span class="glyphicon glyphicon-file"></span> View Report </button>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('#date').datepicker();

		$('#selecttime').html("");
	  gojax('get', '/api/press/allday').done(function(data) {
			$.each(data, function(k, v) {
				$('#selecttime').append('<option value="'+ v.TimeID +'">'+v.TimeTo+'-'+v.TimeFrom+'</option>');
			});
			$('#selecttime').multipleSelect();
		});

		$('#shift').on('change', function(){
		  var val = $(this).val();
		  if(val === '1') {
		    $('#selecttime').html("");
			  gojax('get', '/api/press/allday').done(function(data) {
					$.each(data, function(k, v) {
						$('#selecttime').append('<option value="'+ v.TimeID +'">'+v.TimeTo+'-'+v.TimeFrom+'</option>');
					});
					$('#selecttime').multipleSelect();
				});
		  } else if(val === '2') {
		    $('#selecttime').html("");
		  	gojax('get', '/api/press/allnight').done(function(data) {
					$.each(data, function(k, v) {
						$('#selecttime').append('<option value="'+ v.TimeID +'">'+v.TimeTo+'-'+v.TimeFrom+'</option>');
					});
					$('#selecttime').multipleSelect();
				});
		  }
  	});
	});
</script>
