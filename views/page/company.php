<?php $this->layout("layouts/base", ['title' => 'Company']); ?>

<h1>Company Master</h1>

<div class="btn-panel">
  <button onclick="return modal_create_open()" type="button" class="btn btn-success" data-backdrop="static" data-toggle="modal" data-target="#modal_create" disabled>Create</button>
  <button class="btn btn-info" id="edit">Edit</button>
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
        		<label for="internal_code">Internal Code</label>
        		<input type="text" name="internal_code" class="form-control" required>
        	</div>
        	<div class="form-group">

        		<label for="company_name">Name</label>
        		<input type="text" name="company_name" class="form-control" required>
        	</div>
        	<input type="hidden" name="_id">
        	<input type="hidden" name="form_type">
        	<button class="btn btn-primary"> Save </button>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="grid_company">
</div>

<script>
	jQuery(document).ready(function($) {
		grid_company();

		$('#edit').on('click', function(e) {
			var rowdata = row_selected('#grid_company');
			if (typeof rowdata !== 'undefined') {
				$('input[name=form_type]').val('update');
				$('#modal_create').modal({backdrop: 'static'});
				$('.modal-title').text('Update');
				$('input[name=internal_code]').prop('readonly', true);
				$('input[name=_id]').val(rowdata.ID);
				$('input[name=internal_code]').val(rowdata.ID);
				$('input[name=company_name]').val(rowdata.Description);
			}
		});
	});

	function modal_create_open() {
		$('input[name=form_type]').val('create');
		$('.modal-title').text('Create new');
		$('input[name=internal_code]').prop('readonly', false);
		$('#form_create').trigger('reset');
	}

	function submit_create() {
		gojax_f('post', base_url + '/api/company/create', '#form_create')
			.done(function(data) {
				if (data.status == 404) {
					gotify(data.message, 'danger');
				} else {
					$('#modal_create').modal('hide');
					$('#grid_company').jqxGrid('updatebounddata');
				}
			})
			.fail(function() {
				gotify('ไม่สามารถส่งข้อมูลได้', 'danger');
			});
		return false;
	}

	function grid_company() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
	        datafields: [
	        	{ name: 'ID', type: 'string'},
	        	{ name: 'Description', type: 'string' }
	        ],
	        url: base_url + "/api/company/all"
		});

		return $("#grid_company").jqxGrid({
	        width: 600,
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
	          { text: 'ID', datafield: 'ID', width: 300},
	          { text: 'Description', datafield: 'Description', width: 300}
	        ]
	    });
	}
</script>