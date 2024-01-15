<?php $this->layout("layouts/base", ['title' => 'Deducted Rate by User']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<hr>
<div class="panel panel-default" id="panel_search" style="max-width: 400px; margin: auto;" >
	<div class="panel-heading">ค้นหาพนักงาน</div>
	<div class="panel-body">
		<!-- <form id="formSearch"> -->
		<div class="form-group">
			<label for="date">Date</label>
			<input type="text" id="date" name="date" class=form-control required  placeholder="เลือกวันที่..." autocomplete="off" />
		</div>
		<div class="form-group">
			<label for="shift">Shift</label>
			<select name="shift" id="shift" class="form-control" required>
			<option value="day">กลางวัน</option>
			<option value="night">กลางคืน</option>
			</select>
		</div>
		<div class="form-group">
			<label>เครื่อง</label><br>
			<!-- <select name="machine" id="machine" class="form-control" ></select> -->
            <select name="selectMenu[]" id="selectMenu"  multiple="multiple" style="width: 150px">
            </select>
			<input type="button" name="grp_tbr" id="grp_tbr" class="btn btn-primary btn-sm" value="TBR" style="width:75px">
            <input type="button" name="grp_pcr" id="grp_pcr" class="btn btn-primary btn-sm" value="PCR" style="width:75px">
		</div>
		<!-- <div class="form-group">
			<label>พนักงาน</label><br>
			<select name="user" id="user" class="form-control" ></select>
		</div> -->

		<button type="submit" id="btnSearch" class="btn btn-block btn-lg btn-primary" style="max-width: 400px; margin: auto;">
			<span class="glyphicon glyphicon-log-in"></span>
			ยืนยัน
		</button>	
		<!-- </form> -->
	</div>
</div>

<div class="panel panel-default" id="panel_manage">
  <div class="panel-heading">
 	วันที่: <span id="DateHead" > </span>  กะการทำงาน: <span id="ShiftHead" > </span>  เครื่อง: <span id="MachineHead" > </span> 
            
  </div>
    <div class="panel-body">
        <div class="btn-panel">
            <button class="btn btn-danger"" id="Edit"> หักค่าเลท </button>
			&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
            &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
			&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
			&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
			&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; 
			<button class="btn btn-info" id="Detail"> ดูประวัติการโดนหักค่าเลท </button>
            <button class="btn btn-warning" id="Back"> เปลี่ยนเงื่อนไข </button>
            
        </div>
        <div id="grid_user"></div>
    </div>
</div>

<div class="modal" id="modal_add" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
	  	<div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">เพิ่มค่าความเสียหาย</h4>
        </div>
		<div class="modal-body">
        	<form id="form_add">
            	<div class="form-group">
					<label for="charge">จำนวนเงินที่หัก</label>
					<input type="text" class="form-control input-lg" name="charge" id="charge"
					autocomplete="off" placeholder="เงินที่หัก (บาท)" autofocus>
            	</div>
				<div class="form-group">
					<label for="remark">หมายเหตุ</label>
					<input type="text" class="form-control input-lg" name="remark" id="remark"
					autocomplete="off" placeholder="สาเหตุุที่หัก">
            	</div>
				<input type="hidden" class="form-control input-lg" name="UserId" id="UserId">
				<input type="hidden" class="form-control input-lg" name="Id" id="Id">
				<input type="hidden" class="form-control input-lg" name="shift2" id="shift2">
				<input type="hidden" class="form-control input-lg" name="buildtypeid" id="buildtypeid">
				
              	<button type="submit" id="btnAddCharge" class="btn btn-block btn-lg btn-primary">
					<span class="glyphicon glyphicon-log-in"></span>
					ยืนยัน
            	</button>
          	</form>
        </div>
        
      </div>
    </div>
</div>

<div class="modal" id="modal_detail" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
	  	<div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">ประวัติการโดนหักเงิน</h4>
        </div>
		<div class="modal-body">	
			<div id="grid_deduct"></div>
        </div>
        
      </div>
    </div>
</div>

  
<script type="text/javascript">
    jQuery(document).ready(function($) {
		$( "#date" ).datepicker({dateFormat: 'dd-mm-yy'});
		$('#panel_search').show();
        $("#panel_manage").hide();
		setInt('#charge');
        
        $('#grp_tbr').prop('disabled',true);
		$('#grp_pcr').prop('disabled',false);

		getMachineTBR()
		.done(function(data) {
			$.each(data, function(k, v) {
				$('#selectMenu').append('<option value="'+ v.ID +'">'+v.ID+'</option>');
			});
			$('#selectMenu').multipleSelect({single: true});
		});

		$('#grp_tbr').on('click', function() {
			$('#grp_tbr').prop('disabled',true);
			$('#grp_pcr').prop('disabled',false);
			$('#selectMenu').html("");
			getMachineTBR()
			.done(function(data) 
			{
				$.each(data, function(k, v) 
				{
					$('#selectMenu').append('<option value="'+ v.ID +'">'+v.ID+'</option>');
				});
				$('#selectMenu').multipleSelect({single: true});
			});
		});

		$('#grp_pcr').on('click', function() {
			$('#grp_tbr').prop('disabled',false);
			$('#grp_pcr').prop('disabled',true);
			$('#selectMenu').html("");
			getMachinePCR()
			.done(function(data) 
			{
				$.each(data, function(k, v) 
				{
					$('#selectMenu').append('<option value="'+ v.ID +'">'+v.ID+'</option>');
				});
				$('#selectMenu').multipleSelect({single: true});
			});
		});

		$('#btnSearch').on('click', function()
        {
			event.preventDefault();
			date = $('#date').val();
			shift = $('select[name=shift]').val();
			machine = $('#selectMenu').val();
			if (shift =='day'){
				shift_th = 'กลางวัน';
			}else{ shift_th = 'กลางคืน'; }

			if (date !== '' && shift !== '' && machine !== '')
			{
				$('#panel_search').hide();
				$("#panel_manage").show();
				bindGrid();
				$('#DateHead').text(date);
				$('#ShiftHead').text(shift_th);
				$('#MachineHead').text(machine);
				
			}
			else 
			{
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text("กรุณากรอกข้อมูลให้ครบ");

				$('#panel_search').show();
				$("#panel_manage").hide();
			}

        });

		$('#form_add').on('submit', function(event) 
		{
			$('#modal_add').modal('hide');
			event.preventDefault();

			date = $('#date').val();
			s = $('select[name=shift]').val();
			machine = $('#selectMenu').val();

			
			gojax_f('post', base_url+'/api/deduct/checkLog/'+date+'/'+machine , '#form_add')
			.done(function(chk) 
			{
				if(chk.status === 200)
				{
					gojax_f('post', base_url+'/api/deduct/insertDeduct/'+ date +'/'+ machine , '#form_add')
					.done(function(data) 
					{
						if (data.status == 404) 
						{
							$('#modal_alert').modal({backdrop: 'static'});
							$('#modal_alert_message').text(data.message);
						} 
						else 
						{
							$('#grid_user').jqxGrid('updatebounddata');
						}
					});
				}
				else
				{
					gojax_f('post', base_url+'/api/deduct/updateDeduct', '#form_add')
					.done(function(data) 
					{
						if (data.status == 404) 
						{
							$('#modal_alert').modal({backdrop: 'static'});
							$('#modal_alert_message').text(data.message);
						} 
						else 
						{
							$('#grid_user').jqxGrid('updatebounddata');
						}
					});
				}
			});
	
		});

		$('#Edit').on('click',function()
		{
			var rowdata = row_selected('#grid_user');
			if (typeof rowdata !== 'undefined') 
			{
				$('#modal_add').modal({backdrop: 'static'});
				$("#form_add")[0].reset();
				$('#charge').val(rowdata.Charge);
				$('#remark').val(rowdata.Remark);
				$('#UserId').val(rowdata.CreateBy);
				$('#Id').val(rowdata.Id);
				$('#shift2').val(rowdata.Shift);
				$('#buildtypeid').val(rowdata.BuildTypeId);
				
			} 
			else 
			{
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text('กรุณาเลือกข้อมูล');
			}
		});

		$('#Detail').on('click',function()
		{
			event.preventDefault();
			var rowdata = row_selected('#grid_user');
			if (typeof rowdata !== 'undefined') 
			{
				$('#modal_detail').modal({backdrop: 'static'});
				userId = rowdata.CreateBy;
				bindGridDeduct();
				
			} 
			else 
			{
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text('กรุณาเลือกข้อมูล');
			}
		});

		$('#Back').on('click',function()
		{
			$('#panel_search').show();
        	$("#panel_manage").hide();
			$('#date').val('');
			$('select[name=shift]').val('day');

			$('#selectMenu').empty();
			$('#grp_tbr').prop('disabled',true);
			$('#grp_pcr').prop('disabled',false);
			getMachineTBR()
			.done(function(data) {
				$.each(data, function(k, v) {
					$('#selectMenu').append('<option value="'+ v.ID +'">'+v.ID+'</option>');
				});
				$('#selectMenu').multipleSelect({single: true});
			});

		});
		//alert($('#date').val()+' '+$('select[name=shift]').val()+' '+ $('select[name=machine]').val());

        function getMachineTBR() 
		{
			return $.ajax({
				url : base_url + '/api/deduct/machineTBR',
				type : 'get',
				dataType : 'json',
				cache : false
			});
		}

		function getMachinePCR() 
		{
			return $.ajax({
				url : base_url + '/api/deduct/machinePCR',
				type : 'get',
				dataType : 'json',
				cache : false
			});
		}

		function bindGrid() 
		{
			date = $('#date').val();
			shift = $('select[name=shift]').val();
			machine = $('#selectMenu').val();

			var dataAdapter = new $.jqx.dataAdapter({
				datatype: 'json',
				datafields: [
				{ name: 'CreateBy', type: 'int'},
				{ name: 'EmployeeID', type: 'int'},
				{ name: 'Name', type: 'string'},
				{ name: 'Total', type: 'string'},
				{ name: 'Charge', type: 'int' },
				{ name: 'Remark', type: 'string'},
				{ name: 'Id', type: 'int'},
				{ name: 'Shift', type: 'int'},
				{ name: 'BuildType', type: 'string'},
				{ name: 'BuildTypeId', type: 'string'}
				],
				url: base_url + "/api/deduct/bindGrid/"+ date +'/'+ shift +'/'+ machine
			});
			return $("#grid_user").jqxGrid({
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
				{ text: 'ชื่อพนักงาน', datafield: 'Name', width: 200},
				{ text: 'ตำแหน่ง', datafield: 'BuildType', width: 120},
				{ text: 'ค่าเลทที่ได้', datafield: 'Total', width: 100},
				{ text: 'จำนวนเงินที่โดนหัก', datafield: 'Charge', width: 120},
				{ text: 'หมายเหตุ', datafield: 'Remark', width: 250}
				// ,{ text: 'หมายเหตุ', datafield: 'Shift', width: 50}
				]
			});
		}

		function bindGridDeduct()
		{
			var dataAdapter = new $.jqx.dataAdapter({
				datatype: 'json',
				datafields: [
					{ name: 'DeductDate', type: 'string'},
					{ name: 'Machine', type: 'string'},
					{ name: 'Shift', type: 'string'},
					{ name: 'Charge', type: 'int' },
					{ name: 'Remark', type: 'string'}
				],
				url: base_url + "/api/deduct/bindGridDeduct/"+ userId
			});
			return $("#grid_deduct").jqxGrid({
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
					{ text: 'วันที่โดนหัก', datafield: 'DeductDate', width: 130},
					{ text: 'กะ', datafield: 'Shift', width: 50},
					{ text: 'เครื่อง', datafield: 'Machine', width: 80},
					{ text: 'เงิน(บาท)', datafield: 'Charge', width: 80},
					{ text: 'หมายเหตุ', datafield: 'Remark', width: 200}
				]
			});
		}


    });
    
</script>

