<?php $this->layout("layouts/base", ['title' => 'Finish Good Withdraw']); ?>
<h1 class="head-text">Finish Good Withdraw Report</h1>
<hr>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
  <div class="panel-body">
    <form id="form_internal" method="post" action="<?php echo APP_ROOT; ?>/api/v1/finish_good/withdraw/pdf"
		onsubmit="return form_internal()">

		<div class="form-group">
			<label for="date">Date</label>
			<input type="text" id="date_withdraw" name="date_withdraw" class=form-control required  placeholder="เลือกวันที่..." />
		</div>

		<button type="submit" class="btn btn-primary btn-lg btn-block"> <span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>

	</form>
  </div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$( "#date_withdraw" ).datepicker({dateFormat: 'dd-mm-yy'});
	});
</script>
