<?php $this->layout("layouts/base", ['title' => 'Item Receive Location']); ?>

<h1>Item Receive Location</h1>
<hr>

<div style="margin-bottom: 20px;">
  <button id="line" class="btn btn-primary"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Line</button>
  <button id="update_location" class="btn btn-default"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Update</button>
</div>

<div id="grid_relc"></div>  

<!-- Modal Line-->
<div class="modal" id="modal_line" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
        	<span class="glyphicon glyphicon-remove"></span>
        	Close
        </button>
        <h4 class="modal-title">Line</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div style="font-weight: bold;"> Location : <input type="text" name="line_location" readonly> </div>
        <input type="hidden" name="line_location_id" value=""> 
        <br>  
        <div>
          <button class="btn btn-primary" id="create_line"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create</button>
          <button class="btn btn-default" id="update"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Update</button>
          <button class="btn btn-danger" id="delete_line"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete</button>
        </div>
        
        <hr>
        <div id="grid_item">
          
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Update -->
<div class="modal" id="modal_line_form" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
        	<span class="glyphicon glyphicon-remove"></span>
        	Close
        </button>
        <h4 class="modal-title">Update</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
          <div style="font-weight: bold;"> Location : <input type="text" name="line_location_for_update" readonly> </div>
          <br>  
          <b>QTY</b> : <input type="text" name="qty" >

          <hr>

          <button class="btn btn-primary" id="update_qty">OK</button>
          <button class="btn btn-default" id="cancel_update_item">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal select item -->
<div class="modal" id="modal_select_item" tabindex="-1" role="dialog">
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
        <div id="grid_select_item">
          
        </div>
        <hr>
        <input type="hidden" name="_type">
        <button id="save_line" class="btn btn-success"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Save</button>
      </div>
    </div>
  </div>
</div>

<script>
jQuery(document).ready(function($) {
  grid_relc();

  $('#line').on('click', function() {
    var rowdata = row_selected('#grid_relc');
    if (typeof rowdata !== 'undefined') {
      $('#modal_line').modal({backdrop: 'static'});
      // alert(rowdata.ID);
      $('input[name=line_location]').val(rowdata.Description);
      $('input[name=line_location_id]').val(rowdata.ID);
      grid_item(rowdata.ID);
    } else {
      alert('please select row.');
    }
    
  });

  $('#cancel_update_item').on('click', function() {
    $('input[name=qty]').val('').focus();
  });

  $('#update_location').on('click', function() {
    var rowdata = row_selected('#grid_relc');
    if (typeof rowdata !== 'undefined') {
      $('#modal_line_form').modal({backdrop: 'static'});
      $('input[name=line_location_id]').val(rowdata.ID);
      $('input[name=line_location_for_update]').val(rowdata.Description);
      setInt('input[name=qty]');
      
      $('input[name=qty]').val('').focus();
    } else {
      alert('please select row.');
    }
  });

  $('#update').on('click', function() {
    var rowdata = row_selected('#grid_item');
    if (typeof rowdata !== 'undefined') {
      $('#modal_select_item').modal({backdrop: 'static'});
      $('input[name=_type]').val('update');
      grid_select_item();
      // $('input[name=line_location_for_update]').val($('input[name=line_location]').val());
      // setInt('input[name=qty]');
      // $('input[name=qty]').val('').focus();
    } else {
      alert('please select row.');
    }
  });

  $('#create_line').on('click', function() {
    $('#modal_select_item').modal({backdrop: 'static'});
    $('input[name=_type]').val('create');
    grid_select_item();
  });

  $('#save_line').on('click', function() {
    var rowdata = row_selected('#grid_select_item'), 
        new_item = '',
        item_receive_location_id = '',
        save_type = '';
    
    if (typeof rowdata !== 'undefined') {
      new_item = rowdata.ID;
      item_receive_location_id = $('input[name=line_location_id]').val();
      save_type = $('input[name=_type]').val();

      if (save_type === 'create') {

        gojax('post', '/p2/api/create_item_receive_location', {
          item_id: new_item,
          id: '',
          location_id: item_receive_location_id,
          type: save_type
        }).done(function(data) {
          if (data.result === false) {
            alert(data.message);
          } else {
            $('#grid_item').jqxGrid('updatebounddata');
            $('#modal_select_item').modal('hide');
          }
        });

      } else {

        var rowdata_item = row_selected('#grid_item');

        if (typeof rowdata_item !== 'undefined') {

          gojax('post', '/p2/api/create_item_receive_location', {
            item_id: new_item,
            id: rowdata_item.ID,
            location_id: item_receive_location_id,
            type: save_type
          }).done(function(data) {
            if (data.result === false) {
              alert(data.message);
            } else {
              $('#grid_item').jqxGrid('updatebounddata');
              $('#modal_select_item').modal('hide');
            }
          });

        } else {
          alert('please select row!s');
        }
      
      }
    } else {
      alert('please select item!');
    }

    // var rowdata_item = row_selected('#grid_item');

    // if (typeof rowdata !== 'undefined') {
    //   if (typeof rowdata_item !== 'undefined') {

    //     if ($('input[name=_type]').val() !== 'create') {
    //       gojax('post', '/p2/api/create_item_receive_location', {
    //         item_id: rowdata.ID,
    //         id: rowdata_item.ID,
    //         location_id: rowdata_item.LocationID,
    //         type: $('input[name=_type]').val()
    //       }).done(function(data) {
    //         if (data.result === false) {
    //           alert(data.message);
    //         } else {
    //           $('#grid_item').jqxGrid('updatebounddata');
    //           $('#modal_select_item').modal('hide');
    //         }x
    //       });
    //     } else {
    //       alert('please select row');
    //     }

    //   } else {
    //     gojax('post', '/p2/api/create_item_receive_location', {
    //       item_id: rowdata.ID,
    //       id: '',
    //       location_id: $('input[name=line_location_id]').val(),
    //       type: $('input[name=_type]').val()
    //     }).done(function(data) {
    //       $('#grid_item').jqxGrid('updatebounddata');
    //       $('#modal_select_item').modal('hide');
    //     });
    //   }
    // } else {
    //   alert('please select row');
    // }
  });

  $('#delete_line').on('click', function() {
    var rowdata = row_selected('#grid_item');

    if (typeof rowdata !== 'undefined') {
      if (confirm('Are you sure?')) {
        gojax('post', '/p2/api/delete_item_receive_location', {
          id: rowdata.ID
        }).done(function(data) {
          if (data.result === false) {
            alert(data.message);
          }
          $('#grid_item').jqxGrid('updatebounddata');
        });
      }
    } else {
      alert('please select row');
    }
  });

  $('#update_qty').on('click', function() {
    var location = $('input[name=line_location_id]').val();
    gojax('post', '/api/v2/item_receive_location/update_qty', {
      qty: $('input[name=qty]').val(),
      location: location
    }).done(function(data) {
      $("#modal_line_form").modal('hide');
      alert(data.message);
      $('#grid_relc').jqxGrid('updatebounddata');
    });
  });
});

function grid_relc() {
  var dataAdapter = new $.jqx.dataAdapter({
    datatype: 'json',
        datafields: [
          { name: 'ID', type: 'number'},
          { name: 'Description', type: 'string' },
          { name: 'QTY', type: 'number'},
          { name: 'QTYInUse', type: 'number'},
          { name: 'Remain', type: 'number'}
        ],
        url: '/p2/api/all_location'
  });

  return $("#grid_relc").jqxGrid({
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
          { text: 'Description', datafield: 'Description', width: 100},
          { text: 'QTY', datafield: 'QTY', width: 100},
          { text: 'QTY In Use', datafield: 'QTYInUse', width: 100},
          { text: 'Remain', datafield: 'Remain', width: 100}
        ]
    });
}

function grid_item(location_id) {
  var dataAdapter = new $.jqx.dataAdapter({
    datatype: 'json',
        datafields: [
          { name: 'ID', type: 'number'},
          { name: 'ItemID', type: 'string' },
          { name: 'Location',type: 'string'}
        ],
        url: '/p2/api/all_item_receive_location?location_id=' + location_id
  });

  return $("#grid_item").jqxGrid({
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
          { text: 'Location', datafield: 'Location', width: 100},
          { text: 'Item ID', datafield: 'ItemID', width: 100}
        ]
    });
}

function grid_select_item() {

	var dataAdapter = new $.jqx.dataAdapter({
		datatype: 'json',
	        datafields: [
	        	{ name: 'ID', type: 'string'},
            { name: 'NameTH', type: 'string'}
	        ],
	        url: '/p2/api/all_item_fg'
	});

	return $("#grid_select_item").jqxGrid({
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
	          { text: 'Item ID', datafield: 'ID', width: 100},
            { text: 'Item Name', datafield: 'NameTH'}
	        ]
	    });
}
</script>