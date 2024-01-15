<?php $this->layout("layouts/base", ['title' => 'Home']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<div class="head-space"></div>
<div class="panel panel-default">
	<div class="panel-heading">Home</div>
	<div class="panel-body">

		<div class="btn-panel">

		<?php if ($PermissionService->getUserAction($_SESSION['user_permission'] , 'line_home') === true): ?>
			<button
				id="btn_show_trans"
				onclick="return show_trans()"
				class="btn btn-primary"
				data-backdrop="static"
				data-toggle="modal"
				data-target="#modal_trans"
				disabled>
				<span class="glyphicon glyphicon-th-list"></span>
				Line
			</button>
		<?php endif ?>

		<?php if ($PermissionService->getUserAction($_SESSION['user_permission'] , 'print_home') === true): ?>
			<button class="btn btn-default" id="print" disabled>
				<span class="glyphicon glyphicon-print"></span>
				Print
			</button>
		<?php endif ?>
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
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div id="grid_trans"></div>
      </div>
    </div>
  </div>
</div>

<form
	action="<?php echo APP_ROOT; ?>/api/barcode/printing"
	target="_blank"
	id="formReprint"
	method="post">

	<input type="hidden" name="start" id="start" />
	<input type="hidden" name="qty" id="qty" />
	<input type="hidden" name="end" id="end" />
	<input type="hidden" name="reprint" id="reprint" value="1">

</form>

<script>
	jQuery(document).ready(function($) {

		grid_table();

		$('#grid_table').on('rowclick', function(event) {
			$('#btn_show_trans').prop('disabled', false);
			$('#print').prop('disabled', false);
		});

		$('#print').on('click', function(event) {
			event.preventDefault();
			var rowdata = row_selected('#grid_table');
			if (typeof rowdata !== 'undefined') {
				$('#start').val(rowdata.Barcode);
				$('#qty').val(1);
				$('#end').val(rowdata.Barcode);
				$('#formReprint').submit();
			}
		});

		$('#grid_table').on('bindingcomplete', function(e) {
			$('#grid_table').jqxGrid({ disabled: false });
		});

	});

	function show_trans() {
		var rowdata = row_selected('#grid_table');
		if (typeof rowdata !== 'undefined') {
			grid_trans(rowdata.Barcode);
			$('#grid_trans').jqxGrid('clearselection');
			$('.modal-title').text('Transaction : ' + rowdata.Barcode);
		} else {
			$('.modal-title').text('Transaction : No data!');
		}
	}

	function grid_trans(barcode) {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
	        datafields: [
	        	{ name: 'TransID', type: 'string'},
	        	{ name: 'Barcode', type: 'string'},
	        	{ name: 'CodeID', type: 'string'},
	        	{ name: 'NameTH', type: 'string'},
	        	{ name: 'Batch', type: 'string'},
	        	{ name: 'Shift', type: 'string'},
	        	{ name: 'Disposal', type: 'string'},
	        	{ name: 'Defect', type: 'string'},
	        	{ name: 'WarehouseReceiveDate', type: 'date'},
	        	{ name: 'WarehouseTransReceiveDate', type: 'date'},
	        	{ name: 'QTY', type: 'string'},
	        	{ name: 'Unit', type: 'string'},
	        	{ name: 'WH', type: 'string'},
	        	{ name: 'LC', type: 'string'},
	        	{ name: 'Document', type: 'string'},
	        	{ name: 'CreateBy', type: 'string'},
	        	{ name: 'Company', type: 'string'},
	        	{ name: 'Username', type: 'string'},
	        	{ name: 'InventJournalID', type: 'string'},
	        	{ name: 'AuthorizeName', type: 'string'},
	        	{ name: 'Side', type: 'string'},
						{ name: 'RefDocId', type: 'string'},
						{ name: 'CreateDate', type: 'date'},
						{ name: 'HoldType', type: 'string'}
	        ],
	        url: base_url + "/api/invent/trans/" + barcode
		});

		return $("#grid_trans").jqxGrid({
	        width: '100%',
	        source: dataAdapter,
	        autoheight: true,
	        // rowsheight : 40,
	        // columnsheight : 40,
	        altrows : true,
	        sortable: true,
	        filterable : true,
	        showfilterrow : true,
	        columnsresize: true,
	        pageSize: 10,
	        // theme : 'theme',
	        columns: [
	        	{ text: 'Barcode', datafield: 'Barcode', width: 100},
	        	{ text: 'CodeID', datafield: 'CodeID', width: 100},
	        	{ text: 'NameTH', datafield: 'NameTH', width: 500},
	        	{ text: 'Batch', datafield: 'Batch', width: 100},
	        	{ text: 'Disposal', datafield: 'Disposal', width: 100},
	        	{ text: 'Defect', datafield: 'Defect', width: 200},
	        	{ text: 'HoldType', datafield: 'HoldType', width: 100},
	        	{ text: 'Side', datafield: 'Side', width: 100},
	        	{ text: 'QTY', datafield: 'QTY', width: 50},
	        	{ text: 'Unit', datafield: 'Unit', width: 100},
	        	{ text: 'Warehouse', datafield: 'WH', width: 200},
	        	{ text: 'Location', datafield: 'LC', width: 150},
	        	{ text: 'Operator', datafield: 'Username', width: 100},
	        	{ text: 'Shift', datafield: 'Shift', width: 50},
	        	{ text: 'Document', datafield: 'Document', width: 100},
	        	{ text: 'Invent Journal ID', datafield: 'InventJournalID', width: 150},
	        	{ text: 'Authorize By', datafield: 'AuthorizeName', width: 200},
						{ text: 'Ref Doc Id', datafield: 'RefDocId', width: 250},
	        	{ text: 'Company', datafield: 'Company', width: 100},
	        	{ text: 'Date', datafield: 'CreateDate', width: 200, filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss'}
	        ]
	    });
	}

	function grid_table() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			filter : function (data) {
				$('#grid_table').jqxGrid('updatebounddata', 'filter');
			},
      datafields: [
      	{ name: 'ID', type: 'string'},
      	{ name: 'Username', type: 'string'},
      	{ name: 'Barcode', type: 'string' },
      	{ name: 'BarcodeFoil', type: 'string' },
      	{ name: 'DateBuild', type: 'date'},
      	{ name: 'BuildingNo', type: 'string'},
      	{ name: 'GT_Code', type: 'string'},
      	{ name: 'CuringDate', type: 'date'},
      	{ name: 'CuringCode', type: 'date'},
      	{ name: 'ItemID', type: 'string'},
      	{ name: 'NameTH', type: 'string'},
      	{ name: 'Batch', type: 'string'},
      	{ name: 'QTY', type: 'number'},
      	{ name: 'Unit', type: 'string'},
      	{ name: 'PressNo', type: 'string'},
      	{ name: 'PressSide', type: 'string'},
      	{ name: 'MoldNo', type: 'string'},
      	{ name: 'TemplateSerialNo', type: 'string'},
      	{ name: 'XrayDate', type: 'date'},
      	{ name: 'XrayNo', type: 'string'},
      	{ name: 'FinalReceiveDate', type: 'date'},
      	// { name: 'GateDescription', type: 'string'},
      	{ name: 'WarehouseReceiveDate', type: 'date'},
      	{ name: 'WarehouseTransReceiveDate', type: 'date'},
      	{ name: 'LoadingDate', type: 'string'},
      	{ name: 'DONo', type: 'string'},
      	{ name: 'PickingListID', type: 'string'},
      	{ name: 'OrderID', type: 'string'},
      	{ name: 'Disposal', type: 'string'},

      	{ name: 'WH', type: 'string'},
      	{ name: 'LC', type: 'string'},
      	{ name: 'Status', type: 'string'},
      	{ name: 'Company', type: 'string'},
      	{ name: 'Name', type: 'string'},
      	{ name: 'Weight', type: 'string'},
      	{ name: 'CheckBuild', type: 'bool'},
				{ name: 'checkcur', type: 'bool'},
				{ name: 'checkcounting', type: 'bool'},
				{ name: 'CuredTireReciveDate', type: 'date'},
				{ name: 'GT_InspectionDate', type: 'date'}
      ],
      url: "/api/invent/table/all",
      updaterow: function (rowid, rowdata, commit) {
        // console.log(rowdata.TemplateSerialNo + ' - ' + rowdata.Barcode);
        gojax('post', '/api/v1/serial/update', {
        	barcode: rowdata.Barcode,
        	new_serial: rowdata.TemplateSerialNo
        }).done(function(data) {
        	if (data.result === false) {
        		alert(data.message);
        		commit(false);
        	} else {
        		commit(true);
        	}
        }).fail(function() {
        	commit(false);
        	alert('ไม่สามารถอัพเดทได้');
        });

      },
		});

		return $("#grid_table").jqxGrid({
	        width: '100%',
	        source: dataAdapter,
	        autoheight: true,
	        pageSize : 10,
	        // rowsheight : 40,
	        // columnsheight : 40,
	        altrows : true,
	        pageable : true,
	        sortable: true,
	        filterable : true,
					disabled: true,
	        showfilterrow : true,
	        columnsresize: true,
	        editable: true,
	        columns: [
	          { text: 'No.', datafield: 'ID', width: 90, editable: false},
	          { text: 'Barcode', datafield: 'Barcode',  width: 100, editable: true},
	          { text: 'Barcode Foil', datafield: 'BarcodeFoil', width: 100, editable: false},
	          { text: 'Date Build', datafield: 'DateBuild', filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 180, editable: false},
	          { text: 'Building MC.', datafield: 'BuildingNo', width: 120, editable: false},
	          { text: 'Check Build', width: 100, datafield: 'CheckBuild', filtertype: 'bool', columntype: 'checkbox', editable: false},
						{ text: 'Check Cure', width: 100, datafield: 'checkcur', filtertype: 'bool', columntype: 'checkbox', editable: false},
	          { text: 'GT Code', datafield: 'GT_Code', width: 100, editable: false},
						{ text: 'GT InspectionDate', datafield: 'GT_InspectionDate', filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 180, editable: false},
	          { text: 'Weight', datafield: 'Weight', width: 100, editable: false},
	          { text: 'Curing Date', datafield: 'CuringDate', filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 180, editable: false},
	          { text: 'Curing Code', datafield: 'CuringCode', width: 130, editable: false},
	          { text: 'Item ID', datafield: 'ItemID', width: 100, editable: false},
	          { text: 'Size', datafield: 'NameTH', width: 200, editable: false},
	          { text: 'Batch', datafield: 'Batch', width: 100, editable: false},
	          { text: 'QTY', datafield: 'QTY', width: 100, editable: false},
	          { text: 'Unit', datafield: 'Unit', width: 50, editable: false},
	          { text: 'Press No.', datafield: 'PressNo', width: 100, editable: false},
	          { text: 'Press Side', datafield: 'PressSide', width: 100, editable: false},
	          { text: 'Mold No.', datafield: 'MoldNo', width: 100, editable: false},
	          <?php if ($PermissionService->getUserAction($_SESSION['user_permission'] , 'edit_serial_number') === true): ?>
	          	{ text: 'Template Serial No.', datafield: 'TemplateSerialNo', width: 150, editable: true},
	          <?php else: ?>
	          	{ text: 'Template Serial No.', datafield: 'TemplateSerialNo', width: 150, editable: false},
	          <?php endif; ?>
	          { text: 'X-ray Date', datafield: 'XrayDate', width: 100, editable: false},
	          { text: 'X-ray No.', datafield: 'XrayNo', width: 100, editable: false},
	          { text: 'Final Receive Date', datafield: 'FinalReceiveDate',filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 200, editable: false},
	          // { text: 'Gate', datafield: 'GateDescription', width: 100, editable: false},
	          { text: 'Q-Tech Receive Date', width: 150, editable: false},
	          { text: 'WH Trans Receive Date', datafield:'WarehouseTransReceiveDate', filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 200, editable: false},
	          { text: 'Warehouse Receive Date', datafield: 'WarehouseReceiveDate', filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 200, editable: false},
			  { text: 'Stock Count', width: 100, datafield: 'checkcounting', filtertype: 'bool', columntype: 'checkbox', editable: false},
	          { text: 'Loading Date', datafield: 'LoadingDate', width: 100, editable: false},
	          { text: 'DO No.', datafield: 'DONo', width: 100, editable: false},
	          { text: 'Picking List ID', datafield: 'PickingListID', width: 120, editable: false},
	          { text: 'Order ID', datafield: 'OrderID', width: 100, editable: false},
	          { text: 'Disposition ID', datafield: 'Disposal',  width: 100, editable: false},
	          { text: 'Warehouse', datafield: 'WH', width: 100, editable: false},
	          { text: 'Location', datafield: 'LC', width: 150, editable: false},
	          { text: 'Status', datafield: 'Status', width: 100, editable: false},
	          { text: 'Company', datafield: 'Company', width: 100, editable: false},
	          { text: 'Operator', datafield: 'Username', width: 100, editable: false}
	        ]
	    });
	}
</script>
