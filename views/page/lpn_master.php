<?php $this->layout("layouts/base", ['title' => 'LPN Master']); ?>

<h1>LPN Master</h1>
<hr>

<div style="margin-bottom: 20px;">
  <button id="create" class="btn btn-primary">Create</button>
  <button id="generate_lpn" class="btn btn-warning">Gen LPN Auto</button>
  <button id="update_lpn" class="btn btn-info">Update</button>
  <button id="lpn_line" class="btn btn-default">Line</button>
  <button id="complate_lpn" class="btn btn-success">Complete</button>
  <button id="print_goods_tag" class="btn btn-inverse">Print Tag</button>
  <button id="print_lpn" class="btn btn-inverse">Print LPN</button>
  <button id="delete_lpn" class="btn btn-danger">Delete LPN</button>
</div>

<div id="grid_lpn"></div>

<!-- Modal Create -->
<div class="modal" id="modal_create" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
        	<span class="glyphicon glyphicon-remove"></span>
        	Close
        </button>
        <h4 class="modal-title">Create</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div class="form-group">
          <label for="input_create_item">Item</label>
          <div class="input-group">
            <input type="text" name="input_create_item" class="form-control inputs" readonly>
            <span class="input-group-btn">
              <button class="btn btn-info" id="select_item">เลือก Item</button>
            </span>
          </div>
        </div>
        <div class="form-group">
          <label for="input_create_batch">Batch No.</label>
          <input type="text" name="input_create_batch" class="form-control inputs">
        </div>
        <input type="button" id="save_create_lpn" class="btn btn-primary" value="Save">
        <input type="button" class="btn btn=default" value="Cancel">
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
        <div id="grid_select_item"></div>
        <!-- <hr>
        <button id="confirm_item_selected" class="btn btn-success">Save</button> -->
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal" id="modal_line" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
        	<span class="glyphicon glyphicon-remove"></span>
        	Close
        </button>
        <h4 class="modal-title">LPN Line</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div id="show_lpn_no"></div>
        <div id="show_item_no"></div>
        <div id="show_item_desc"></div>
        <div id="show_batch_no"></div>


        <div id="grid_lpn_line" style="margin-top: 20px;"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal" id="modal_update" tabindex="-1" role="dialog">
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
        <div id="show_update_item"></div>
        <div id="show_update_batch"></div>
        <br>
        <input type="hidden" name="update_location_id">
        <input type="hidden" name="update_location_lpn">
        <input type="hidden" name="update_location_item">
        <input type="hidden" name="update_location_id_temp">
        Location <input type="text" name="update_location_desc" readonly> <button class="btn btn-default" id="show_update_select_location">เลือก</button>

        <br>
        <button class="btn btn-primary" id="save_update_location">บันทึก</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal" id="modal_select_location" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
          <span class="glyphicon glyphicon-remove"></span>
          Close
        </button>
        <h4 class="modal-title">Select Location</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div id="grid_select_location"></div>
        <br>
        <button class="btn btn-primary" id="confirm_location_update"> ยืนยัน </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal" id="modal-loading" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        
        <h4 class="modal-title">Message</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        กำลังประมวลผล...
      </div>
    </div>
  </div>
</div>

<script>

var grid_lpn_selected = [];

jQuery(document).ready(function($) {
  grid_lpn();

  $('input[name=input_create_batch]').keydown(function(e) {
    if (e.which === 13) {
      gojax('post', '/p2/api/create_lpn_master', {
        item: $('input[name=input_create_item]').val(),
        batch: $('input[name=input_create_batch]').val()
      }).done(function(data) {
        alert(data.message);
        $('#modal_create').modal('hide');
        $('#grid_lpn').jqxGrid('updatebounddata');
      });
    }
  });

  $('#save_create_lpn').on('click', function() {
    gojax('post', '/p2/api/create_lpn_master', {
      item: $('input[name=input_create_item]').val(),
      batch: $('input[name=input_create_batch]').val()
    }).done(function(data) {
      alert(data.message);

      if (data.result === true) {
        $('#top_alert').show();
        $('#top_alert_message').text(data.message);
        $('#modal_alert').modal('hide');
      } else {
        $('#top_alert').hide();
        $('#modal_alert').modal({backdrop: 'static'});
        $('#modal_alert_message').text(data.message);
      }

      $('#modal_create').modal('hide');
      $('#grid_lpn').jqxGrid('updatebounddata');
    });
  });

  $('#create').on('click', function() {
    $('#modal_create').modal({backdrop: 'static'});
    $('input[name=input_create_item]').val('');
  });

  $('#select_item').on('click', function() {
    $('#modal_select_item').modal({backdrop: 'static'});
    grid_select_item();
  })

  $('#grid_select_item').on('rowdoubleclick', function() {
    var rowdata = row_selected('#grid_select_item');
    if (typeof rowdata !== 'undefined') {
      $('input[name=input_create_item]').val(rowdata.ID);
      $('#modal_select_item').modal('hide');
      $('input[name=input_create_batch]').val('').focus();
    }
  });

  $('#generate_lpn').on('click', function() {
    if (confirm('Are you sure?')) {
      // $('#modal_loading').modal({backdrop: 'static'});
      $('#http-loading').show();
      gojax('post', '/api/v2/genauto').done(function(data) {
        // $('#modal_loading').modal('hide');
        $('#http-loading').hide();
        // console.log(data);
        // alert(data.message);
        if (data.result === true) {
          $('#top_alert').show();
          $('#top_alert_message').text(data.message);
          $('#modal_alert').modal('hide');
        } else {
          $('#top_alert').hide();
          $('#modal_alert').modal({backdrop: 'static'});
          $('#modal_alert_message').text(data.message);
        }

        $('#grid_lpn').jqxGrid('updatebounddata');
      });
    }
  });

  $('#print_lpn').on('click', function() {
    var rowdata = row_selected('#grid_lpn');
    if (typeof rowdata !== 'undefined') {
      // console.log(grid_lpn_selected);
      window.open('/print/lpn/' + grid_lpn_selected.join(), '_blank'); 
    } else {
      alert('please select row.');
    }
  });

  $('#grid_lpn').on('rowselect', function(event) {
    var rowdata = row_selected('#grid_lpn');
    if (typeof rowdata !== 'undefined') {
      grid_lpn_selected.push(rowdata.LPNID);
    }
    // console.log(grid_lpn_selected);
  });

  $('#grid_lpn').on('rowunselect', function() {
    var rowdata = row_selected('#grid_lpn');
    if (typeof rowdata !== 'undefined') {
      remove_from_array(grid_lpn_selected, rowdata.LPNID);
    } else {
      grid_lpn_selected = [];
    }
    // console.log(grid_lpn_selected);
  });

  $('#print_goods_tag').on('click', function() {
    var rowdata = row_selected('#grid_lpn');
    if (typeof rowdata !== 'undefined') {
      window.open('/print/goods_tag/' + rowdata.LPNID, '_blank'); 
    } else {
      alert('please select row.');
    }
  });

  $('#lpn_line').on('click', function() {
    var rowdata = row_selected('#grid_lpn');
    if (typeof rowdata !== 'undefined') {
      $('#modal_line').modal({backdrop: 'static'});

      $('#show_lpn_no').html('<b>LPN No. </b>' + rowdata.LPNID);
      $('#show_item_no').html('<b>Item No. </b>'+ rowdata.ItemID);
      $('#show_item_desc').html('<b>Description</b> ' + rowdata.ItemDesc);
      $('#show_batch_no').html('<b>Batch No. </b>' + rowdata.BatchNo);

      grid_lpn_line(rowdata.LPNID);
    } else {
      alert('please select row.');
    }
  });

  $('#update_lpn').on('click', function() {
    var rowdata = row_selected('#grid_lpn');
    if (typeof rowdata !== 'undefined' && rowdata.Status === 'Completed') {
      $('#modal_update').modal({ backdrop: 'static' });

      $('#show_update_item').html('<b>Item : </b>' + rowdata.ItemID);
      $('#show_update_batch').html('<b>Batch : </b>' + rowdata.BatchNo);
      $('input[name=update_location_id_temp]').val(rowdata.LocationID);
      $('input[name=update_location_id]').val(rowdata.LocationID);
      $('input[name=update_location_desc]').val(rowdata.Location);
      $('input[name=update_location_lpn]').val(rowdata.LPNID);
      $('input[name=update_location_item]').val(rowdata.ItemID);
    } else {
      alert('please select row or select complete only.');
    }

    
    
  });


  $('#show_update_select_location').on('click', function() {
    $('#modal_select_location').modal({backdrop: 'static'});
    grid_select_location($('input[name=update_location_item]').val());
  });

  $('#grid_select_location').on('rowdoubleclick', function() {
    var rowdata = row_selected('#grid_select_location');
    if (typeof rowdata !== 'undefined') {
      $('input[name=update_location_id]').val(rowdata.ID);
      $('input[name=update_location_desc]').val(rowdata.Description);
      
      $('#modal_select_location').modal('hide');
    }
  });

  $('#save_update_location').on('click', function() {
    $('#http-loading').show();
    $('#save_update_location').prop('disabled', true);
    if ($('input[name=update_location_id]').val() === "") {
      alert('please select location.');
    } else {
      gojax('post', '/api/v2/save_update_location', {
        location: $('input[name=update_location_id]').val(),
        location_temp: $('input[name=update_location_id_temp]').val(),
        lpn: $('input[name=update_location_lpn]').val()
      }).done(function(data) {
        $('#save_update_location').prop('disabled', false);
        $('#http-loading').hide();


        if (data.result === true) {
          $('#top_alert').show();
          $('#top_alert_message').text(data.message);
          $('#modal_alert').modal('hide');
        } else {
          $('#top_alert').hide();
          $('#modal_alert').modal({backdrop: 'static'});
          $('#modal_alert_message').text(data.message);
        }

        // if (data.result === false) {
        //   alert(data.message);
          
        // } else {
          // $('#modal_update').modal('hide');
          $('#grid_lpn').jqxGrid('updatebounddata');
        // }
      });
    }
    // end
  });

  $('#complate_lpn').on('click', function() {
    var rowdata = row_selected('#grid_lpn');
    if (typeof rowdata !== 'undefined') {
      if (confirm('Are you sure ?')) {
        // $('#modal_loading').modal({backdrop: 'static'});
        $('#http-loading').show();
        gojax('post', '/api/v2/set_lpn_complete', {
          lpn: rowdata.LPNID,
          item: rowdata.ItemID
        }).done(function(data) {
          // $('#modal_loading').modal('hide');
          $('#http-loading').hide();

          if (data.result === true) {
            $('#top_alert').show();
            $('#top_alert_message').text('Complete LPN Location [' + data.location + '] Success.');
            $('#modal_alert').modal('hide');
          } else {
            $('#top_alert').hide();
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text(data.message);
          }

          $('#grid_lpn').jqxGrid('updatebounddata');
          // alert(data.message);
        });
      }
    } else {
      alert('please select row.');
    }
  });

  $('#confirm_location_update').on('click', function() {
    var rowdata = row_selected('#grid_select_location');
    if (typeof rowdata !== 'undefined') {
      $('input[name=update_location_id]').val(rowdata.ID);
      $('input[name=update_location_desc]').val(rowdata.Description);
      
      $('#modal_select_location').modal('hide');
    }
  });

  $('#delete_lpn').on('click', function() {
    var rowdata = row_selected('#grid_lpn');
    if (typeof rowdata !== 'undefined') {
      if (confirm('Are you sure ?')) {
        gojax('post', '/api/v2/delete_lpn', {
          lpnid: rowdata.LPNID
        }).done(function(data) {
          alert(data.message);
          $('#grid_lpn').jqxGrid('updatebounddata');
        });
      }
    } else {
      alert('please select row.');
    }
  });

  $("#grid_lpn").bind('rowselect', function (event) {
    if (Array.isArray(event.args.rowindex)) {
      if (event.args.rowindex.length > 0) {
        // alert("All rows selected");
        var __rowdata = $('#grid_lpn').jqxGrid('getrows');
        grid_lpn_selected = [];
        $.each(__rowdata, function(i, v) {
          grid_lpn_selected.push(v.LPNID);
        });
      } else {
        // alert("All rows unselected");
        grid_lpn_selected = [];
      } 
    }
    // console.log(grid_lpn_selected);
  });
});


function grid_select_location(item) {
  var dataAdapter = new $.jqx.dataAdapter({
    datatype: 'json',
    datafields: [
      { name: 'ID', type: 'string' },
      { name: 'Description', type: 'string' }
    ],
    url: '/api/v2/location_by_type?item=' + item
  });

  return $("#grid_select_location").jqxGrid({
    width: '100%',
    source: dataAdapter,
    autoheight: true,
    pageSize: 10,
    altrows: true,
    pageable: true,
    sortable: true,
    filterable: true,
    showfilterrow: true,
    columnsresize: true,
    columns: [
      { text: 'Location ID', datafield: 'ID', width: 150 },
      { text: 'Location Description', datafield: 'Description' }
    ]
  });
}

function grid_lpn_line(id) {
  var dataAdapter = new $.jqx.dataAdapter({
    datatype: 'json',
    datafields: [
      { name: 'Barcode', type: 'string' },
      { name: 'Name', type: 'string' }
    ],
    url: '/api/v2/get_lpn_line?id=' + id
  });

  return $("#grid_lpn_line").jqxGrid({
    width: '100%',
    source: dataAdapter,
    autoheight: true,
    pageSize: 10,
    altrows: true,
    pageable: true,
    sortable: true,
    filterable: true,
    showfilterrow: true,
    columnsresize: true,
    columns: [
      { text: 'Barcode', datafield: 'Barcode', width: 150 },
      { text: 'Name', datafield: 'Name' }
    ]
  });
}

function grid_lpn() {
	var dataAdapter = new $.jqx.dataAdapter({
		datatype: 'json',
    datafields: [
      { name: 'LPNID', type: 'string'},
      { name: 'ItemID', type: 'string'},
      { name: 'ItemDesc', type: 'string'},
      { name: 'BatchNo', type: 'string'},
      { name: 'Location', type: 'string'},
      { name: 'LocationID', type: 'number'},
      { name: 'QtyPerPallet', type: 'number'},
      { name: 'QtyInUse', type: 'number'},
      { name: 'Remain', type: 'number'},
      { name: 'Status', type: 'string'},
      { name: 'CompleteDate', type: 'date'},
      { name: 'CreateDate', type: 'date'},
      { name: 'CreateBy', type: 'string'},
      { name: 'Company', type: 'string'},
      { name: 'UpdateDate', type: 'date'},
      { name: 'UpdateBy', type: 'string'}
    ],
    url: '/api/v2/lpn_all'
	});

	return $("#grid_lpn").jqxGrid({
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
    selectionmode: 'checkbox',
    columns: [
      { text: 'LPN ID', datafield: 'LPNID', width: 150},
      { text: 'Item', datafield: 'ItemID', width: 100},
      { text: 'Batch', datafield: 'BatchNo', width: 100},
      { text: 'Location', datafield: 'Location', width:  100},
      { text: 'QTY/Pallet', datafield: 'QtyPerPallet', width: 100},
      { text: 'QTY in use', datafield: 'QtyInUse', width: 100},
      { text: 'Remain', datafield: 'Remain', width: 100},
      { text: 'Status', datafield: 'Status', width: 100},
      { text: 'CompleteDate', datafield: 'CompleteDate', cellsformat: 'yyyy-MM-dd HH:mm', width: 150 },
      { text: 'CreateDate', datafield: 'CreateDate', cellsformat: 'yyyy-MM-dd HH:mm', width: 150 },
      { text: 'CreateBy', datafield: 'CreateBy', width: 100 },
      { text: 'Company', datafield: 'Company', width: 100 },
      { text: 'UpdateDate', datafield: 'UpdateDate', cellsformat: 'yyyy-MM-dd HH:mm', width: 150},
      { text: 'UpdateBy', datafield: 'UpdateBy', width: 100}
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