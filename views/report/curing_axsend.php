<?php $this->layout("layouts/base", ["title" => "Curing AX (Final Send)"]); ?>
<style>
.btn-xl {
    padding: 5px 22px;
    font-size: 20px;
    border-radius: 5px;
}

</style>

<div class="head-space"></div>



<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
		<div class="panel-heading">Curing AX (Final Send)</div>
  <div class="panel-body">
		<form  id="form_Curing_AX" action="<?php echo APP_ROOT; ?>/report/curing_axsend/pdf" method="post" target="_blank">
			<div class="form-group">
				<label for="date">Date</label>
				<input type="text" id="date_curing" name="date_curing" class=form-control required  placeholder="เลือกวันที่..." autocomplete="off" />
				<input type="text" id="check_type" name="check_type" hidden/>
			</div>
			<div class="form-group">
				<label for="shift">Shift</label>
				<select name="shift" id="shift" class="form-control" required>
				  <option value="day">กลางวัน</option>
				  <option value="night">กลางคืน</option>
				</select>
			</div>

	      	<div class="form-group">
	            <label for="BOI">BOI</label><br>
	            <select name="selectMenuBOI[]" id="selectMenuBOI" multiple="multiple"   style="width: 370px" >
	            </select>
	      	</div>

			<div class="form-group">
				<label class="radio-inline" style="padding-left: 10px;">
			  		<strong> Type : </strong>
				</label>
				<label class="radio-inline">
			  		<input type="radio" name="item_group" value="tbr" checked> <strong>TBR</strong>
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="item_group" value="pcr"> <strong>PCR</strong>
				</label>
			</div>
			<div class="form-group">
				<label class="radio-inline" style="padding-left: 10px;">
			  		<strong>Batch : </strong>
				</label>
				<label class="radio-inline">
			  		<input type="radio" name="batch" value="all" checked> <strong>ALL</strong>
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="batch" value="less2020"> <strong><2020</strong>
				</label>
				<label class="radio-inline">
				  	<input type="radio" name="batch" value="over2020"> <strong>>2020</strong>
				</label>
			</div>

			<!-- <button type="submit" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-print"></span> Print</button> -->
			<button type="button" class="btn btn-primary btn-xl " id="to_pdf" ><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>
	    <button type="button" class="btn btn-success btn-xl " id="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button>
		</form>
  </div>
</div>


<script>
	jQuery(document).ready(function($) {
		$( "#date_curing" ).datepicker({dateFormat: 'dd-mm-yy'});
    $('#selectMenuBOI').html("");
    $('#selectMenuBOI').multipleSelect({single: true});
    getPressSideBOI()
        .done(function(data) {
          $('#selectMenuBOI').append('<option value="1">ALL</option>');
          $.each(data, function(k, v) {
            $('#selectMenuBOI').append('<option value="'+ v.ID +'">'+v.ID+'</option>');
          });
            $('#selectMenuBOI').multipleSelect({single: true});
        });

		$('#to_pdf').on('click', function(event) {
			event.preventDefault();
			$('#to_excel').attr("disabled", true);
			setTimeout(function () {
			$('#to_excel').attr("disabled", false);
			}, 10000);
			$('input[name=check_type]').val(1);
			$('#form_Curing_AX').submit();

				});

				$('#to_excel').on('click', function(event) {
					event.preventDefault();
					$('#to_pdf').attr("disabled", true);
					setTimeout(function () {
					$('#to_pdf').attr("disabled", false);
					}, 10000);
					$('input[name=check_type]').val(2);
					$('#form_Curing_AX').submit();

						});
	});

  function getPressSideBOI() {
    return $.ajax({
      url : base_url + '/api/press/allBOI',
      type : 'get',
      dataType : 'json',
      cache : false
    });
  }
</script>
