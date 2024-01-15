<?php $this->layout("layouts/base", ['title' => 'Repair Inventory']); ?>

<style type="text/css">
	.btn-xl {
	    padding: 5px 22px;
	    font-size: 20px;
	    border-radius: 5px;
	}
</style>

<div class="head-space"></div>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
	<div class="panel-heading">Repair Inventory Report</div>
	<div class="panel-body">
		<form id="final_ins_params" method="post" action="<?php echo APP_ROOT; ?>/api/pdf/repairinventory"  target="_blank">

			<div class="form-group">
              <label for="BOI">BOI</label><br>
              <select name="selectMenuBOI[]" id="selectMenuBOI" multiple="multiple" value=""   style="width: 370px" required  >
              </select>
			</div>

			<div class="form-group">
				<b style="padding-right: 30px;">Type : </b>
				<label for="param_type" style="padding-right: 40px;">
					<input type="radio" name="param_type" value="tbr" checked>
					TBR
				</label>
				<label for="param_type" style="padding-right: 40px;">
					<input type="radio" name="param_type" value="pcr">
					PCR
				</label>
			</div>
			<input type="text" id="check_type" name="check_type" hidden/>
			<button type="button" class="btn btn-primary btn-xl " id="to_pdf" ><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>
    		<button type="button" class="btn btn-success btn-xl " id="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button>
		</form>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#selectMenuBOI').html("");
		$('#selectMenuBOI').multipleSelect({single: true});


		$('#to_pdf').on('click', function(event) {
	        event.preventDefault();

	        $('#to_excel').attr("disabled", true);
	        setTimeout(function () {
	        $('#to_excel').attr("disabled", false);
	        }, 10000);
	        $('input[name=check_type]').val(1);
	        $('#final_ins_params').submit();

        });

        $('#to_excel').on('click', function(event) {
	        event.preventDefault();

	        $('#to_pdf').attr("disabled", true);
	        setTimeout(function () {
	        $('#to_pdf').attr("disabled", false);
	        }, 10000);
	        $('input[name=check_type]').val(2);
	        $('#final_ins_params').submit();


        });

	  getPressSideBOI()
	      .done(function(data) {
					$('#selectMenuBOI').append('<option value="1">ALL</option>');
	        $.each(data, function(k, v) {
	          $('#selectMenuBOI').append('<option value="'+ v.ID +'">'+v.ID+'</option>');
	        });
	          $('#selectMenuBOI').multipleSelect({single: true});
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