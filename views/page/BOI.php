<?php $this->layout("layouts/base", ['title' => 'BOI Master.']); ?>

<div class="head-space"></div>

<div class="panel panel-default">
	<div class="panel-heading">BOI Master.</div>
	<div class="panel-body">
		<div class="btn-panel">
			<button onclick="return modal_create_open()"  class="btn btn-success" data-backdrop="static" data-toggle="modal" data-target="#modal_create">Create</button>
			<button class="btn btn-info" id="edit">Edit</button>
			<button class="btn btn-danger" id="delete">Delete</button>
			<!-- <button id="print" class="btn btn-default">Print</button> -->
		</div>

		<div id="grid_building"></div>
	</div>
</div>

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

      			<input type="hidden" name="building_id" id="building_id" class="form-control" autocomplete="off" required>
							<input type="hidden" name="delete_id" id="delete_id" class="form-control" autocomplete="off" required>


          <div class="form-group">
            <label for="BOI">BOI</label>
            <input type="text" name="BOI_id" id="BOI_id" class="form-control" autocomplete="off" required>
          </div>

      		<div class="form-group">
      			<label for="building_desc">Description</label>
      			<input type="text" name="building_desc" id="building_desc" class="form-control" autocomplete="off" required>
      		</div>

      		<input type="hidden" name="form_type">
      		<input type="hidden" name="_id">
      		<button class="btn btn-primary" type="submit">Save</button>
      	</form>
      </div>
    </div>
  </div>
</div>


<script>
	jQuery(document).ready(function($) {

		grid_building();

		$('#edit').on('click', function(e) {
			var rowdata = row_selected("#grid_building");
			if (typeof rowdata !== 'undefined') {
				$('#modal_create').modal({backdrop: 'static'});
				$('input[name=form_type]').val('update');
				$('input[name=building_id]').prop('readonly', true);
				$('.modal-title').text('Update');
				$('input[name=building_id]').val(rowdata.ID);
				$('input[name=BOI_id]').val(rowdata.ID).prop('readonly', true);;
        $('input[name=building_desc]').val(rowdata.Description);


			}

		});

		$('#delete').on('click', function(event) {
			event.preventDefault();
			var rowdata = row_selected('#grid_building');
			$('input[name=form_type]').val('delete');
			$('input[name=delete_id]').val(rowdata.ID);
			//alert(rowdata.ID); exit();
			if (!!rowdata) {
				if (confirm('Are you sure?')) {
					gojax_f('post', base_url + '/api/boi/create', '#form_create')
					.done(function(data) {
						if (data.status != 200) {
						//	gotify(data.message, 'danger');
							alert(data.message);
						} else {
							alert(data.message);
							$('#grid_building').jqxGrid('updatebounddata');
						}
					});

				}
			}
		});

		// $('#print').on('click', function() {
		// 	var rowdata = row_selected('#grid_building');
		// 	if (typeof rowdata !== 'undefined') {
		// 		window.open(base_url + '/generator/building/a5/' + rowdata.ID, '_blank');
		// 	}
		//
		// });



	});

	function modal_create_open() {
		$('#form_create').trigger('reset');
		$('input[name=form_type]').val('create');
		$('.modal-title').text('Create new');
		$('input[name=BOI_id]').prop('readonly', false);
	}

	function submit_create() {
		var	building_id = $('input[name=BOI_id]').val();
		var	building_desc = $('input[name=building_desc]').val();

		if (!!building_id && !!building_desc) {
			gojax_f('post', base_url + '/api/boi/create', '#form_create')
			.done(function(data) {
				if (data.status != 200) {
				//	gotify(data.message, 'danger');
					alert(data.message);
				} else {
					//	alert(data.message);
					$('#modal_create').modal('hide');
					$('#grid_building').jqxGrid('updatebounddata');
				}
			});
		}
		return false;
	}

	function grid_building() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
	        datafields: [
	        	{ name: 'ID', type: 'string'},
	        	{ name: 'Description', type: 'string' },
						{ name: 'BOI', type: 'string' }

	        ],
	        url: base_url + "/api/boi/all"
		});

		return $("#grid_building").jqxGrid({
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
            { text: 'BOI', datafield: 'ID', width: 150},
	          { text: 'Description', datafield: 'Description', width: 150}


	        ]
	    });
	}



</script>
