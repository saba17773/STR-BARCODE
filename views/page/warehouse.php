<?php $this->layout("layouts/base", ['title' => 'Warehouse']); ?>

<h1>Warehouse</h1>

<div class="btn-panel">
	<button onclick="return modal_create_open()"  class="btn btn-success" data-backdrop="static" data-toggle="modal" data-target="#modal_wh_create">Create</button>
	<button class="btn btn-info" id="edit">Edit</button>
</div>

<div id="grid_wh"></div>

<!-- Create Modal -->
<div class="modal" id="modal_wh_create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Create new</h4>
      </div>
      <div class="modal-body">
      	<form id="form_create_wh" onsubmit="return submit_create_wh()">
      		<div class="form-group">
      			<label for="wh_name">Warehouse name</label>
      			<input type="text" name="wh_name" id="wh_name" class="form-control" autocomplete="off" required>
      		</div>
      		<div class="form-group">
      			<label for="type">Type</label>
      			<select name="type" id="type" class="form-control" required></select>
      		</div>
      		<input type="hidden" name="wh_id" value="">
      		<button class="btn btn-primary">Save</button>
      	</form>
      </div>
    </div>
  </div>
</div>


<script>
	jQuery(document).ready(function($) {
		grid_wh();

		$('#edit').on('click', function(e) {
			var rowdata = row_selected("#grid_wh");
			if (typeof rowdata !== 'undefined') {
				$('#modal_wh_create').modal({backdrop: 'static'});
				$('.modal-title').text('Update');
				$('input[name=wh_id]').val(rowdata.ID);
				$('input[name=wh_name]').val(rowdata.Description);

				gojax('get', base_url+'/api/warehouse_type/all')
					.done(function(data) {
						$('#type').html('');
						$.each(data, function(index, val) {
							 $('#type').append('<option value="'+val.ID+'">'+val.Description+'</option>');
						});
						$('#type').val(rowdata.Type);
					});
			}
		});
	});

	function modal_create_open() {
		$('#form_create_wh').trigger('reset');
		$('.modal-title').text('Create new');

		gojax('get', base_url+'/api/warehouse_type/all')
			.done(function(data) {
				$('#type').html('<option value="">= เลือกข้อมูล =</option>');
				$.each(data, function(index, val) {
					 $('#type').append('<option value="'+val.ID+'">'+val.Description+'</option>');
				});
			});
	}

	function submit_create_wh() {
		var	wh_name = $('#wh_name').val();
		var	type = $('#type').val();
		if (!!wh_name && !!type) {
			$.ajax({
				url : base_url + '/api/warehouse/create',
				type : 'post',
				cache : false,
				dataType : 'json',
				data : $('form#form_create_wh').serialize()
			})
			.done(function(data) {
				if (data.status != 200) {
					alert(data.message);
				} else {
					$('#modal_wh_create').modal('hide');
					$('#grid_wh').jqxGrid('updatebounddata');
				}
			});
		}
		return false;
	}

	function grid_wh() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
	        datafields: [
	        	{ name: 'ID', type: 'number'},
	        	{ name: 'Description', type: 'string' },
	        	{ name: 'Company', type: 'string'},
	        	{ name: 'Type', type: 'number'},
	        	{ name: 'TypeName', type: 'string'}
	        ],
	        url: base_url + "/api/warehouse/all"
		});

		return $("#grid_wh").jqxGrid({
	        width: 700,
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
	          { text: 'Description', datafield: 'Description', width: 300},
	          { text: 'Company', datafield: 'Company', width: 200},
	          { text: 'Type', datafield: 'TypeName', width: 100}
	        ]
	    });
	}
</script>
