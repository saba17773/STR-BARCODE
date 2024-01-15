<?php $this->layout("layouts/base", ['title' => 'Loading']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<div class="head-space"></div>

<div class="panel panel-default">
	<div class="panel-heading">Loading</div>
	<div class="panel-body">
		<div class="btn-panel">
			<?php if ($PermissionService->getUserAction($_SESSION['user_permission'] , 'loading_desktop_update') === true): ?>
				<button class="btn btn-primary" id="update">Update</button>
			<?php endif ?>
			<button class="btn btn-info" id="line">Line</button>
			<button class="btn btn-default" id="print">Print</button>
			<button class="btn btn-success" id="export">Export</button>

		</div>

		<div id="grid_loading"></div>
	</div>
</div>

<!-- Modal -->
<div class="modal" id="modal_line" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Line</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div class="row">
        	<div class="col-xs-2">
        		Order ID : <input type="text" id="line_order_id" class="form-control" readonly>
        	</div>
        	<div class="col-xs-2">
        		Picking List ID : <input type="text" id="line_picking_list_id" class="form-control" readonly>
        	</div>
        	<div class="col-xs-8">
        		Customer Name : <input type="text" id="line_customer_name" class="form-control" readonly>
        	</div>
        </div>
        <div class="btn-panel" style="margin-top: 20px;">
        	<button class="btn btn-info" id="line_detail">Line</button>
        </div>
        <div id="grid_line"></div>	
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal" id="modal_line_detail" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Loading Transaction</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div class="row">
        	<div class="col-xs-2">
        		<label for="loading_trans_item_id">Item ID</label>
        		<input type="text" id="loading_trans_item_id" class="form-control" disabled>
        	</div>
        	<div class="col-xs-10">
        		<label for="loading_trans_item_name">Name</label>
        		<input type="text" id="loading_trans_item_name" class="form-control" disabled>
        	</div>
        </div>
        <hr />
        <div id="grid_line_detail"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Update-->
<div class="modal" id="modal_update" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Update</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <table class="table">
        	<tr>
        		<td>
        			<div class="form-group">
								<label for=""> Doc No.</label>
								<input type="text" id="doc_no" name="doc_no" class="form-control" readonly>
							</div>
        		</td>
        		<td>
        			<div class="form-group">
								<label for=""> Order ID.</label>
								<input type="text" id="order_id" name="order_id" class="form-control" readonly>
							</div>
        		</td>
        	</tr>
        	<tr>
        		<td>
        			<div class="form-group">
								<label for=""> Picking List ID</label>
					     	<input type="text" id="pickinglist_id" name="pickinglist_id" class="form-control" readonly>
							</div>
        		</td>
        		<td>
        			<div class="form-group">
								<label for=""> Customer Name</label>
					     	<input type="text" id="cust_name" name="cust_name" class="form-control" readonly>
							</div>
        		</td>
        	</tr>
        	<tr>
        		<td>
        			<div class="form-group">
								<label for=""> Delivery Date</label>
					     	<input type="text" id="delivery_date" name="delivery_date" class="form-control" readonly>
							</div>
        		</td>
        		<td>
        			<div class="form-group">
								<label for=""> Confirm Date</label>
					     	<input type="text" id="confirm_date" name="confirm_date" class="form-control" readonly>
							</div>
        		</td>
        	</tr>
        	<tr>
        		<td colspan="2">
							<div class="input-group" style="width: 100%;">
					      <input type="text" id="pickinglist_ref" name="pickinglist_ref" class="form-control" placeholder="Ref PickingList">
					      <span class="input-group-btn">
					        <button class="btn btn-info" id="search_ref_pickinglist" type="button">
					        	<span class="glyphicon glyphicon-search" aria-hidden="true">
					        </button>
					      </span>
					    </div><!-- /input-group -->
        		</td>
        	</tr>
        </table>
				<button class="btn btn-primary" id="save_ref">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal select ref picking list from invent picking list jour -->
<div class="modal" id="modal_ref_pickinglist" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Picking Lists</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div id="grid_pickinglist_ref"></div>
      </div>
    </div>
  </div>
</div>

<script>
	jQuery(document).ready(function($) {
		grid_loading();

		$('#grid_loading').on('rowclick', function(event) {
	    var args = event.args;
	    var rowBoundIndex = args.rowindex;
    	var rowdata = $('#grid_loading').jqxGrid('getrowdata', rowBoundIndex);
			if (typeof rowdata !== 'undefined') {
				
				if (rowdata.Status === 6 ||rowdata.Status === 1) {
					$('#export').prop('disabled', true);
					
				} else {
					$('#export').prop('disabled', false);
				}
				if (rowdata.Status === 3) {
					$('#update').prop('disabled', false);
					
				} else {
					$('#update').prop('disabled', true);
				}
			}
		});

		$("#save_ref").on('click', function(event) {
			event.preventDefault();
			if ($.trim($('#pickinglist_ref').val()) === '') {
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text('กรุณาเลือกข้อมูล');
			} else {
				gojax('post', base_url+'/api/loading/pickinglist_ref', {
					pickinglist_id_current: $('#pickinglist_id').val(),
					pickinglist_id_ref: $('#pickinglist_ref').val()
				})
				.done(function(data) {
					if (data.status === 200) {
						$('#modal_update').modal('hide');
						$('#grid_loading').jqxGrid('updatebounddata');
					} else {
						$('#modal_alert').modal({backdrop: 'static'});
						$('#modal_alert_message').text(data.message);
					}
				});
			}
		});	

		$("#grid_pickinglist_ref").on('rowdoubleclick', function() {
			var rowdata = row_selected('#grid_pickinglist_ref');
			if (typeof rowdata !== 'undefined') {
				$('#pickinglist_ref').val(rowdata.PICKINGLISTID);
				$('#modal_ref_pickinglist').modal('hide');
			} else {
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text('Error');
			}
		});

		$('#search_ref_pickinglist').on('click', function() {
			$('#modal_ref_pickinglist').modal({backdrop: 'static'});
			// /api/loading/pickinglist_by_orderid
			grid_pickinglist_ref($('#order_id').val());
		});

		$('#modal_update').on('shown.bs.modal', function() {
			$('#ref_pickinglist_id').val('').focus();
		});

		$('#line').on('click', function() { 
			var rowdata = row_selected('#grid_loading');
			if (typeof rowdata !== 'undefined') {
				$('#modal_line').modal({backdrop: 'static'});
				$('#line_order_id').val(rowdata.OrderId);
				$('#line_picking_list_id').val(rowdata.PickingListId);
				$('#line_customer_name').val(rowdata.CustName);
				grid_line(rowdata.PickingListId);
				// console.log(rowdata);
			} else {
				$('#modal_alert').modal({backdrop: 'static'});
        $('#modal_alert_message').text('กรุณาเลือกข้อมูล');
			}
		});

		$('#update').on('click', function() {
			var rowdata = row_selected('#grid_loading');
			if (typeof rowdata !== 'undefined') {
				$('#modal_update').modal({backdrop: 'static'});

				$('#doc_no').val(rowdata.DocNo);
				$('#order_id').val(rowdata.OrderId);
				$('#pickinglist_id').val(rowdata.PickingListId);
				$('#cust_name').val(rowdata.CustName);
				
				var de_date = String(rowdata.DeliveryDate.toISOString().substr(0, 10));
				var co_date = String(rowdata.ConfirmDate.toISOString().substr(0, 10));

				$('#delivery_date').val(de_date);
				$('#confirm_date').val(co_date);

				$('#pickinglist_ref').val('').focus();
			} else {
				$('#modal_alert').modal({backdrop: 'static'});
        $('#modal_alert_message').text('กรุณาเลือกข้อมูล');
			}
		});

		$('#line_detail').on('click', function() {
			var rowdata = row_selected('#grid_line');
			if (typeof rowdata !== 'undefined') {
				$('#modal_line_detail').modal({backdrop: 'static'});
				$('#loading_trans_item_id').val(rowdata.ItemId);
				$('#loading_trans_item_name').val(rowdata.Name);
				grid_line_detail(rowdata.PickingListId, rowdata.ItemId, rowdata.InventTransId);
			} else {
				$('#modal_alert').modal({backdrop: 'static'});
        $('#modal_alert_message').text('กรุณาเลือกข้อมูล');
			}
		});

		$('#print').on('click', function() {
			var rowdata = row_selected('#grid_loading');
			//alert(rowdata.OrderId+","+rowdata.PickingListId);

			// console.log(rowdata);

			var d = rowdata.CreatedDate;
			var CreatedDate = getFormattedDate(d);
			var str = rowdata.CustName;
			var str = str.toString();
			var check = 1;

			if (!!rowdata) {
				window.open(base_url + '/api/loading/report/' + rowdata.PickingListId+'/'+rowdata.OrderId+'/'+CreatedDate+'/'+str.replace('/', '')+'/'+check, '_blank');
			}
			
		});

		$('#export').on('click', function() {
			var rowdata = row_selected('#grid_loading');
			//alert(rowdata.OrderId+","+rowdata.PickingListId);

			// console.log(rowdata);

			var d = rowdata.CreatedDate;
			var CreatedDate = getFormattedDate(d);
			var str = rowdata.CustName;
			var str = str.toString();
			var check = 2;
			
			if (!!rowdata) {
				window.open(base_url + '/api/loading/report/' + rowdata.PickingListId+'/'+rowdata.OrderId+'/'+CreatedDate+'/'+str.replace('/', '')+'/'+check, '_blank');
			}
			
		});
	});
	

	function getFormattedDate(date) {
		 var year = date.getFullYear();
		 var month = (1 + date.getMonth()).toString();
		 month = month.length > 1 ? month : '0' + month;
		 var day = date.getDate().toString();
		 day = day.length > 1 ? day : '0' + day;
		 return year + '-' + month + '-' + day;
	}

	function grid_loading() {
		var dataAdapter = new $.jqx.dataAdapter({
			filter : function (data) {
				// console.log(data);
				$('#grid_loading').jqxGrid('updatebounddata', 'filter');
			},
			datatype: 'json',
	        datafields: [
				{ name: 'DocNo', type: 'string'},
	        	{ name: 'OrderId', type: 'string'},
	        	{ name: 'Sodsc', type: 'string'},
						{ name: 'PickingListId', type: 'string' },
						{ name: 'CustID', type: 'string'},
	        	{ name: 'CustName', type: 'string'},
				{ name: 'DeliveryDate', type: 'date'},
				{ name: 'ConfirmDate', type: 'date'},
	        	{ name: 'Status', type: 'number' },
	        	{ name: 'RefPickingListId', type: 'string'},
				{ name: 'StatusDesc', type: 'string'},
				{ name: 'Fullname', type: 'string'},
				{ name: 'CreatedDate', type: 'date'}
	        ],
	        url: base_url + "/api/loading/table/all_status",
	        sortcolumn: 'CreatedDate',
    			sortdirection: 'desc'
		});
		return $("#grid_loading").jqxGrid({
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
	        showfilterrow : true,
	        columnsresize: true,
	        // theme: 'theme',
	        columns: [
						// { text: 'Doc No.', datafield: 'DocNo', width: 250},
	          { text: 'Order ID', datafield: 'OrderId', width: 120},
	          { text: 'Order DSC', datafield: 'Sodsc', width: 120},
	          { text: 'Picking List ID', datafield: 'PickingListId', width: 120},
	          { text: 'Customer Name', datafield: 'CustName', width: 320},
	          { text: 'Delivery Date', datafield: 'DeliveryDate', filtertype: 'range', columntype: 'datetimeinput', width: 120, cellsformat: 'yyyy-MM-dd'},
	          { text: 'Confirm Date',datafield: 'ConfirmDate', cellsformat: 'yyyy-MM-dd', filtertype: 'range', columntype: 'datetimeinput',  width: 120, },
	          { text: 'Ref Picking List', datafield: 'RefPickingListId', width: 130},
	          { text: 'Status', datafield: 'StatusDesc', width: 100, filtertype: 'checkedlist', filteritems: ['Open','In-Progress','Confirm','Confirmed','Complete','Cancel','Picked'],
	          	 cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
				          var cl;

				          if (rowdata.Status == 1) {
				            cl = 'white';
				          } else if (rowdata.Status == 2) {
				            cl = 'yellow';
				          } else if (rowdata.Status == 3) {
				            cl = 'orange';
				          } else if (rowdata.Status == 4) {
				            cl = 'green';
				          } else if (rowdata.Status == 5) {
				            cl = 'blue';
				          } else if (rowdata.Status == 6) {
				          	cl = 'red';
				          }
				          return '<div style=\'padding: 5px; height: 60px; background : '+ cl +' ; color:#000000;\'> '+ value +' </div>';
				        }
	        	},
	        	{ text: 'Create Date', datafield: 'CreatedDate', cellsformat: 'yyyy-MM-dd', width: 120, filtertype: 'range', columntype: 'datetimeinput'},
	          { text: 'Create By' , datafield: 'Fullname', width: 150}
	          
	        ]
	  });
	}

	function grid_line(pickingListId) {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
	        datafields: [
	        	{ name: 'LineID', type: 'string'},
	        	{ name: 'PickingListId', type: 'string' },
	        	{ name: 'InventTransId', type: 'string'},
						{ name: 'ItemId', type: 'string'},
						{ name: 'Name', type: 'string'},
						{ name: 'OrderQty', type: 'number'},
						{ name: 'OrderUnit', type: 'string'},
						{ name: 'Remainder', type: 'number'},
						{ name: 'LoadingQTY', type: 'number'},
						{ name: 'Status', type: 'number'},
						{ name: 'StatusDesc', type: 'string'}
	        ],
	        url: base_url + "/api/loading/line/"+pickingListId
		});
		return $("#grid_line").jqxGrid({
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
	        showfilterrow : true,
	        columnsresize: true,
	        // theme: 'theme',
	        columns: [
	          { text: 'Line ID' , datafield: 'LineID', width: 50},
	          { text: 'Item ID', datafield: 'ItemId', width: 100},
	          { text: 'Name', datafield: 'Name', width: 300},
	          { text: 'Order QTY',datafield: 'OrderQty', width: 100},
	          { text: 'Order Unit',datafield: 'OrderUnit', width: 100},
	          { text: 'Remainder',datafield: 'Remainder', width: 100},
	          { text: 'Loading QTY',datafield: 'LoadingQTY', width: 100},
	          { text: 'Status',datafield: 'StatusDesc', width: 100}
	        ]
	  });
	}

	function grid_line_detail(pid, itemid, inventtranid) {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
	        datafields: [
	        	{ name: 'LineId', type: 'number'},
	        	{ name: 'InventTransId', type: 'string'},
	        	{ name: 'Barcode', type: 'string' },
	        	{ name: 'BatchNo', type: 'string'},
	        	{ name: 'Qty', type: 'number'},
	        	{ name: 'warehouse_desc', type: 'string'},
	        	{ name: 'location_desc', type: 'string'},
	        	{ name: 'StatusDesc', type: 'string'},
	        	{ name: 'CreatedDate', type: 'date'},
	        	{ name: 'Fullname', type: 'string'},
	        	{ name: 'SerialName', type: 'string'}
	        ],
	        url: base_url + "/api/loading/trans/"+pid+'/'+itemid+'/'+inventtranid
		});
		return $("#grid_line_detail").jqxGrid({
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
	        showfilterrow : true,
	        columnsresize: true,
	        // theme: 'theme',
	        columns: [
	        	{ text: 'No.', datafield: 'LineId', width: 100, 
	        		cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
	        			return '<div style="padding: 5px;">'+(index+1)+'</div>';
	        		}
	        	},
	        	{ text: 'Barcode', datafield: 'Barcode', width: 100},
	        	{ text: 'InventTransId', datafield: 'InventTransId', width: 100},
	        	{ text: 'Template Serial Number', datafield: 'SerialName', width: 200},
	        	{ text: 'Warehouse', datafield: 'warehouse_desc', width: 200},
	        	{ text: 'Location', datafield: 'location_desc', width: 100},
	        	{ text: 'Batch', datafield: 'BatchNo', width:  100},
	        	{ text: 'QTY', datafield: 'Qty', width: 100},
	        	{ text: 'Status', datafield: 'StatusDesc', width: 100},
	        	{ text: 'Create By', datafield: 'Fullname', width: 200},
	        	{ text: 'Create Date', datafield: 'CreatedDate', cellsformat: 'yyyy-MM-dd', width: 100}
	        ]
	  });
	}

	// picking list by orider id
	function grid_pickinglist_ref(order_id) {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
	        datafields: [
	        	{ name: 'ORDERID', type: 'number'},
	        	{ name: 'PICKINGLISTID', type: 'string' }
	        ],
	        url: base_url + "/api/loading/pickinglist_by_orderid/"+order_id
		});
		return $("#grid_pickinglist_ref").jqxGrid({
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
	        showfilterrow : true,
	        columnsresize: true,
	        // theme: 'theme',
	        columns: [
	        	{ text: 'ORDERID', datafield: 'ORDERID', width: 200},
	        	{ text: 'PICKINGLISTID', datafield: 'PICKINGLISTID', width: 200}
	        ]
	  });
	}
</script>