<?php $this->layout("layouts/base", ['title' => 'Report Quality Checking']); ?>

<style type="text/css">
	.btn-xl {
	    padding: 5px 22px;
	    font-size: 20px;
	    border-radius: 5px;
        
	}
</style>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
	<div class="panel-heading">Report Quality Checking</div>
	<div class="panel-body">
		<form id="quality_params" method="post" action="<?php echo APP_ROOT; ?>/api/excel/quality"  target="_blank">
			<div class="form-group">
				<label for="param_date">Date</label>
				<input type="text" id="param_date" name="param_date" class="form-control" autocomplete="off" required>
			</div>

			<div class="form-group">
				<label for="shift">Shift</label>
				<select name="shift" id="shift" class="form-control" required>
					<option value="day">กลางวัน</option>
					<option value="night">กลางคืน</option>
				</select>
			</div>
			<!-- <input type="text" id="check_type" name="check_type" /> -->
			<!-- <button type="submit" class="btn btn-primary btn-block btn-lg">View Report</button> -->
			<!-- <button type="button" class="btn btn-primary btn-xl " id="to_pdf" ><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button> -->
            <button type="button" class="btn btn-success btn-xl " id="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button>
		</form>
	</div>
</div>

<script type="text/javascript">

	jQuery(document).ready(function($) {
		$( "#param_date" ).datepicker({dateFormat: 'dd-mm-yy'});


        $('#to_excel').on('click', function(event) {
	        event.preventDefault();
					var param_date = $('input[name=param_date]').val();
					var shift = $('select[name=shift]').val();
					if(param_date =="" || shift == "")
					{
						alert("กรุณาใส่ข้อมูลให้ครบ");
						return false;
					}
	        // $('#to_pdf').attr("disabled", true);
	        // setTimeout(function () {
	        // $('#to_pdf').attr("disabled", false);
	        // }, 10000);
	        // $('input[name=check_type]').val(2);
	        $('#quality_params').submit();
        });
	});
</script>
