<?php $this->layout("layouts/base", ['title' => 'Monthly Rate Deduction Report']); ?>
<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>
<h1 class="head-text">รายงานประวัติการหักค่าเลท รายเดือน</h1>
<hr>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
  <div class="panel-body">
    <form id="form_deduct" method="post" 
		action="<?php echo APP_ROOT; ?> /api/pdf/deduct"
		onsubmit="return form_deduct()" target="_blank" > 
		<div class="form-group">
			<label for="date">Month</label>
			<input type="text" id="month" name="month" class=form-control required  placeholder="เลือกเดือน" autocomplete="off" />
		</div>
		<div class="form-group">
            <label for="selectMac">Machine</label><br>
            <select name="selectMac[]" id="selectMac"  multiple="multiple" style="width: 150px">
            </select>
			<input type="button" name="grp_tbr" id="grp_tbr" class="btn btn-primary btn-sm" value="TBR" style="width:75px">
            <input type="button" name="grp_pcr" id="grp_pcr" class="btn btn-primary btn-sm" value="PCR" style="width:75px">
        </div>

        <div class="form-group">
			<div class="row">
				<div class="col-md-12">
					<label for="emp">Employee</label>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="input-group">
				      <input type="text" class="form-control" name="emp" id="emp" readonly>
				      <span class="input-group-btn">
				        <button class="btn btn-info" id="select_emp" type="button">
				        	<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
				        </button>
				      </span>
				    </div>
				</div>
			</div>
		</div>
        <input type="hidden" class="form-control" name="UserId" id="UserId" readonly>

		<button type="submit" id ="view" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>

	</form>
  </div>
</div>


<div class="modal" id="modal_select_emp" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Select Employee</h4>
      </div>
      <div class="modal-body">
        <div id="grid_emp"></div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

	jQuery(document).ready(function($) {
        
        $('#month').datepicker( {changeMonth: true,changeYear: true,
                                showButtonPanel: true,dateFormat: 'mm-yy',
                                onClose: function(dateText, inst) 
            { 
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            }
        });
       
		$('#selectMac').html("");

		$('#grp_tbr').prop('disabled',true);
		$('#grp_pcr').prop('disabled',false);

		getMachine_TBR()
		.done(function(data) {
			$.each(data, function(k, v) {
				$('#selectMac').append('<option value="'+ v.Machine +'">'+v.Machine+'</option>');
			});
			$('#selectMac').multipleSelect({single: true});
		});

		$('#grp_tbr').on('click', function() {
			$('#grp_tbr').prop('disabled',true);
			$('#grp_pcr').prop('disabled',false);
			$('#selectMac').html("");
			getMachine_TBR()
			.done(function(data) 
			{
				$.each(data, function(k, v) 
				{
					$('#selectMac').append('<option value="'+ v.Machine +'">'+v.Machine+'</option>');
				});
				$('#selectMac').multipleSelect({single: true});
			});
		});

		$('#grp_pcr').on('click', function() {
			$('#grp_tbr').prop('disabled',false);
			$('#grp_pcr').prop('disabled',true);
			$('#selectMac').html("");
			getMachine_PCR()
			.done(function(data) 
			{
				$.each(data, function(k, v) 
				{
					$('#selectMac').append('<option value="'+ v.Machine +'">'+v.Machine+'</option>');
				});
				$('#selectMac').multipleSelect({single: true});
			});
		});
		
        $('#select_emp').on('click', function() {
			$('#modal_select_emp').modal({backdrop:'static'});
			$('#grid_emp').jqxGrid('clearselection');
			bindGrid_Employee();
		});

		$('#grid_emp').on('rowdoubleclick', function() {
			var rowdata = row_selected('#grid_emp');
			$('input[name=emp]').val(rowdata.EmployeeID + " : " + rowdata.Name);
            $('input[name=UserId]').val(rowdata.ID);
			$('#modal_select_emp').modal('hide');
		});
		

		function getMachine_TBR() {
			return $.ajax({
				url : base_url + '/api/mac/Type_TBR',
				type : 'get',
				dataType : 'json',
				cache : false
			});
		}

		function getMachine_PCR() {
			return $.ajax({
				url : base_url + '/api/mac/Type_PCR',
				type : 'get',
				dataType : 'json',
				cache : false
			});
		}

        function bindGrid_Employee() 
		{
			
			var dataAdapter = new $.jqx.dataAdapter({
				datatype: 'json',
				datafields: [
				{ name: 'EmployeeID', type: 'string'},
				{ name: 'Name', type: 'string'},
				{ name: 'ID', type: 'int'}
				],
				url: base_url + "/rptdeduct/bindGridEmp"
			});

			return $("#grid_emp").jqxGrid({
				width: '100%',
				source: dataAdapter,
				autoheight: true,
				pageSize : 10,
				altrows : true,
				pageable : true,
				sortable: true,
				filterable : true,
				showfilterrow : true,
				columnsresize: true,
				columns: [
				{ text: 'รหัสพนักงาน', datafield: 'EmployeeID', width: 100},
				{ text: 'ชื่อ - นามสกุล', datafield: 'Name', width: 250}
				]
			});
		}

	});
</script>