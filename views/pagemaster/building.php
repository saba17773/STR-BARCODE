<?php $this->layout("layouts/base", ['title' => 'Building']); ?>
<style>
	.btn-xl {
		padding: 5px 22px;
		font-size: 20px;
		border-radius: 5px;
	}
</style>
<h1 class="head-text">Building Report</h1>
<hr>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
	<div class="panel-body">
		<form id="form_building" method="post" action="<?php echo APP_ROOT; ?>/api/pdf/building" target="_blank">

			<div class="form-group">
				<label for="date">Date</label>
				<input type="text" id="date_building" name="date_building" class=form-control required placeholder="เลือกวันที่..." />
				<input type="text" id="check_type" name="check_type" hidden />
			</div>
			<div class="form-group">
				<label for="shift">Shift</label>
				<select name="shift" id="shift" class="form-control" required>
					<option value="day">กลางวัน</option>
					<option value="night">กลางคืน</option>
				</select>
			</div>
			<div class="form-group">
				<label for="BOI">BOI</label><br>
				<select name="selectMenuBOI[]" id="selectMenuBOI" multiple="multiple" style="width: 370px">
					<!-- <input type="text" id="dataBOI" name="date_building" class=form-control required  placeholder="เลือกวันที่..." /> -->


				</select>

			</div>

			<div class="form-group" style="display: block;">
				<strong>Type : </strong>
				<label style="padding-left: 40px;">
					<input type="radio" name="item_group" value="tbr" /> TBR
				</label>
				<label style="padding-left: 40px;">
					<input type="radio" name="item_group" value="pcr" /> PCR
				</label>
			</div>

			<!-- <div class="form-group">
			<label for="group">Group</label>
			<select name="group" id="group" class="form-control" required></select>
		</div> -->

			<button type="button" class="btn btn-primary btn-xl " id="to_pdf"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>
			<button type="button" class="btn btn-success btn-xl " id="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button>

		</form>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("#date_building").datepicker({
			dateFormat: 'dd-mm-yy'
		});
		$('#selectMenuBOI').html("");
		$('#selectMenuBOI').multipleSelect({
			single: true
		});
		getPressSide()
			.done(function(data) {
				$('select[name=group]').html('');
				$.each(data, function(index, val) {
					$('select[name=group]').append('<option value="' + val.ID + '">' + val.Description + '</option>');
				});
			});
		getPressSideBOI()
			.done(function(data) {
				$('#selectMenuBOI').append('<option value="1">ALL</option>');
				$.each(data, function(k, v) {

					$('#selectMenuBOI').append('<option value="' + v.ID + '">' + v.ID + '</option>');
				});
				//  $('#selectMenu').multipleSelect({single: true});
				$('#selectMenuBOI').multipleSelect({
					single: true
				});
			});


		$('#to_pdf').on('click', function(event) {
			event.preventDefault();
			$('#to_excel').attr("disabled", true);
			setTimeout(function() {
				$('#to_excel').attr("disabled", false);
			}, 10000);
			$('input[name=check_type]').val(1);
			$('#form_building').submit();

		});

		$('#to_excel').on('click', function(event) {
			event.preventDefault();
			$('#to_pdf').attr("disabled", true);
			setTimeout(function() {
				$('#to_pdf').attr("disabled", false);
			}, 10000);
			$('input[name=check_type]').val(2);
			$('#form_building').submit();

		});
	});

	function getPressSide() {
		return $.ajax({
			url: base_url + '/api/shift/all',
			type: 'get',
			dataType: 'json',
			cache: false
		});
	}

	function getPressSideBOI() {
		return $.ajax({
			url: base_url + '/api/press/allBOI',
			type: 'get',
			dataType: 'json',
			cache: false
		});
	}
</script>