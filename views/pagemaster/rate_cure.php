<?php $this->layout("layouts/base", ['title' => 'Calculate Rate Build']); ?>
<h1 class="head-text">Curing Rate Report</h1>
<hr>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
  <div class="panel-body">
    <form id="form_rate" method="post" 
		action="<?php echo APP_ROOT; ?>/api/pdf/ratecuring"
		onsubmit="return form_rate()" target="_blank" > 
		<div class="form-group">
			<label for="date">Date</label>
			<input type="text" id="date_rate" name="date_rate" class=form-control required  placeholder="เลือกวันที่..." autocomplete="off" />
		</div>
		<div class="form-group">
			<label for="shift">Shift</label>
			<select name="shift" id="shift" class="form-control" required>
			  <option value="day">กลางวัน</option>
			  <option value="night">กลางคืน</option>
			</select>
		</div>
		
		<!-- <div class="form-group" style="display: block;">
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
		</div> -->

		<button type="submit" id ="view" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>

	</form>
  </div>
</div>

<script type="text/javascript">

	jQuery(document).ready(function($) {
	
		$( "#date_rate" ).datepicker({dateFormat: 'dd-mm-yy'});
		// group = "TBR";

	});
</script>