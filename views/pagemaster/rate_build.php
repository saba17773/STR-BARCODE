<?php $this->layout("layouts/base", ['title' => 'Calculate Rate Build']); ?>
<h1 class="head-text">Building Rate Report</h1>
<hr>
<style>

</style>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
	<div class="panel-body">
		<!-- <form id="form_rate" method="post" action="<?php echo APP_ROOT; ?>/api/pdf/ratebuilding" onsubmit="return form_rate()" target="_blank"> -->
		<form id="form_rate" method="post" action="<?php echo APP_ROOT; ?>/api/pdf/ratebuilding" target="_blank">
			<div class="form-group">
				<label for="date">Date</label>
				<input type="text" id="date_rate" name="date_rate" class=form-control required placeholder="เลือกวันที่..." autocomplete="off" />
				<input type="text" id="check_type" name="check_type" hidden />
			</div>
			<div class="form-group">
				<label for="shift">Shift</label>
				<select name="shift" id="shift" class="form-control" required>
					<option value="day">กลางวัน</option>
					<option value="night">กลางคืน</option>
				</select>
			</div>

			<div class="form-group" style="display: block;">
				<strong>Type : </strong>
				<label style="padding-left: 40px;">
					<input type="radio" name="item_group" value="tbr" checked /> TBR
				</label>
				<label style="padding-left: 40px;">
					<input type="radio" name="item_group" value="pcr_n" /> PCR
				</label>
				<label style="padding-left: 40px;">
					<input type="radio" name="item_group" value="pcr" /> PCR_Ply
				</label>
			</div>

			<!-- <button type="submit" id="view" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button> -->
			<button type="button" class="btn btn-primary " style="width: 180px; margin: 0 auto;" id="to_pdf"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>
			<button type="button" class="btn btn-success btn-xl " style="width: 180px; margin: 0 auto;" id="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button>
		</form>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		$("#date_rate").datepicker({
			dateFormat: 'dd-mm-yy'
		});
		group = "TBR";

		// $('#selectMenu').html("");

		// $('#grp_tbr').prop('disabled',true);
		// $('#grp_pcr').prop('disabled',false);

		// getLineMachine_TBR()
		// .done(function(data) {
		// 	$.each(data, function(k, v) {
		// 		$('#selectMenu').append('<option value="'+ v.Line +'">'+v.Line+v.No+'</option>');
		// 	});
		// 	$('#selectMenu').multipleSelect({single: true});
		// });

		// $('#grp_tbr').on('click', function() {
		// $('#grp_tbr').prop('disabled',true);
		// $('#grp_pcr').prop('disabled',false);
		// group = "TBR";
		// $('#selectMenu').html("");
		// getLineMachine_TBR()
		// .done(function(data) 
		// {
		// 	$.each(data, function(k, v) 
		// 	{
		// 		$('#selectMenu').append('<option value="'+ v.Line +'">'+v.Line+v.No+'</option>');
		// 	});
		// 	$('#selectMenu').multipleSelect({single: true});
		// });
		// });

		// $('#grp_pcr').on('click', function() {
		// $('#grp_tbr').prop('disabled',false);
		// $('#grp_pcr').prop('disabled',true);
		// group = "PCR";
		// $('#selectMenu').html("");
		// getLineMachine_PCR()
		// .done(function(data) 
		// {
		// 	$.each(data, function(k, v) 
		// 	{
		// 		$('#selectMenu').append('<option value="'+ v.Line +'">'+v.Line+v.No+'</option>');
		// 	});
		// 	$('#selectMenu').multipleSelect({single: true});
		// });
		// });


		// function getLineMachine_TBR() {
		// 	return $.ajax({
		// 		url : base_url + '/api/line/Line_TBR',
		// 		type : 'get',
		// 		dataType : 'json',
		// 		cache : false
		// 	});
		// }

		// function getLineMachine_PCR() {
		// 	return $.ajax({
		// 		url : base_url + '/api/line/Line_PCR',
		// 		type : 'get',
		// 		dataType : 'json',
		// 		cache : false
		// 	});
		// }

		$('#to_pdf').on('click', function(event) {
			event.preventDefault();
			$('#to_excel').attr("disabled", true);
			setTimeout(function() {
				$('#to_excel').attr("disabled", false);
			}, 10000);
			$('input[name=check_type]').val(1);
			$('#form_rate').submit();

		});

		$('#to_excel').on('click', function(event) {
			event.preventDefault();
			$('#to_pdf').attr("disabled", true);
			setTimeout(function() {
				$('#to_pdf').attr("disabled", false);
			}, 10000);
			$('input[name=check_type]').val(2);
			$('#form_rate').submit();

		});


	});
</script>