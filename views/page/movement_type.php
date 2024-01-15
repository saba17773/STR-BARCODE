<?php $this->layout("layouts/base", ["title" => "Movement Type"]); ?>

<h1>Movement Type</h1>

<div class="btn-panel">
	<button class="btn btn-success btn-lg"
		data-backdrop="static" data-toggle="modal" data-target="#modal_create">Create</button>
	<button class="btn btn-info btn-lg" id="edit">Edit</button>
	<button class="btn btn-danger btn-lg" id="delete">Delete</button>
</div>

<!-- Modal -->
<div class="modal" id="modal_create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Create</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <form id="formMovementType">
        	<div class="form-group">
        		<label for="id">ID</label>
						<input type="text" name="id" id="id" class="form-control" required>	
        	</div>
        	<div class="form-group">
        		<label for="description">Description</label>
						<input type="text" name="description" id="description" class="form-control" required>	
        	</div>
        	<button class="btn btn-primary btn-lg" type="submit">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="grid_movementType"></div>

<script>
	
jQuery(document).ready(function($) {
	
	grid_movementType();

	$('#delete').on('click', function(event) {
		event.preventDefault();
		var rowdata = row_selected('#grid_movementType');
		if(typeof rowdata !== 'undefined') {
			alert('coming soon...');
		} else {
			$('#modal_alert').modal({backdrop: 'static'});
			$('#modal_alert_message').text('กรุณากรอกข้อมูล');
			$('#top_alert').hide();
		}
	});

	$('#edit').on('click', function(event) {
		event.preventDefault();
		var rowdata = row_selected('#grid_movementType');
		if (typeof rowdata !== 'undefined') {
			$('#id').val(rowdata.ID).prop('readonly', true);
			$('#description').val(rowdata.Description);
			$('#modal_create').modal({backdrop: 'static'});
		}
	});

	$('#formMovementType').on('submit', function(event) {
		event.preventDefault();

		var _id = $('#id').val();
		var description = $('#description').val();

		if (!!$.trim(_id) && !!$.trim(description)) {
			gojax_f('post', base_url+'/api/movement_type/save', '#formMovementType')
			.done(function(data) {
				if (data.status === 200) {
					$('#grid_movementType').jqxGrid('updatebounddata');
					$('#modal_create').modal('hide');
				} else {
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text(data.message);
					$('#top_alert').hide();
				}
			});
		} else {
			$('#modal_alert').modal({backdrop: 'static'});
			$('#modal_alert_message').text('กรุณากรอกข้อมูล');
			$('#top_alert').hide();
		}
	});

});

function grid_movementType() {

		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
        datafields: [
        	{ name: 'ID', type: 'string'},
        	{ name: 'Description', type: 'string' }
        ],
        url: base_url + "/api/movement_type/all"
		});

		return $("#grid_movementType").jqxGrid({
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