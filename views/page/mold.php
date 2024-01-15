<?php $this->layout("layouts/base", ['title' => 'Mold']); ?>

<div class="head-space"></div>

<div class="panel panel-default">
	<div class="panel-heading">Mold</div>
	<div class="panel-body">
		<div class="btn-panel">
			<button onclick="return modal_create_open()" class="btn btn-success" data-backdrop="static" data-toggle="modal" data-target="#modal_create">Create</button>
			<button class="btn btn-info" id="edit">Edit</button>
		</div>

		<div id="grid_mold"></div>
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
        		<label for="ID">ID</label>
        		<input type="text" name="ID" id="ID" class="form-control" autocomplete="off" required>
        	</div>
        	<div class="form-group">
        		<label for="Description">Description</label>
        		<input type="text" name="Description" id="Description" class="form-control" autocomplete="off" required>
        	</div>
        	<input type="hidden" name="form_type">
        	<button class="btn btn-primary" type="submit">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
	jQuery(document).ready(function($) {
		grid_mold();

		$('#edit').on('click', function(e) {
			var rowdata = row_selected("#grid_mold");
			if (typeof rowdata !== 'undefined') {
				$('#modal_create').modal({backdrop: 'static'});
				$('input[name=form_type]').val('update');
				$('input[name=ID]').prop('readonly', true);
				$('.modal-title').text('Update');
				$('input[name=ID]').val(rowdata.ID);
				$('input[name=Description]').val(rowdata.Description);
			}
		});
	});

	function modal_create_open() {
		$('form#form_create').trigger('reset');
		$('input[name=form_type]').val('create');
		$('.modal-title').text('Create new');
		$('input[name=ID]').prop('readonly', false);
	}

	function submit_create() {
		var id = $('input[name=ID]').val();
		var desc = $('input[name=Description]').val();
		if (!!id && !!desc) {
			gojax_f('post', base_url+'/api/mold/create', '#form_create')
				.done(function(data) {
					if (data.status == 404) {
						gotify(data.message, 'danger');
					} else {
						$('#modal_create').modal('hide');
						$('#grid_mold').jqxGrid('updatebounddata');
					}
				});
		}
		return false;
	}

	function grid_mold() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
	        datafields: [
	        	{ name: 'ID', type: 'number'},
	        	{ name: 'Description', type: 'string' }
	        ],
	        url: base_url + "/api/mold/all"
		});

		return $("#grid_mold").jqxGrid({
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
	        // theme : 'theme',
	        columns: [
	        	{ text: 'ID', datafield: 'ID', width: 100},
	        	{ text: 'Description', datafield: 'Description', width: 100}
	        ]
	    });
	}
</script>