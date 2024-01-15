<?php $this->layout("layouts/base", ['title' => 'Final Finishing-INS Report']); ?>

<style type="text/css">
	.btn-xl {
	    padding: 5px 22px;
	    font-size: 20px;
	    border-radius: 5px;
	}
</style>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
	<div class="panel-heading">Final Finishing-INS Report</div>
	<div class="panel-body">
		<form id="final_ins_params" method="post" action="<?php echo APP_ROOT; ?>/report/final_ins/pdf"  target="_blank">
			<div class="form-group">
				<label for="param_date">Date</label>
				<input type="text" id="param_date" name="param_date" class="form-control" autocomplete="off" required>
			</div>

			<div class="form-group">
				<label for="param_shift">Shift</label>
				<select name="param_shift" id="param_shift" class="form-control" required>
					<option value="1">กลางวัน</option>
					<option value="2">กลางคืน</option>
				</select>
			</div>

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
		$( "#param_date" ).datepicker({dateFormat: 'dd-mm-yy'});
		$('#selectMenuBOI').html("");
		$('#selectMenuBOI').multipleSelect({single: true});


		$('#to_pdf').on('click', function(event) {
	        event.preventDefault();
						var param_date = $('input[name=param_date]').val();
						var param_shift = $('select[name=param_shift]').val();
					//	var param_type = $('input[name=param_type]:checked').val();
						if(param_date =="" || param_shift == "")
						{
							alert("กรุณาใส่ข้อมูลให้ครบ");
							return false;
						}
	        $('#to_excel').attr("disabled", true);
	        setTimeout(function () {
	        $('#to_excel').attr("disabled", false);
	        }, 10000);
	        $('input[name=check_type]').val(1);
	        $('#final_ins_params').submit();

        });

        $('#to_excel').on('click', function(event) {
	        event.preventDefault();
					var param_date = $('input[name=param_date]').val();
					var param_shift = $('select[name=param_shift]').val();
				//	var param_type = $('input[name=param_type]:checked').val();
					if(param_date =="" || param_shift == "")
					{
						alert("กรุณาใส่ข้อมูลให้ครบ");
						return false;
					}
	        $('#to_pdf').attr("disabled", true);
	        setTimeout(function () {
	        $('#to_pdf').attr("disabled", false);
	        }, 10000);
	        $('input[name=check_type]').val(2);
	        $('#final_ins_params').submit();


        });

		// $('#daily_final_hold_params').submit(function(e) {
		// 	e.preventDefault();
		//
		// 	var param_date = $('input[name=param_date]').val();
		// 	var param_shift = $('select[name=param_shift]').val();
		// 	var param_type = $('input[name=param_type]:checked').val();
		//
		// 	var param_checktype = $('input[name=check_type]').val();
		//
		//
		// 	if ( typeof param_type === 'undefined' ) {
		// 		alert('please select type of tire!');
		// 		$('#to_pdf').attr("disabled", false);
		// 		$('#to_excel').attr("disabled", false);
		// 	} else {
		//
		// 		window.open('/report/daily_final_hold/' + param_date + '/' + param_shift + '/' + param_type + '/' + param_checktype + '/view', '_blank');
		//
		// 	}
		//
		// });

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
