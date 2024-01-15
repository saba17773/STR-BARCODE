<?php $this->layout("layouts/base", ["title" => "Report Repair Daily"]) ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
	<div class="panel-heading">Repair Daily</div>
	<div class="panel-body">
		<form id="formDateScrap">
			<div class="form-group">
				<label for="date_scrap">วันที่ซ่อมยาง</label>
				<input type="date" name="date_scrap" id="date_scrap" class="form-control">
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
			<button class="btn btn-primary btn-block btn-lg" type="submit"><span class="glyphicon glyphicon-print"></span> Print</button>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('#date_scrap').datepicker({
			dateFormat: 'yy-mm-dd', 
			'setDate': new Date()
		});

		$('#formDateScrap').submit(function(event) {
			event.preventDefault();
			var date_scrap = $('#date_scrap').val();
			var product_group = $('input[name=item_group]:checked').val();
			if (!!date_scrap) {
				// console.log(date_scrap);
				window.open(base_url+'/report/curetire/scrap/'+date_scrap+'/'+product_group, '_blank');
			}
		});
	});
</script>