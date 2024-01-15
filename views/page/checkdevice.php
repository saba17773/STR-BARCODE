<?php $this->layout("layouts/base", ['title' => 'Home']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<div class="head-space"></div>
<div class="panel panel-default">
	<div class="panel-heading">Home</div>
	<div class="panel-body">

		<div class="btn-panel">


			<button id="btn_show_trans" onclick="return show_trans()" class="btn btn-primary" data-backdrop="static" data-toggle="modal" data-target="#modal_trans" disabled>
				<span class="glyphicon glyphicon-th-list"></span>
				Line
			</button>


			<button class="btn btn-success" id="Create_data">
				<span class="glyphicon glyphicon-plus"></span>
				Create
			</button>
			<button class="btn btn-warning" id="Update_data">
				<span class="glyphicon glyphicon-pencil"></span>
				Update
			</button>
			<button class="btn btn-info" id="Update_repair">
				<span class="glyphicon glyphicon-wrench"></span>
				Repair/Receive
			</button>


		</div>

		<div id="grid_table"></div>
	</div>
</div>

<!-- Modal -->
<div class="modal" id="modal_trans" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" style="width: 90%;" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="row">
					<div class="col-xs-2">
						Device Name: <input type="text" id="Device_name" name="Device_name" class="form-control" readonly>
					</div>
					<div class="col-xs-2">
						Serial Number : <input type="text" id="SN_number" name="SN_number" class="form-control" readonly>
					</div>

				</div>
				<!-- <h4 class="modal-title"></h4> -->
			</div>
			<div class="modal-body">
				<!-- <button class="btn btn-success" id="New_row_tr">New</button>
				<button class="btn btn-primary" id="Update_row_tr">Update</button>
				<button class="btn btn-danger" id="Delete_row_tr">Delete</button> -->

				<!-- Content -->
				<div id="grid_trans"></div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Create-->
<div class="modal" id="modal_create" tabindex="-1" role="dialog">
	<div class="modal-dialog" style="width: 60%;" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Create</h4>
			</div>
			<div class="modal-body">
				<!-- Content -->
				<form id="formAddDevice">
					<table class="table">
						<tr>
							<td>
								<div class="form-group">
									<label for=""> Device </label>
									<div class="input-group">
										<input type="text" id="add_Device" name="add_Device" class="form-control required = " required" onkeypress="return false;" autocomplete="off"" >
										 <span class=" input-group-btn">
										<button class="btn btn-info" id="press_Device" type="button">
											<span class="glyphicon glyphicon-search"></span>
										</button>
										<input type="hidden" id="add_DeviceID" name="add_DeviceID" class="form-control">
										</span>
									</div>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for=""> S/N</label>
									<input type="text" id="add_SN" name="add_SN" class="form-control" autocomplete="off">
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for=""> Mac Address</label>
									<input type="text" id="add_MacAddr" name="add_MacAddr" class="form-control" autocomplete="off">
								</div>
							</td>
						</tr>
						<tr>

							<td>
								<div class="form-group">
									<label for=""> IP Address</label>
									<input type="text" id="add_IpAddr" name="add_IpAddr" class="form-control" autocomplete="off">
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for=""> PO</label>
									<input type="text" id="add_PO" name="add_PO" class="form-control" autocomplete="off">
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for=""> Vendor</label>
									<div class="input-group">
										<input type="text" id="add_Vendor" name="add_Vendor" class="form-control required = " required" onkeypress="return false;" autocomplete="off"" >
        				      <span class=" input-group-btn">
										<button class="btn btn-info" id="press_vendor" type="button">
											<span class="glyphicon glyphicon-search"></span>
										</button>
										<input type="hidden" id="add_VendorID" name="add_VendorID" class="form-control">
										</span>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label for="">Receive By</label>
									<div class="input-group">
										<input type="text" id="add_ReceiveBy" name="add_ReceiveBy" class="form-control required = " required" onkeypress="return false;" autocomplete="off"" >
										 <span class=" input-group-btn">
										<button class="btn btn-info" id="press_ReceviveBy" type="button">
											<span class="glyphicon glyphicon-search"></span>
										</button>
										<input type="hidden" id="add_ReceiveByID" name="add_ReceiveByID" class="form-control">
										</span>
									</div>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for=""> Receive Date</label>

									<input type="text" id="add_ReceiveDate" name="add_ReceiveDate" class="form-control" autocomplete="off">
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for=""> Start Warranty Date</label>
									<input type="text" id="add_startWarrantyDate" name="add_startWarrantyDate" class="form-control" autocomplete="off">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label for=""> End Warranty Date</label>
									<input type="text" id="add_EndWarrantyDate" name="add_EndWarrantyDate" class="form-control" autocomplete="off">
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for=""> Fixed Asset No</label>
									<input type="text" id="add_FixedAssetNo" name="add_FixedAssetNo" class="form-control" autocomplete="off">
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for=""> Remark</label>
									<input type="text" id="add_Remark" name="add_Remark" class="form-control" autocomplete="off">
									<input type="hidden" id="check_datainsert" name="check_datainsert" value="" class="form-control">
									<input type="hidden" id="add_IDupdate" name="add_IDupdate" value="" class="form-control">
								</div>
							</td>
						</tr>


					</table>
					<button class="btn btn-primary" id="save_Devi" type="submit">Save</button>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- Modal vendor -->
<div class="modal" id="modal_vendor" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Vendor</h4>
			</div>
			<div class="modal-body">
				<form id="form_create_batch">
					<div class="form-group">
						<div id="grid_Vendor"></div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>

<!-- Modal DeviceTYpe -->
<div class="modal" id="modal_Device" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Device Type</h4>
			</div>
			<div class="modal-body">
				<form id="form_create_batch">
					<div class="form-group">
						<div id="grid_Device"></div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>

<!-- Modal DeviceTYpe -->
<div class="modal" id="modal_user" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Receive BY</h4>
			</div>
			<div class="modal-body">
				<form id="form_create_batch">
					<div class="form-group">
						<div id="grid_User"></div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>

<!-- Modal createTrans-->
<div class="modal" id="modal_create_trans" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<!-- <h4 class="modal-title">Create</h4> -->
			</div>
			<div class="modal-body">
				<!-- Content -->
				<form id="formAddDeviceTrans">
					<table class="table">
						<tr>
							<td>
								<div class="form-group">
									<label for=""> Mac Address</label>
									<input type="text" id="add_MacAddr_trans" name="add_MacAddr_trans" class="form-control" required autocomplete="off">
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for=""> SN</label>
									<input type="text" id="add_SN_trans" name="add_SN_trans" class="form-control" required autocomplete="off">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label for=""> Receive user Date</label>
									<input type="text" id="add_ReceiveDate_trans" name="add_ReceiveDate_trans" class="form-control" required autocomplete="off">
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for=""> Send SVO Date</label>
									<input type="text" id="add_SVODate_trans" name="add_SVODate_trans" class="form-control" required autocomplete="off">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label for="">Return Date</label>
									<input type="text" id="add_ReturnDate_trans" name="add_ReturnDate_trans" class="form-control" required autocomplete="off">
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for=""> Send User Date</label>
									<input type="text" id="add_SendUserdate_trans" name="add_SendUserdate_trans" class="form-control" required autocomplete="off">
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-group">
									<label for=""> Detail</label>
									<input type="text" id="add_Detail_trans" name="add_Detail_trans" class="form-control" required autocomplete="off">
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for=""> Remark</label>
									<input type="text" id="add_Remark_trans" name="add_Remark_trans" class="form-control">
									<!-- <input type="text" id="check_datainsert_trans" name="check_datainsert_trans" value="" class="form-control" > -->
									<input type="hidden" id="add_IDupdate_trans" name="add_IDupdate_trans" value="" class="form-control">
								</div>
							</td>
						</tr>
					</table>
					<button class="btn btn-primary" id="save_DeviTrans" type="submit">Save</button>
				</form>
			</div>
		</div>
	</div>
</div>



<script>
	jQuery(document).ready(function($) {
		$("#add_startWarrantyDate").datepicker({
			dateFormat: 'dd-mm-yy'
		});
		$("#add_ReceiveDate").datepicker({
			dateFormat: 'dd-mm-yy'
		});
		$("#add_EndWarrantyDate").datepicker({
			dateFormat: 'dd-mm-yy'
		});
		$("#add_ReceiveDate_trans").datepicker({
			dateFormat: 'dd-mm-yy'
		});
		$("#add_ReturnDate_trans").datepicker({
			dateFormat: 'dd-mm-yy'
		});
		$("#add_SendUserdate_trans").datepicker({
			dateFormat: 'dd-mm-yy'
		});
		$("#add_SVODate_trans").datepicker({
			dateFormat: 'dd-mm-yy'
		});
		grid_table();

		$('#grid_table').on('rowclick', function(event) {
			$('#btn_show_trans').prop('disabled', false);
			$('#print').prop('disabled', false);
		});

		$('#Update_data').on('click', function(event) {
			event.preventDefault();
			var rowdata = row_selected('#grid_table');
			//console.log(dayjs(rowdata.StartWarranty).format('DD/MM/YYYY'));


			if (typeof rowdata !== 'undefined') {
				$('input[name=add_DeviceID]').val(rowdata.Devicetype);
				$('input[name=add_Device]').val(rowdata.NameDevice);
				$('input[name=add_SN]').val(rowdata.SN);
				$('input[name=add_MacAddr]').val(rowdata.MacAddress);
				$('input[name=add_IpAddr]').val(rowdata.IPAdress);
				$('input[name=add_PO]').val(rowdata.PO);
				$('input[name=add_VendorID]').val(rowdata.SN);
				$('input[name=add_Vendor]').val(rowdata.Name);
				$('input[name=add_ReceiveByID]').val(rowdata.Devicetype);
				$('input[name=add_ReceiveBy]').val(rowdata.NameReceive);
				$('input[name=add_ReceiveDate]').val(dayjs(rowdata.ReceiveDate).format('DD-MM-YYYY'));
				//$('input[name=add_startWarrantyDate]').val(rowdata.StartWarranty);
				$('input[name=add_startWarrantyDate]').val(dayjs(rowdata.StartWarranty).format('DD-MM-YYYY'));
				$('input[name=add_EndWarrantyDate]').val(dayjs(rowdata.EndWarranty).format('DD-MM-YYYY'));
				$('input[name=add_FixedAssetNo]').val(rowdata.FixedAssetNo);
				$('input[name=add_Remark]').val(rowdata.Remark);
				$('input[name=check_datainsert]').val(1);
				$('input[name=add_IDupdate]').val(rowdata.ID);
				$('#modal_create').modal({
					backdrop: 'static'
				});
				$('.modal-title').text('update');

			} else {
				alert('กรุณาเลืกรุณาเลือกรายการ');
			}

		});

		$('#Update_repair').on('click', function(event) {
			event.preventDefault();
			var rowdata = row_selected('#grid_table');
			if (typeof rowdata !== 'undefined') {
				$('#modal_create_trans').modal({
					backdrop: 'static'
				});
				$("#formAddDeviceTrans")[0].reset();
				$('input[name=add_IDupdate_trans]').val(rowdata.ID);
				$('input[name=add_MacAddr_trans]').val(rowdata.MacAddress);
				$('input[name=add_SN_trans]').val(rowdata.SN);
			} else {
				alert('กรุณาเลืกรุณาเลือกรายการ');
			}

		});

		$('#Create_data').on('click', function(event) {
			event.preventDefault();
			$('#modal_create').modal({
				backdrop: 'static'
			});
			$('.modal-title').text('Create');
			$('input[name=check_datainsert]').val(0);
			$("#formAddDevice")[0].reset();
		});

		// $('#New_row_tr').on('click', function(event) {
		// 	event.preventDefault();
		// 	$('#modal_create_trans').modal({backdrop: 'static'});
		// 	// $('.modal-title').text('Create');
		// 	$('input[name=check_datainsert_trans]').val(0);
		// 	$("#formAddDeviceTrans")[0].reset();
		// });

		// $('#Update_row_tr').on('click', function(event) {
		// 	event.preventDefault();
		// 	$('#modal_create_trans').modal({backdrop: 'static'});
		// 	var rowdata = row_selected('#grid_trans');
		//
		//
		// 	if(typeof rowdata !== 'undefined') {
		// 		$('input[name=add_MacAddr_trans]').val(rowdata.Mac);
		// 		$('input[name=add_SN_trans]').val(rowdata.SN);
		// 		$('input[name=add_ReceiveDate_trans]').val(dayjs(rowdata.ReceiveUserDate).format('DD/MM/YYYY'));
		// 		$('input[name=add_ReturnDate_trans]').val(dayjs(rowdata.ReturnDate).format('DD/MM/YYYY'));
		// 		$('input[name=add_SendUserdate_trans]').val(dayjs(rowdata.SendUserDate).format('DD/MM/YYYY'));
		// 		$('input[name=add_Detail_trans]').val(rowdata.Detail);
		// 		$('input[name=add_IDupdate_trans]').val(rowdata.ID);
		// 		$('input[name=add_SVODate_trans]').val(dayjs(rowdata.SendSVODate).format('DD/MM/YYYY'));
		// 		$('input[name=add_Remark_trans]').val(rowdata.Remark);
		// 		$('input[name=check_datainsert_trans]').val(1);
		// 		alert(rowdata.ID);
		// 	}
		// 	else {
		// 		alert('กรุณาเลืกรุณาเลือกรายการ');
		// 	}
		// 	// $('.modal-title').text('Create');
		//
		// });

		// $('#Delete_row_tr').on('click', function(event) {
		// 	event.preventDefault();
		// 	//$('#modal_create_trans').modal({backdrop: 'static'});
		// 	var rowdata = row_selected('#grid_trans');
		//
		//
		// 	if(typeof rowdata !== 'undefined') {
		// 		$('input[name=add_IDupdate_trans]').val(rowdata.ID);
		// 		$('input[name=check_datainsert_trans]').val(2);
		// 		gojax_f('post', base_url+'/api/Device/table/saveDeviceTabletrans', '#formAddDeviceTrans')
		// 				.done(function(data) {
		// 					if (data.status === 200) {
		// 						alert(data.message);
		// 						 $('#grid_trans').jqxGrid('updatebounddata');
		// 						 	$('#modal_create_trans').modal('hide');
		// 			} else {
		// 							alert(data.message);
		// 			}
		// 		});
		// 	}
		// 	else {
		// 		alert('กรุณาเลืกรุณาเลือกรายการ');
		// 	}
		// 	// $('.modal-title').text('Create');
		//
		// });

		$('#press_vendor').on('click', function() {
			$('#modal_vendor').modal({
				backdrop: 'static'
			});
			grid_Vendor();
		});

		$('#press_Device').on('click', function() {
			$('#modal_Device').modal({
				backdrop: 'static'
			});
			grid_Device();
		});

		$('#press_ReceviveBy').on('click', function() {
			$('#modal_user').modal({
				backdrop: 'static'
			});
			grid_User();
		});

		$('#grid_Vendor').on('dblclick', function() {
			var rowdata = row_selected('#grid_Vendor');
			if (typeof rowdata !== 'undefined') {
				$('input[name=add_Vendor]').val(rowdata.Name);
				$('input[name=add_VendorID]').val(rowdata.VendorID)
				$('#modal_vendor').modal('hide');
			}
		});

		$('#grid_Device').on('dblclick', function() {
			var rowdata = row_selected('#grid_Device');
			if (typeof rowdata !== 'undefined') {
				$('input[name=add_Device]').val(rowdata.Name);
				$('input[name=add_DeviceID]').val(rowdata.ID)
				$('#modal_Device').modal('hide');
			}
		});

		$('#grid_User').on('dblclick', function() {
			var rowdata = row_selected('#grid_User');
			if (typeof rowdata !== 'undefined') {
				$('input[name=add_ReceiveBy]').val(rowdata.Name);
				$('input[name=add_ReceiveByID]').val(rowdata.ID)
				$('#modal_user').modal('hide');
			}
		});

		$('#formAddDevice').on('submit', function(event) {
			event.preventDefault();
			if ($('input[name=add_SN]').val() == "" ||
				$('input[name=add_MacAddr]').val() == '' ||
				$('input[name=add_IpAddr]').val() == '') {
				if ($('input[name=add_SN]').val() == "") {
					alert('กรุณาใส่ S/N');
					$('input[name=add_SN]').val('').focus();
					return false;
				}
				if ($('input[name=add_MacAddr]').val() == "") {
					alert('กรุณาใส่ MacAddress');
					$('input[name=add_MacAddr]').val('').focus();
					return false;
				}
				if ($('input[name=add_IpAddr]').val() == "") {
					alert('กรุณาใส่ IPAddress');
					$('input[name=add_IpAddr]').val('').focus();
					return false;
				}
			} else {
				gojax_f('post', base_url + '/api/Device/table/saveDeviceTable', '#formAddDevice')
					.done(function(data) {
						if (data.status === 200) {
							alert(data.message);
							$('#modal_create').modal('hide');
							$('#grid_table').jqxGrid('updatebounddata');

						} else {
							alert(data.message);
						}
					});
			}



		});

		$('#formAddDeviceTrans').on('submit', function(event) {
			event.preventDefault();
			gojax_f('post', base_url + '/api/Device/table/saveDeviceTabletrans', '#formAddDeviceTrans')
				.done(function(data) {
					if (data.status === 200) {
						alert(data.message);
						$('#grid_trans').jqxGrid('updatebounddata');
						$('#modal_create_trans').modal('hide');
					} else {
						alert(data.message);
					}
				});
		});

		$('#grid_table').on('bindingcomplete', function(e) {
			$('#grid_table').jqxGrid({
				disabled: false
			});
		});




	});

	function show_trans() {
		var rowdata = row_selected('#grid_table');
		if (typeof rowdata !== 'undefined') {
			grid_trans(rowdata.ID);
			$('#grid_trans').jqxGrid('clearselection');
			//$('.modal-title').text(rowdata.NameDevice);
			$('input[name=Device_name]').val(rowdata.NameDevice);
			$('input[name=SN_number]').val(rowdata.SN);
			$('input[name=add_IDupdate_trans]').val(rowdata.ID);

		} else {
			//	$('.modal-title').text('Transaction : No data!');
		}
	}

	function grid_trans(barcode) {

		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [{
					name: 'ID',
					type: 'string'
				},
				{
					name: 'Mac',
					type: 'string'
				},
				{
					name: 'SN',
					type: 'string'
				},
				{
					name: 'ReceiveUserDate',
					type: 'date'
				},
				{
					name: 'Detail',
					type: 'string'
				},
				{
					name: 'SendSVODate',
					type: 'date'
				},
				{
					name: 'ReturnDate',
					type: 'date'
				},
				{
					name: 'SendUserDate',
					type: 'date'
				},
				{
					name: 'Remark',
					type: 'string'
				},
				{
					name: 'CreateDate',
					type: 'date'
				}

			],
			url: base_url + "/api/Device/trans/" + barcode
		});

		return $("#grid_trans").jqxGrid({
			width: '100%',
			source: dataAdapter,
			autoheight: true,
			// rowsheight : 40,
			// columnsheight : 40,
			altrows: true,
			sortable: true,
			filterable: true,
			showfilterrow: true,
			columnsresize: true,
			pageSize: 10,
			// theme : 'theme',
			columns: [{
					text: 'Mac',
					datafield: 'Mac',
					width: 100
				},
				{
					text: 'SN',
					datafield: 'SN',
					width: 100
				},
				{
					text: 'ReceiveUserDate',
					datafield: 'ReceiveUserDate',
					width: 200,
					filtertype: 'range',
					columntype: 'datetimeinput',
					cellsformat: 'yyyy-MM-dd HH:mm:ss'
				},
				{
					text: 'Detail',
					datafield: 'Detail',
					width: 100
				},
				{
					text: 'SendSVODate',
					datafield: 'SendSVODate',
					width: 200,
					filtertype: 'range',
					columntype: 'datetimeinput',
					cellsformat: 'yyyy-MM-dd HH:mm:ss'
				},
				{
					text: 'ReturnDate',
					datafield: 'ReturnDate',
					width: 200,
					filtertype: 'range',
					columntype: 'datetimeinput',
					cellsformat: 'yyyy-MM-dd HH:mm:ss'
				},
				{
					text: 'SendUserDate',
					datafield: 'SendUserDate',
					width: 200,
					filtertype: 'range',
					columntype: 'datetimeinput',
					cellsformat: 'yyyy-MM-dd HH:mm:ss'
				},
				{
					text: 'Remark',
					datafield: 'Remark',
					width: 50
				},
				{
					text: 'CreateDate',
					datafield: 'CreateDate',
					width: 200,
					filtertype: 'range',
					columntype: 'datetimeinput',
					cellsformat: 'yyyy-MM-dd HH:mm:ss'
				}
			]
		});
	}

	function grid_table() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			filter: function(data) {
				$('#grid_table').jqxGrid('updatebounddata', 'filter');
			},
			datafields: [{
					name: 'ID',
					type: 'string'
				},
				{
					name: 'FixedAssetNo',
					type: 'string'
				},
				{
					name: 'SN',
					type: 'string'
				},
				{
					name: 'Devicetype',
					type: 'string'
				},
				{
					name: 'NameDevice',
					type: 'string'
				},
				{
					name: 'StartWarranty',
					type: 'date'
				},
				{
					name: 'EndWarranty',
					type: 'date'
				},
				{
					name: 'IPAdress',
					type: 'string'
				},
				{
					name: 'MacAddress',
					type: 'string'
				},
				{
					name: 'PO',
					type: 'string'
				},
				{
					name: 'NameReceive',
					type: 'string'
				},
				{
					name: 'SN',
					type: 'string'
				},
				{
					name: 'ReceiveDate',
					type: 'date'
				},
				{
					name: 'NameCreateBy',
					type: 'string'
				},
				{
					name: 'CreateDate',
					type: 'date'
				},
				{
					name: 'NameUpdateBy',
					type: 'string'
				},
				{
					name: 'UpdateDate',
					type: 'date'
				},
				{
					name: 'Name',
					type: 'string'
				},
				{
					name: 'Remark',
					type: 'string'
				},


			],
			url: "/api/Device/table/all"
			// updaterow: function (rowid, rowdata, commit) {
			//   // console.log(rowdata.TemplateSerialNo + ' - ' + rowdata.Barcode);
			//   gojax('post', '/api/v1/serial/update', {
			//   	barcode: rowdata.Barcode,
			//   	new_serial: rowdata.TemplateSerialNo
			//   }).done(function(data) {
			//   	if (data.result === false) {
			//   		alert(data.message);
			//   		commit(false);
			//   	} else {
			//   		commit(true);
			//   	}
			//   }).fail(function() {
			//   	commit(false);
			//   	alert('ไม่สามารถอัพเดทได้');
			//   });
			//
			// },
		});

		return $("#grid_table").jqxGrid({
			width: '100%',
			source: dataAdapter,
			autoheight: true,
			pageSize: 10,
			// rowsheight : 40,
			// columnsheight : 40,
			altrows: true,
			pageable: true,
			sortable: true,
			filterable: true,
			disabled: true,
			showfilterrow: true,
			columnsresize: true,
			editable: true,
			columns: [{
					text: 'FixedAssetNo.',
					datafield: 'FixedAssetNo',
					width: 90,
					editable: false
				},
				{
					text: 'SN',
					datafield: 'SN',
					width: 100,
					editable: false
				},
				{
					text: 'NameDevice.',
					datafield: 'NameDevice',
					width: 90,
					editable: false
				},
				{
					text: 'StartWarranty',
					datafield: 'StartWarranty',
					width: 200,
					filtertype: 'range',
					columntype: 'datetimeinput',
					cellsformat: 'yyyy-MM-dd',
					editable: false
				},
				{
					text: 'EndWarranty',
					datafield: 'EndWarranty',
					width: 200,
					filtertype: 'range',
					columntype: 'datetimeinput',
					cellsformat: 'yyyy-MM-dd',
					editable: false
				},
				{
					text: 'IPAdress',
					datafield: 'IPAdress',
					width: 100,
					editable: false
				},
				{
					text: 'MacAddress',
					datafield: 'MacAddress',
					width: 100,
					editable: false
				},
				{
					text: 'PO.',
					datafield: 'PO',
					width: 90,
					editable: false
				},
				{
					text: 'ReceiveBy',
					datafield: 'NameReceive',
					width: 100,
					editable: false
				},
				{
					text: 'ReceiveDate',
					datafield: 'ReceiveDate',
					width: 200,
					filtertype: 'range',
					columntype: 'datetimeinput',
					cellsformat: 'yyyy-MM-dd',
					editable: false
				},
				{
					text: 'NameCreateBy',
					datafield: 'NameCreateBy',
					width: 100,
					editable: false
				},
				{
					text: 'CreateDate',
					datafield: 'CreateDate',
					width: 200,
					filtertype: 'range',
					columntype: 'datetimeinput',
					cellsformat: 'yyyy-MM-dd',
					editable: false
				},
				{
					text: 'UpdateBy',
					datafield: 'NameUpdateBy',
					width: 100,
					editable: false
				},
				{
					text: 'UpdateDate',
					datafield: 'UpdateDate',
					width: 200,
					filtertype: 'range',
					columntype: 'datetimeinput',
					cellsformat: 'yyyy-MM-dd',
					editable: false
				},
				{
					text: 'Name',
					datafield: 'Name',
					width: 100,
					editable: false
				},
				{
					text: 'Remark.',
					datafield: 'Remark',
					width: 90,
					editable: false
				}


			]
		});
	}

	function grid_Vendor() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [{
					name: 'VendorID',
					type: 'string'
				},
				{
					name: 'Name',
					type: 'string'
				}
			],
			// url: base_url + '/api/movement_issue/Batch'
			url: base_url + '/api/Device/vendor/all'
		});
		//console.log(dataAdapter);
		return $("#grid_Vendor").jqxGrid({
			width: '100%',
			source: dataAdapter,
			pageable: true,
			autoHeight: true,
			filterable: true,
			showfilterrow: true,
			enableanimations: false,
			sortable: true,
			pagesize: 10,
			// theme: 'theme',
			columns: [

				{
					text: 'VendorID',
					datafield: 'VendorID',
					width: 100
				},
				{
					text: 'Name',
					datafield: 'Name',
					width: 300
				}


			]
		});
	}

	function grid_Device() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [{
					name: 'ID',
					type: 'string'
				},
				{
					name: 'Name',
					type: 'string'
				}
			],
			// url: base_url + '/api/movement_issue/Batch'
			url: base_url + '/api/Device/device/all'
		});
		//console.log(dataAdapter);
		return $("#grid_Device").jqxGrid({
			width: '100%',
			source: dataAdapter,
			pageable: true,
			autoHeight: true,
			filterable: true,
			showfilterrow: true,
			enableanimations: false,
			sortable: true,
			pagesize: 10,
			// theme: 'theme',
			columns: [

				{
					text: 'ID',
					datafield: 'ID',
					width: 100
				},
				{
					text: 'Name',
					datafield: 'Name',
					width: 300
				}


			]
		});
	}

	function grid_User() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [{
					name: 'ID',
					type: 'string'
				},
				{
					name: 'Name',
					type: 'string'
				}
			],
			// url: base_url + '/api/movement_issue/Batch'
			url: base_url + '/api/Device/user/all'
		});
		//console.log(dataAdapter);
		return $("#grid_User").jqxGrid({
			width: '100%',
			source: dataAdapter,
			pageable: true,
			autoHeight: true,
			filterable: true,
			showfilterrow: true,
			enableanimations: false,
			sortable: true,
			pagesize: 10,
			// theme: 'theme',
			columns: [

				{
					text: 'ID',
					datafield: 'ID',
					width: 100
				},
				{
					text: 'Name',
					datafield: 'Name',
					width: 300
				}


			]
		});
	}
</script>
