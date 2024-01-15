<?php $this->layout("layouts/base", ['title' => 'รายงานยาง PCR ส่งไปที่ SVO']); ?>
<div class="head-space"></div>
<div class="panel panel-default form-center">
	<div class="panel-heading">รายงานยาง PCR ส่งไปที่ SVO</div>
  <div class="panel-body">
    <form id="form_building" method="post" action="<?php echo APP_ROOT; ?>/api/v2/transfer_pcr_to_svo"
		onsubmit="return form_building()">

		<div class="form-group">
			<label for="date">Date</label>
			<input type="text" id="datewarehouse" name="datewarehouse" class=form-control autocomplete="off" required  placeholder="เลือกวันที่..." />
		</div>
		<div class="form-group">
			<label for="shift">Shift</label>
			<select name="shift" id="shift" class="form-control" required>
			  <option value="day">กลางวัน</option>
			  <option value="night">กลางคืน</option>
			</select>
		</div>
		<div class="form-group">
			<label for="location_type">Location</label>
			<select name="location_type" id="location_type" class="form-control">
				<option value="2">Final</option>
				<option value="3">Warehouse</option>
			</select>
		</div>
		<div class="form-group">
            <label for="time">Time</label><br>
            <select name="selecttime[]" id="selecttime"  multiple="multiple" style="width: 200px;" required>
            </select>
        </div>
		<input type="hidden" name="warehouse" id="warehouse" value="sent">
		<button type="submit" class="btn btn-primary btn-lg btn-block">Print</button>

	</form>
  </div>
</div>

<script type="text/javascript">
	$( "#datewarehouse" ).datepicker({dateFormat: 'dd-mm-yy'});
	$('#shift').on('change', function(){
		  var val = $(this).val();
		  if(val === 'day') {
		    //alert('day!')
		    $('#selecttime').html("");
		  	getPressSide()
			.done(function(data) {
				$.each(data, function(k, v) {
					$('#selecttime').append('<option value="'+ v.TimeID +'">'+v.TimeTo+'-'+v.TimeFrom+'</option>');
				});
				$('#selecttime').multipleSelect();
			});
		  }
		  else if(val === 'night') {
		    //alert('night!')
		    $('#selecttime').html("");
		  	getPressSideN()
			.done(function(data) {
				$.each(data, function(k, v) {
					$('#selecttime').append('<option value="'+ v.TimeID +'">'+v.TimeTo+'-'+v.TimeFrom+'</option>');
				});
				$('#selecttime').multipleSelect();
			});
		  }
  	});


	getPressSide()
			.done(function(data) {
				$.each(data, function(k, v) {
					$('#selecttime').append('<option value="'+ v.TimeID +'">'+v.TimeTo+'-'+v.TimeFrom+'</option>');
				});
				$('#selecttime').multipleSelect();
			});


	function getPressSide() {
		return $.ajax({
			url : base_url + '/api/press/allday',
			type : 'get',
			dataType : 'json',
			cache : false
		});
	}
	function getPressSideN() {
		return $.ajax({
			url : base_url + '/api/press/allnight',
			type : 'get',
			dataType : 'json',
			cache : false
		});
	}
</script>
