<?php $this->layout("layouts/base", ['title' => 'Curing']); ?>
<h1 class="head-text">Curing Report</h1>
<hr>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
	<div class="panel-body">
		<form id="form_curing" method="post" action="<?php echo APP_ROOT; ?>/api/pdf/curing" target="_blank">

			<div class="form-group">
				<label for="date">Date</label>
				<input type="text" id="date_curing" name="date_curing" class=form-control required placeholder="เลือกวันที่..." autocomplete="off" />
			</div>
			<div class="form-group">
				<label for="shift">Shift</label>
				<select name="shift" id="shift" class="form-control" required>
					<option value="day">กลางวัน</option>
					<option value="night">กลางคืน</option>
				</select>
			</div>

			<div class="form-group">
				<label for="press">Press</label><br>
				<select name="selectMenu[]" id="selectMenu" multiple="multiple" style="width: 150px">
				</select>
				<input type="button" name="click_a" id="click_a" class="btn btn-primary btn-sm" value="A" style="width:75px">
				<input type="button" name="click_b" id="click_b" class="btn btn-primary btn-sm" value="B" style="width:75px">
			</div>

			<button type="submit" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>

		</form>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("#date_curing").datepicker({
			dateFormat: 'dd-mm-yy'
		});
		$('#press').html("");
		$('#selectMenu').html("");
		$('#click_b').prop('disabled', true);
		getPressSide()
			.done(function(data) {
				$.each(data, function(k, v) {
					$('#selectMenu').append('<option value="' + v.BDF + '">' + v.BDF + v.No + '</option>');
				});
				$('#selectMenu').multipleSelect({
					single: true
				});
			});
		$('#click_a').on('click', function() {
			$('#click_a').prop('disabled', true);
			$('#click_b').prop('disabled', false);
			$('#selectMenu').html("");
			getPressSideA()
				.done(function(data) {
					$.each(data, function(k, v) {
						$('#selectMenu').append('<option value="' + v.BDF + '">' + v.BDF + v.No + '</option>');
					});
					$('#selectMenu').append('<option value="dummy">Dummy</option>');
					$('#selectMenu').multipleSelect({
						single: true
					});
				});
		});
		$('#click_b').on('click', function() {
			$('#click_b').prop('disabled', true);
			$('#click_a').prop('disabled', false);
			$('#selectMenu').html("");
			getPressSide()
				.done(function(data) {
					$.each(data, function(k, v) {
						$('#selectMenu').append('<option value="' + v.BDF + '">' + v.BDF + v.No + '</option>');
					});
					$('#selectMenu').multipleSelect({
						single: true
					});
				});
		});

	});

	function getPressSide() {
		return $.ajax({
			url: base_url + '/api/press/allBDF',
			type: 'get',
			dataType: 'json',
			cache: false
		});
	}

	function getPressSideA() {
		return $.ajax({
			url: base_url + '/api/press/allBDFA',
			type: 'get',
			dataType: 'json',
			cache: false
		});
	}
</script>