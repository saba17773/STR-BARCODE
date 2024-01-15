<?php $this->layout("layouts/base", ['title' => 'Item Set']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<div class="head-space"></div>

<div class="panel panel-default">
	<div class="panel-heading">Item Set</div>
	<div class="panel-body">		<div class="btn-panel">
		<?php if ($PermissionService->getUserAction($_SESSION['user_permission'] , 'create_item_set') === true): ?>
			<button class="btn btn-primary" id="create">Create</button>
		<?php endif ?>
		<?php if ($PermissionService->getUserAction($_SESSION['user_permission'] , 'update_item_set') === true): ?>
			<button class="btn btn-info" id="update">Update</button>
		<?php endif ?>
		<?php if ($PermissionService->getUserAction($_SESSION['user_permission'] , 'print_item_set') === true): ?>
			<button class="btn btn-default" id="print">Print</button>
		<?php endif ?>
		</div>

		<div id="grid_itemset_master"></div>
	</div>
</div>

<!-- Modal -->
<div class="modal" id="modal-create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
        	<span class="glyphicon glyphicon-remove"></span>
        	Close
        </button>
        <h4 class="modal-title">Create</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <form id="form_item_set">
					<div class="form-group">
						<label>Item Set ID</label>

						<div class="input-group">
							<input type="text" class="form-control" name="item_set_id" id="item_set_id" readonly>
							<span class="input-group-btn">
								<button class="btn btn-info" type="button" id="btn-select-item-set">
									<span class="glyphicon glyphicon-search"></span>
									เลือก Item Set
								</button>
							</span>
						</div>
						
					</div>

					<div class="form-group">
						<label>Item ID</label>

						<div class="input-group">
							<input type="text" class="form-control" name="item_id" id="item_id" readonly>
							<span class="input-group-btn">
								<button class="btn btn-info" type="button" id="btn-select-item">
									<span class="glyphicon glyphicon-search"></span>
									เลือก Item
								</button>
							</span>
						</div>
						
					</div>

					<input type="hidden" name="_id" id="_id">
					<input type="hidden" name="form_type" id="form_type">

					<button type="submit" class="btn btn-primary">
						<span class="glyphicon glyphicon-save"></span>
						Save
					</button>
				</form>
      </div>
    </div>
  </div>
</div>

<!-- Modal เลือก item set-->
<div class="modal" id="modal-select-item-set" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
        	<span class="glyphicon glyphicon-remove"></span>
        	Close
        </button>
        <h4 class="modal-title">Item Set</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
				<div id="grid_itemset"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal ดลือก item -->
<div class="modal" id="modal-select-item" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
        	<span class="glyphicon glyphicon-remove"></span>
        	Close
        </button>
        <h4 class="modal-title">Item</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div id="grid_item_normal"></div>
      </div>
    </div>
  </div>
</div>

<script>
	jQuery(document).ready(function($) {
		grid_itemset_master();

		$('#print').on('click', function() {
			var rowdata = row_selected('#grid_itemset_master');
			if (typeof rowdata !== 'undefined') {
				window.open('/itemset/print/item/'+rowdata.item_set_id, '_blank');
			} else {
				alert("กรุณาเลือกข้อมูล");
			}
		});
		
		$('#update').on('click', function() {
			var rowdata = row_selected('#grid_itemset_master');
			if (typeof rowdata !== 'undefined') {
				$('#modal-create').modal({backdrop: 'static'});
				$('.panel-heading').text('Update');
				$('#form_type').val('update');
				$('#_id').val(rowdata.id);
				$('#item_set_id').val(rowdata.item_set_id);
				$('#item_id').val(rowdata.item_id);
			} else {
				alert('กรุณาเลือกข้อมูล');
			}
			
		});

		$('#form_item_set').submit(function(e) {
			e.preventDefault();

			var d = {};

			if ($('#form_type').val() === 'save') {
				d = {
					method: 'save',
					data: {
						item_set_id: $("#item_set_id").val(),
						item_id: $('#item_id').val()
					}
				};
			} else {
				var rowdata = row_selected('#grid_itemset_master');
				if (typeof rowdata !== 'undefined') {
					d = {
						method: 'update',
						data: {
							id: rowdata.id,
							item_set_id: $("#item_set_id").val(),
							item_id: $('#item_id').val()
						}
					};
				} else {
					alert('กรุณาเลือกข้อมูล');
				}
			}

			gojax('post', '/api/v1/itemset/'+d.method, d.data)
				.done(function(data) {
					if (data.result === true) {
						$('#modal-create').modal('hide');
						$('#grid_itemset_master').jqxGrid('updatebounddata');
					} else {
						alert('save failed');
					}
				});
		});

		$("#grid_itemset").on('rowdoubleclick', function(event) {
			  var args = event.args;
				// row's bound index.
				var boundIndex = args.rowindex;
				var rowdata = $('#grid_itemset').jqxGrid('getrowdata', boundIndex);
				$('#item_set_id').val(rowdata.ID);
				$('#modal-select-item-set').modal('hide');
		});

		$("#grid_item_normal").on('rowdoubleclick', function(event) {
			  var args = event.args;
				// row's bound index.
				var boundIndex = args.rowindex;
				var rowdata = $('#grid_item_normal').jqxGrid('getrowdata', boundIndex);
				$('#item_id').val(rowdata.ID);
				$('#modal-select-item').modal('hide');
		});

		$('#btn-select-item-set').on('click', function() {
			$('#modal-select-item-set').modal({backdrop: 'static'});
			grid_itemset();
		});

		$('#btn-select-item').on('click', function() {
			$('#modal-select-item').modal({backdrop: 'static'});
			grid_item_normal();
		});


		$('#create').on('click', function() {
			$('#modal-create').modal({backdrop: 'static'});
			$('#form_type').val('save');
			$('#form_item_set').trigger('reset');
			// gojax('get', '/api/v1/item/itemset')
			// .done(function(data) {
			// 	$('#item_set_id').html('<option value="">= เลือกข้อมูล =</option>');
			// 	$.each(data, function(i, v) {
			// 		$('#item_set_id').append('<option value="'+v.ID+'">'+v.ID+'</option>');
			// 	});
			// });
		});
	});

	function grid_itemset_master() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [
				{ name: 'id', type: 'string'},
				{ name: 'item_set_id', type: 'string'},
				{ name: 'item_id', type: 'string'},
				{ name: 'item_name', type: 'string'},
				{ name: 'item_set_name', type: 'string'}
			],
			url: '/api/v1/itemset/all'
		});

		return $("#grid_itemset_master").jqxGrid({
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
				{ text: 'Item Set', datafield: 'item_set_id', width: 100},
				{ text: 'Item Set Name', datafield: 'item_set_name', width: 600},
				{ text: 'Item ID', datafield: 'item_id', width: 100},
				{ text: 'Item Name', datafield: 'item_name', width: 600}
			]
		});
	}

	function grid_itemset() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [
				{ name: 'ID', type: 'string'},
				{ name: 'NameTH', type: 'string'},
				{ name: 'Pattern', type: 'string'},
				{ name: 'Brand', type: 'string'},
				{ name: 'UnitID', type: 'string'}
			],
			url: '/api/v1/item/itemset'
		});

		return $("#grid_itemset").jqxGrid({
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
				{ text: 'ID', datafield: 'ID', width: 100},
				{ text: 'Name', datafield: 'NameTH', width: 500},
				{ text: 'Pattern', datafield: 'Pattern', width: 100},
				{ text: 'Brand', datafield: 'Brand', width: 100},
				{ text: 'Unit', datafield: 'UnitID', width: 100}
			]
		});
	}

	function grid_item_normal() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [
				{ name: 'ID', type: 'string'},
				{ name: 'NameTH', type: 'string'},
				{ name: 'Pattern', type: 'string'},
				{ name: 'Brand', type: 'string'},
				{ name: 'UnitID', type: 'string'}
			],
			url: '/api/v1/item/normal'
		});

		return $("#grid_item_normal").jqxGrid({
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
				{ text: 'ID', datafield: 'ID', width: 100},
				{ text: 'Name', datafield: 'NameTH', width: 500},
				{ text: 'Pattern', datafield: 'Pattern', width: 100},
				{ text: 'Brand', datafield: 'Brand', width: 100},
				{ text: 'Unit', datafield: 'UnitID', width: 100}
			]
		});
	}
</script>