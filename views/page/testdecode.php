<?php $this->layout("layouts/base"); ?>
<form id="repairSearch" class="form-center" onsubmit="return repairSearch()">
	<h1>decode</h1>
	<hr>
	<div class="form-group">
		<input type="text" class="form-control" name="search">
	</div>
	<button class="btn btn-primary btn-block">Click</button>
	<h3 id="txtSearchBox" style="display:none;"><span id="txtSearch" ></span></h3>
	<div id="search_result" style="padding:30px;">
		
	</div>
</form>

<script type="text/javascript">
$('input[name="search"]').focus();
function repairSearch() {
	var repair = $('input[name="search"]').val();
	$('#txtSearchBox').show();
	$('#txtSearch').text('BarcodePrinting : ' + repair);

	search_rp(repair)
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

function search_rp(repair) {
	return $.ajax({
		url : './api/search/repair',
		type : 'post',
		cache : false,
		dataType : 'json',
		data : $('#repairSearch').serialize()
	});
}

</script>