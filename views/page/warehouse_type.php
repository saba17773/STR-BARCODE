<?php $this->layout("layouts/base", ['title' => 'Warehouse Type']); ?>

<h1>Warehouse Type</h1>

<div class="btn-panel">
	<button class="btn btn-success" id="create" data-backdrop="static" data-toggle="modal" data-target="#modal_create">Create</button>
	<button class="btn btn-info" id="edit">Edit</button>
	<button class="btn btn-danger" id="delete">Delete</button>
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
        <form id="formWarehouseType">
        	<div class="form-group">
        		<label for="description">Description</label>
        		<input type="text" name="description" id="description" required class="form-control">
        	</div>
          <input type="hidden" name="_id" value="">
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="grid_warehouse_type"></div>

<script>
	jQuery(document).ready(function($) {

    grid_warehouse_type();

    $('#modal_alert').on('hidden.bs.modal', function() {
      $('#description').val('').focus();
    });

    $('#create').on('click', function() {
      $('#formWarehouseType').trigger('reset');
      /* Act on the event */
    });

    $('#delete').on('click', function() {
      var rowdata = row_selected('#grid_warehouse_type');
       if (typeof rowdata !== 'undefined') {
        gojax('post', base_url+'/api/warehouse_type/delete', {
          id: rowdata.ID
        })
        .done(function(data) {
          if (data.status === 200) {
            $('#grid_warehouse_type').jqxGrid('updatebounddata');
          } else {
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text(data.message);
            $('#top_alert').hide();
          }
        });
      } else {
        alert('กรุณาเลือกข้อมูล')
      }
    });

    $('#edit').on('click', function() {
      var rowdata = row_selected('#grid_warehouse_type');
      if (typeof rowdata !== 'undefined') {
        $('#modal_create').modal({backdrop: 'static'});
        $('#description').val(rowdata.Description);
        $('input[name=_id]').val(rowdata.ID);
      } else {
        alert('กรุณาเลือกข้อมูล')
      }
    }); 

		$('form#formWarehouseType').on('submit', function(e) {
      e.preventDefault();
			if (!!$.trim($('#description').val())) {
        gojax_f('post', base_url+'/api/warehouse_type/create', '#formWarehouseType')
        .done(function(data) {
          if (data.status === 200) {
            $('#formWarehouseType').trigger('reset');
            $('#modal_create').modal('hide');
            $('#grid_warehouse_type').jqxGrid('updatebounddata');
            // $('#top_alert').show();
            // $('#top_alert_message').text(data.message);
            // $('#modal_alert').modal('hide');
          } else {
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text(data.message);
            $('#top_alert').hide();
          }
        })
      } else {
        $('#modal_alert').modal({backdrop: 'static'});
        $('#modal_alert_message').text('กรุณากรอกข้อมูล');
        $('#top_alert').hide();
      }
		});
	});

  function grid_warehouse_type() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
            datafields: [
              { name: 'ID', type: 'number'},
              { name: 'Description', type: 'string'}
            ],
            url: base_url+'/api/warehouse_type/all'
    });

    return $("#grid_warehouse_type").jqxGrid({
            width: 400,
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
              { text: 'ID', datafield: 'ID', width: 200},
              { text: 'Description', datafield: 'Description', width: 200}
            ]
        });
  }
</script>