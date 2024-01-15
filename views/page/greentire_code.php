<?php $this->layout("layouts/base", ['title' => 'Greentire Code']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<div class="head-space"></div>

<div class="panel panel-default">
	<div class="panel-heading">Greentire Code</div>
	<div class="panel-body">
		<div class="btn-panel">
			<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'create_greentire_code_master') === true) : ?>
				<button class="btn btn-success" id="btn_create" type="button" data-backdrop="static" data-toggle="modal" data-target="#modal_create">Create</button>
			<?php endif ?>
			<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'edit_greentire_code_master') === true) : ?>
				<button class="btn btn-info" id="edit">Edit</button>
			<?php endif ?>
			<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'delete_greentire_code_master') === true) : ?>
				<button class="btn btn-danger" id="delete">Delete</button>
			<?php endif ?>
			<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'print_greentire_code_master') === true) : ?>
				<button class="btn btn-default" id="print">Print</button>
			<?php endif ?>

			<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'map_item_greentire_code_master') === true) : ?>
				<button class="btn btn-warning" id="map_item">Map Item</button>
			<?php endif ?>

		</div>

		<div id="grid_greentire_code"></div>
	</div>
</div>

<!-- Modal -->
<div class="modal" id="modal_create" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Create new</h4>
			</div>
			<div class="modal-body">
				<form id="form_create" onsubmit="return submit_create()">
					<div class="form-group">
						<label for="id">ID</label>
						<input type="text" name="id" class="form-control" required>
					</div>
					<div class="form-group">
						<label for="description">Description</label>
						<input type="text" name="description" class="form-control" required>
					</div>

					<div class="form-group" id="form-group-map-item">
						<label for="description">Item</label>
						<div class="input-group">
							<input type="text" class="form-control" name="item" readonly>
							<span class="input-group-btn">
								<button class="btn btn-info" id="select_item" type="button">เลือก Item</button>
							</span>
						</div><!-- /input-group -->
					</div>

					<input type="hidden" name="_id">
					<input type="hidden" name="form_type">
					<button class="btn btn-primary" type="submit">Save</button>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal" id="modal-item" tabindex="-1" role="dialog">
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
				<div id="grid_item"></div>
			</div>
		</div>
	</div>
</div>

<script>
	(function() {

		grid_greentire_code();

		$('#btn_create').on('click', function() {
			$('#form-group-map-item').hide();
			$('form#form_create').trigger('reset');
			$('input[name=id]').prop('readonly', false);
			$('input[name=description]').prop('readonly', false);
			$('.modal-title').text('Create new');
			$('input[name=form_type]').val('create');
		});

		$('#map_item').on('click', function(event) {
			$('#form-group-map-item').show();
			var rowdata = row_selected("#grid_greentire_code");
			if (typeof rowdata !== 'undefined') {
				$('#modal_create').modal({
					backdrop: 'static'
				});
				$('input[name=id]').prop('readonly', true);
				$('input[name=description]').prop('readonly', true);
				$('input[name=form_type]').val('map_item');
				$('.modal-title').text('Map Item');
				$('input[name=_id]').val(rowdata.ID);
				$('input[name=id]').val(rowdata.ID);
				$('input[name=item]').val(rowdata.ItemNumber);
				$('input[name=description]').val(rowdata.Name);
			}
		});

		$('#grid_item').on('rowdoubleclick', function(event) {
			var rowdata = row_selected('#grid_item');
			$('input[name=item]').val(rowdata.ID);
			$('#modal-item').modal('hide');
		});

		$('#select_item').on('click', function(event) {
			$('#modal-item').modal({
				backdrop: 'static'
			});
			grid_item();
		});

		$('#edit').on('click', function(e) {
			$('#form-group-map-item').hide();
			var rowdata = row_selected("#grid_greentire_code");
			if (typeof rowdata !== 'undefined') {
				$('#modal_create').modal({
					backdrop: 'static'
				});
				$('input[name=id]').prop('readonly', true);
				$('input[name=description]').prop('readonly', false);
				$('input[name=form_type]').val('update');
				$('.modal-title').text('Update');
				$('input[name=_id]').val(rowdata.ID);
				$('input[name=id]').val(rowdata.ID);
				$('input[name=description]').val(rowdata.Name);
			}

		});

		$('#delete').on('click', function(event) {
			event.preventDefault();
			var rowdata = row_selected('#grid_greentire_code');
			if (!!rowdata) {
				if (confirm('Are you sure?')) {
					gojax('post', base_url + '/api/greentire/delete', {
							id: rowdata.ID
						})
						.done(function(data) {
							if (data.status == 200) {
								$('#grid_greentire_code').jqxGrid('updatebounddata');
							} else {
								alert("ไม่สามารถลบรายการนี้ได้");
							}
						});
				}
			}
		});

		$('#print').on('click', function() {


			var rowdata = row_selected('#grid_greentire_code');
			if (!!rowdata) {
				// window.open(base_url + '/generator/greentire/' + rowdata.ID, '_blank');
				window.open(base_url + '/generator/greentire/a5/' + rowdata.ID + ',' + rowdata.Name, '_blank');
			}

		});
	})();

	function submit_create() {
		gojax_f('post', base_url + '/api/greentire/create', '#form_create')
			.done(function(data) {
				if (data.status == 404) {
					gotify(data.message, 'danger');
				} else {
					$('#modal_create').modal('hide');
					$('#grid_greentire_code').jqxGrid('updatebounddata');
				}
			})
			.fail(function() {
				gotify('ไม่สามารถทำรายการได้', 'danger');
			});
		return false;
	}

	function grid_greentire_code() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [{
					name: 'ID',
					type: 'string'
				},
				{
					name: 'Name',
					type: 'string'
				},
				{
					name: 'Company',
					type: 'string'
				},
				{
					name: 'ItemNumber',
					type: 'string'
				},
				{
					name: 'ItemName',
					type: 'string'
				}
			],
			url: base_url + "/api/greentire/all"
		});

		return $("#grid_greentire_code").jqxGrid({
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
			showfilterrow: true,
			columnsresize: true,
			// theme : 'theme',
			columns: [{
					text: 'ID',
					datafield: 'ID',
					width: 100
				},
				{
					text: 'Description',
					datafield: 'Name',
					width: 150
				},
				{
					text: 'Company',
					datafield: 'Company',
					width: 100
				},
				{
					text: 'Item Number',
					datafield: 'ItemNumber',
					width: 100
				},
				{
					text: 'Item Name',
					datafield: 'ItemName',
					width: 400
				}
			]
		});
	}

	function grid_item() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			filter: function(data) {
				$('#grid_item').jqxGrid('updatebounddata', 'filter');
			},
			datafields: [{
					name: 'ID',
					type: 'string'
				},
				{
					name: 'NameTH',
					type: 'string'
				}
			],
			url: base_url + "/api/v1/item/group_sm"
		});

		return $("#grid_item").jqxGrid({
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
			showfilterrow: true,
			columnsresize: true,
			// theme : 'theme',
			columns: [{
					text: 'Item Number',
					datafield: 'ID',
					width: 100
				},
				{
					text: 'Item Name',
					datafield: 'NameTH',
					width: 450
				}
			]
		});
	}
</script>