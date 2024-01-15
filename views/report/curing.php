<?php $this->layout("layouts/base", ["title" => "Curing Report"]); ?>

<div class="head-space"></div>

<div class="panel panel-default">
	<div class="panel-heading">Curing</div>
	<div class="panel-body">
		<form  id="form_view_curing_report" action="<?php echo APP_ROOT; ?>/report/curing/pdf" method="post">
			<div class="form-group">
				<label for="date">Date</label>
				<input type="date" name="date" id="date" placeholder="วันที่" class="form-control" required>
			</div>
			<div class="form-group">
				<label for="date">กะทำงาน</label>
				<select name="shift" id="shift" class="form-control" required>
					<option value="day">Day</option>
					<option value="night">Night</option>
				</select>
			</div>

			<div class="form-group">
				<div class="btn-group" data-toggle="buttons">
				  <label class="btn btn-primary active">
				    <input type="radio" name="switch" id="switch" autocomplete="off" value="A" checked> A, C, E
				  </label>
				  <label class="btn btn-primary">
				    <input type="radio" name="switch" id="switch" autocomplete="off" value="B"> B, D, F
				  </label>
				</div>
			</div>

			<div class="form-group">
				<label for="press_a">Press</label> <br>
				<select name="press_a[]" id="press_a" style="width: 300px;">
					<option value="">= เลือก =</option>
					<option value="A">A1-12</option>
					<option value="C">C1-12</option>
					<option value="E">E1-12</option>
				</select>
			</div>

			<div class="form-group">
				<label for="press_b">Press</label> <br>
				<select name="press_b[]" id="press_b" style="width: 300px;">
					<option value="">= เลือก =</option>
					<option value="B">B1-12</option>
					<option value="D">D1-12</option>
					<option value="F">F1-12</option>
				</select>
			</div>
			<button type="submit" class="btn btn-primary">Submit</button>

		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('#date').datepicker({dateFormat: 'dd-mm-yy'});
		$('#press_a').multipleSelect({placeholder: 'เลือกข้อมูล', filter: true});
		$('#press_b').multipleSelect({placeholder: 'เลือกข้อมูล', filter: true});
	});
</script>
