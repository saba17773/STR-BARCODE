<?php $this->layout("layouts/base", ['title' => 'Department']); ?>

<h1>Department</h1>

<div class="btn-panel">
	<button onclick="return modal_create_open()"  class="btn btn-success" data-backdrop="static" data-toggle="modal" data-target="#modal_create">Create</button>
	<button class="btn btn-info" id="edit">Edit</button>
</div>

<div id="grid_dep"></div>

<!-- Create Modal -->
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
      			<label for="dep_name">Name</label>
      			<input type="text" name="dep_name" id="dep_name" class="form-control" autocomplete="off" required>
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
		grid_dep();

		$('#edit').on('click', function(e) {
			var rowdata = row_selected("#grid_dep");
			if (typeof rowdata !== 'undefined') {
				$('#modal_create').modal({backdrop: 'static'});
				$('input[name=form_type]').val('update');
				$('.modal-title').text('Update');
				$('input[name=_id]').val(rowdata.Code);
				$('input[name=dep_name]').val(rowdata.Description);
			}
			
		});
	});

	function modal_create_open() {
		$('#form_create').trigger('reset');
		$('.modal-title').text('Create new');
		$('input[name=form_type]').val('create');
	}

	function submit_create() {
		var	wh_name = $('input[name=dep_name]').val();
		if (!!wh_name) {
			gojax_f('post', base_url + '/api/department/create', '#form_create')
			.done(function(data) {
				if (data.status != 200) {
					gotify(data.message, 'danger');
				} else {
					$('#modal_create').modal('hide');
					$('#grid_dep').jqxGrid('updatebounddata');
				}
			});
		}
		return false;
	}

	function grid_dep() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
	        datafields: [
	        	{ name: 'Code', type: 'string'},
	        	{ name: 'Description', type: 'string' }
	        ],
	        url: base_url + "/api/department/all"
		});

		return $("#grid_dep").jqxGrid({
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
	          { text: 'Description', datafield: 'Description', width: 300}
	        ]
	    });
	}
</script>
