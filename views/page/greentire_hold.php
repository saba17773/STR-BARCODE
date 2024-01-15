<?php $this->layout("layouts/base", ['title' => 'Hold']); ?>
<form id="holdSearch" class="form-center" onsubmit="return holdSearch()">
	<h1>Hold</h1>
	<hr>
	<div class="form-group">
		<input type="text" class="form-control" name="barcode">
		<input type="hidden" class="form-control" name="tostatus">
		<input type="hidden" class="form-control" name="disposalid">
	</div>
	<button class="btn btn-primary btn-block">Click</button>
	<h3 id="txtSearchBox" style="display:none;"><span id="txtSearch" ></span></h3>
	<div id="search_result" style="padding:30px;">
		
	</div>
</form>

<script type="text/javascript">
	
function holdSearch() {
	var hold = $('input[name="barcode"]').val();
	$('input[name="tostatus"]').val(5);
	$('input[name="disposalid"]').val(10);
	$('#txtSearchBox').show();
	$('#txtSearch').text('BarcodePrinting : ' + hold);

	search_bc(hold)
		.done(function (data) {
			if (data.status == 404) {

				$('#search_result').show().html(data.message);
        
			} else {
				
				$('#search_result').hide();

			}
			
		})
		.fail(function () {
			gotify('error', 'danger');
		});

	return false;
}

function search_bc(hold) {
	return $.ajax({
		url : './api/greentirehold',
		type : 'post',
		cache : false,
		dataType : 'json',
		data : $('#holdSearch').serialize()
	});
}

</script>