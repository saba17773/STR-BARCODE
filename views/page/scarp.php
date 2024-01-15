<?php $this->layout("layouts/base", ['title' => 'Scrap']); ?>

<h1 class="head-text">Scrap</h1>

<div class="panel panel-default form-center">
  <div class="panel-body">
    <form id="formScrap" onsubmit="return postScrap()">
	
		<div class="form-group well" style="padding: 10px 10px 2px 10px;">
			<table style="margin: auto;">
				<tr valign="bottom">
					<td width="33.33%">
						<label>
							<input type="radio" name="position_scrap" value="1" style="width: 1.5em; height: 1em;"> 
			  <span style="font-size: 1.4em; padding-right: 30px;"> Top </span>
						</label>
					</td>
					<td width="33.33%">
						<label>
							<input type="radio" name="position_scrap" value="2" style="width: 1.5em; height: 1em;"> 
			  <span style="font-size: 1.4em; padding-right: 30px;"> Bottom </span>
						</label>
					</td>
					<td width="33.33%">
						<label>
							<input type="radio" name="position_scrap" value="3" style="width: 1.5em; height: 1em;"> 
			  <span style="font-size: 1.4em; padding-right: 30px;"> Both </span>
						</label>
					</td>
				</tr>
			</table>
		</div>

		<div class="form-group">
			<label for="defectCode">Defect Code</label>
			<select name="defectCode" id="defectCode" class="form-control input-lg inputs" required></select>
		</div>

	    <div class="form-group">
	    	<label for="barcode">Barcode</label>
	        <input type="text" class="form-control input-lg inputs" id="barcode" name="barcode" autocomplete="off" />
	    </div>
	</form>
  </div>
</div>

<script type="text/javascript">

jQuery(document).ready(function($) {
	$('#barcode').val('').focus();

	$('input[name=position_scrap]').on('click', function() {
		$('#barcode').val('').focus();
	});

	getDefectCode()
		.done(function(data) {
			$('select[name=defectCode]').html("<option value=''>= กรุณาเลือกข้อมูล =</option>");
			$.each(data, function(index, val) {
				$('select[name=defectCode]').append('<option value="'+val.ID+'">'+val.ID+' - '+val.Description+'</option>');
			});
		});

	$('select[name=defectCode]').on('change', function() {
		$('#barcode').val('').focus();
	});

	$('#modal_alert').on('hidden.bs.modal', function() {
		$('#barcode').val('').focus();
	});
}); 

function postScrap() {

	$('#barcode').prop('readonly', true);
	var position_scrap = $('input[name=position_scrap]:checked').val();

	if(typeof position_scrap !== 'undefined') {
		gojax('post', base_url+'/api/scrap', {
			defectCode: $('#defectCode').val(),
			barcode: $('#barcode').val(),
			position_scrap: position_scrap
		})
		.done(function(data) {
			if (data.status === 200) {
				$('#modal_alert').modal('hide');
				$('#top_alert').show();
				$('#top_alert_message').text('Barcode ล่าสุด '+ $('#barcode').val());
				$('form#formScrap').trigger('reset');
			} else {
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text(data.message);
				$('#top_alert').hide();
				$('form#formScrap').trigger('reset');
			}

			$('#barcode').prop('readonly', false).val('').focus();
		});
	} else {
		$('#modal_alert').modal({backdrop: 'static'});
		$('#modal_alert_message').text("Please select side!");
		$('#top_alert').hide();
		$('form#formScrap').trigger('reset');
		$('#barcode').prop('readonly', false).val('').focus();
	}
	return false;
}

function getDefectCode() {
		return $.ajax({
			url : base_url + '/api/defect/all',
			type : 'get',
			dataType : 'json',
			cache : false
		});
	}
</script>