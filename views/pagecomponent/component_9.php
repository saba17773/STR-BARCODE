<?php $this->layout("layouts/base", ['title' => 'Component']); ?>
<style type="text/css">
	td {
    	padding: 5px;
	}
</style>

<h1 class="head-text">Component BELT Report</h1>
<hr>
<div class="panel panel-default" style="max-width: 600px; margin: 0 auto;">
  <div class="panel-body">
    <form id="form_component" method="post" action="<?php echo APP_ROOT; ?>/component/report/pdf"
		onsubmit="return form_component()" target="_blank">
		<table align="center">
		<tr>
			<td colspan="2">
			<input type="hidden" name="mode" id="mode">
			<input type="hidden" name="report_type" id="report_type" value="9">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<label>วันที่</label>
				<input type="text" id="date_component" name="date_component" class=form-control required  placeholder="เลือกวันที่..." autocomplete="off"/>
			</td>
		</tr>
		<tr>
			<td>
			<button type="submit" id="a" class="btn btn-info btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> ดูรายงาน กะเช้า </button>
			</td>
			<td>
			<button type="submit" id="b" class="btn btn-danger btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> ดูรายงาน กะกลางคืน </button>
			</td>
		</tr>
		</table>
	</form>
  </div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		$( "#date_component" ).datepicker({dateFormat: 'dd-mm-yy'});

		$('#a').on('click',  function() {
			$('#mode').val('a');
		});

		$('#b').on('click',  function() {
			$('#mode').val('b');
		});
	});
</script>