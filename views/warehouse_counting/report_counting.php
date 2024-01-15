<?php $this->layout("layouts/base", ["title" => "Stock Counting Report"]); ?>

<div class="head-space"></div>

<h1 class="text-center" style="margin-bottom: 40px;">Counting Report</h1>

<form action="/warehouse_counting/report_counting_export" method="post" style="margin: 0 auto; max-width: 300px;">
  <div class="row">
    <div class="col-xs-6">
      <div class="form-group">
        <label for="">From Date</label>
        <input type="text" name="counting_from_date" class="form-control" />
      </div>
    </div>
    <div class="col-xs-6">
      <div class="form-group">
        <label for="">To Date</label>
        <input type="text" name="counting_to_date" class="form-control" />
      </div>
    </div>
  </div>
  <div class="form-group">
				<label for="brand">Brand</label>
				<br>
				<select name="selectbrand[]" id="selectbrand" multiple="multiple" style="width: 300px">
				</select>
	</div>
  <div class="form-group">
				<label class="radio-inline" style="padding-left: 10px;">
					<strong> Type : </strong>
				</label>
				<label class="radio-inline">
					<input type="radio" name="item_group" value="summary" checked> <strong>Summary</strong>
				</label>
				<label class="radio-inline">
					<input type="radio" name="item_group" value="detail"> <strong>Detail</strong>
				</label>
			</div>  

  <div class="form-group text-center">
    <button type="submit1" name="type_report" value="pdf" id="to_pdf" class="btn btn-danger btn-lg">View as PDF</button>
    <button type="submit" name="type_report" value="excel" id="to_excel" class="btn btn-success btn-lg">Export to Excel</button>
  </div>
</form>

<script>
  $(document).ready(function() {
    $("input[name=counting_from_date]").datepicker({
      dateFormat: 'yy-mm-dd'
    });

    $("input[name=counting_to_date]").datepicker({
      dateFormat: 'yy-mm-dd'
    });
  });
</script>

<script type="text/javascript">
	getBrand()
		.done(function(data) {
			$.each(data, function(k, v) {
				$('#selectbrand').append('<option value="' + v.BrandID + '">' + v.BrandName + '</option>');
			});
			$('#selectbrand').multipleSelect();
		});

	$('input[type=radio][name=item_group]').change(function() {
    if (this.value == 'summary') {
		$('#to_pdf').attr("disabled", false);
		$('#to_excel').attr("disabled", false);
		
    }else{
		$('#to_pdf').attr("disabled", true);
		$('#to_excel').attr("disabled", false);
	}
	});
	
	function getBrand() {
		return $.ajax({
			url: base_url + '/api/brand/allbrand',
			type: 'get',
			dataType: 'json',
			cache: false
		});
	}

</script>
