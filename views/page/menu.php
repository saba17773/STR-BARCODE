<?php $this->layout("layouts/base", ['title' => 'Menu']); ?>

<div class="head-space"></div>

<div class="panel panel-default">
	<div class="panel-heading">Menu</div>
	<div class="panel-body">
		<div class="btn-panel">
			<button onclick="return modal_create_open()" class="btn btn-success" data-backdrop="static" data-toggle="modal" data-target="#modal_create">Create</button>
			<button class="btn btn-info" id="edit">Edit</button>
		</div>
		<div id="grid_menu"></div>
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
        <!-- Content -->
        <form id="form_create" onsubmit="return submit_create()">
        	<div class="form-group">
        		<label for="description">Description</label>
        		<input type="text" id="description" name="description" class="form-control" autocomplete="off" required>
        	</div>
        	<div class="form-group">
        		<label for="link">Link</label>
        		<input type="text" id="link" name="link" class="form-control" autocomplete="off" required>
        	</div>
			<div class="form-group">
				<label for="typecheck">Device Type</label>
				<select name="typecheck" id="typecheck" class="form-control" required>
					<option value="0">Desktop</option>
					<option value="1">Mobile</option>
				</select>
			</div>	
        	<input type="hidden" name="form_type">
        	<input type="hidden" name="_id">
        	<button class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
	jQuery(document).ready(function($) {
		grid_menu();

		$('#edit').on('click', function(e) {
			var rowdata = row_selected('#grid_menu');
			if (typeof rowdata !== 'undefined') {
				$('input[name=form_type]').val('update');
				$('#modal_create').modal({backdrop: 'static'});
				$('.modal-title').text('Update');
				$('input[name=_id]').val(rowdata.ID);
				$('input[name=description]').val(rowdata.Description);
				$('input[name=link]').val(rowdata.Link);
				$('input[name=typecheck]').val(rowdata.TypeCheck);
			}
			
		});
	});

	function submit_create() {
		gojax_f('post', base_url + '/api/menu/create', '#form_create')
			.done(function(data) {
				if (data.status == 200) {
					$('#modal_create').modal('hide');
					$('#grid_menu').jqxGrid('updatebounddata', 'cells');
				} else {
					gotify(data.message, 'danger');
				}
			})
			.fail(function() {
				gotify('ไม่สามารถส่งข้อมูลได้', 'danger');
			});
		return false;
	}

	function modal_create_open() {
		$('input[name=form_type]').val('create');
		$('.modal-title').text('Create new');
		$('form#form_create').trigger('reset');
	}

	function grid_menu() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
	        datafields: [
	        	{ name: 'ID', type: 'number'},
	        	{ name: 'Link', type: 'string'},
	        	{ name: 'Description', type: 'string' },
	        	{ name: 'Status', type: 'string'},
	        	{ name: 'TypeCheck', type: 'int'},
	        	{ name: 'NameType', type: 'string'}
	        ],
	        url: base_url + "/api/menu/all"
		});

		return $("#grid_menu").jqxGrid({
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
	          { text: 'Description', datafield: 'Description', width: 200},
	          { text: 'Link', datafield: 'Link', width: 200},
	          { text: 'Status', datafield: 'Status', width: 50},
	          { text: 'Type', datafield: 'NameType', width: 150}
	        ]
	    });
	}
</script>

