<?php $this->layout("layouts/base", ['title' => 'Pallet Table']); ?>

<h1>Pallet Table</h1>
<hr>

<div style="margin-bottom: 20px;">
  <button id="pallet_line" class="btn btn-primary">Line</button>
  <button id="update_pallet" class="btn btn-info">Update</button>
  <button id="pallet_complete" class="btn btn-success">Complete</button>
  <button id="print_tag" class="btn btn-info">Print Tag</button>
</div>

<!-- <div style="margin-bottom: 20px; display: none;">
  <button id="create" class="btn btn-primary">Create</button>
  <button id="generate_lpn" class="btn btn-warning">Gen LPN Auto</button>
  <button id="update_lpn" class="btn btn-info">Update</button>
  <button id="lpn_line" class="btn btn-default">Line</button>
  <button id="complate_lpn" class="btn btn-success">Complete</button>
  <button id="print_goods_tag" class="btn btn-inverse">Print Tag</button>
  <button id="print_lpn" class="btn btn-inverse">Print LPN</button>
  <button id="delete_lpn" class="btn btn-danger">Delete LPN</button>
</div> -->

<div id="grid_pallet_table"></div>

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
        <input type="hidden" name="update_location_pallet">
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
<div class="modal" id="modal_pallet_line" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
          <span class="glyphicon glyphicon-remove"></span>
          Close
        </button>
        <h4 class="modal-title line-title"></h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div id="grid_pallet_line">

        </div>
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

  jQuery(document).ready(function ($) {
    grid_pallet_table();

    $('input[name=input_create_batch]').keydown(function (e) {
      if (e.which === 13) {
        gojax('post', '/p2/api/create_lpn_master', {
          item: $('input[name=input_create_item]').val(),
          batch: $('input[name=input_create_batch]').val()
        }).done(function (data) {
          alert(data.message);
          $('#modal_create').modal('hide');
          $('#grid_lpn').jqxGrid('updatebounddata');
        });
      }
    });

    $('#save_create_lpn').on('click', function () {
      gojax('post', '/p2/api/create_lpn_master', {
        item: $('input[name=input_create_item]').val(),
        batch: $('input[name=input_create_batch]').val()
      }).done(function (data) {
        alert(data.message);

        if (data.result === true) {
          $('#top_alert').show();
          $('#top_alert_message').text(data.message);
          $('#modal_alert').modal('hide');
        } else {
          $('#top_alert').hide();
          $('#modal_alert').modal({ backdrop: 'static' });
          $('#modal_alert_message').text(data.message);
        }

        $('#modal_create').modal('hide');
        $('#grid_lpn').jqxGrid('updatebounddata');
      });
    });

    $('#create').on('click', function () {
      $('#modal_create').modal({ backdrop: 'static' });
      $('input[name=input_create_item]').val('');
    });

    $('#select_item').on('click', function () {
      $('#modal_select_item').modal({ backdrop: 'static' });
      grid_select_item();
    })

    $('#grid_select_item').on('rowdoubleclick', function () {
      var rowdata = row_selected('#grid_select_item');
      if (typeof rowdata !== 'undefined') {
        $('input[name=input_create_item]').val(rowdata.ID);
        $('#modal_select_item').modal('hide');
        $('input[name=input_create_batch]').val('').focus();
      }
    });

    $('#generate_lpn').on('click', function () {
      if (confirm('Are you sure?')) {
        // $('#modal_loading').modal({backdrop: 'static'});
        $('#http-loading').show();
        gojax('post', '/api/v2/genauto').done(function (data) {
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
            $('#modal_alert').modal({ backdrop: 'static' });
            $('#modal_alert_message').text(data.message);
          }

          $('#grid_lpn').jqxGrid('updatebounddata');
        });
      }
    });

    $('#print_lpn').on('click', function () {
      var rowdata = row_selected('#grid_lpn');
      if (typeof rowdata !== 'undefined') {
        // console.log(grid_lpn_selected);
        window.open('/print/lpn/' + grid_lpn_selected.join(), '_blank');
      } else {
        alert('please select row.');
      }
    });

    $('#grid_lpn').on('rowselect', function (event) {
      var rowdata = row_selected('#grid_lpn');
      if (typeof rowdata !== 'undefined') {
        grid_lpn_selected.push(rowdata.LPNID);
      }
      // console.log(grid_lpn_selected);
    });

    $('#grid_lpn').on('rowunselect', function () {
      var rowdata = row_selected('#grid_lpn');
      if (typeof rowdata !== 'undefined') {
        remove_from_array(grid_lpn_selected, rowdata.LPNID);
      } else {
        grid_lpn_selected = [];
      }
      // console.log(grid_lpn_selected);
    });

    $('#print_goods_tag').on('click', function () {
      var rowdata = row_selected('#grid_lpn');
      if (typeof rowdata !== 'undefined') {
        window.open('/print/goods_tag/' + rowdata.LPNID, '_blank');
      } else {
        alert('please select row.');
      }
    });

    // $('#lpn_line').on('click', function() {
    //   var rowdata = row_selected('#grid_lpn');
    //   if (typeof rowdata !== 'undefined') {
    //     $('#modal_line').modal({backdrop: 'static'});

    //     $('#show_lpn_no').html('<b>LPN No. </b>' + rowdata.LPNID);
    //     $('#show_item_no').html('<b>Item No. </b>'+ rowdata.ItemID);
    //     $('#show_item_desc').html('<b>Description</b> ' + rowdata.ItemDesc);
    //     $('#show_batch_no').html('<b>Batch No. </b>' + rowdata.BatchNo);

    //     grid_lpn_line(rowdata.LPNID);
    //   } else {
    //     alert('please select row.');
    //   }
    // });

    $('#pallet_line').on('click', function () {
      var rowdata = row_selected('#grid_pallet_table');
      if (typeof rowdata !== 'undefined') {
        $('#modal_pallet_line').modal({ backdrop: 'static' });
        $('.line-title').html(rowdata.pallet_no);
        grid_pallet_line(rowdata.pallet_no);
      } else {
        alert('กรุณาเลือกข้อมูล');
      }
    });

    $('#update_pallet').on('click', function () {
      var rowdata = row_selected('#grid_pallet_table');
      // console.log(rowdata);
      if (typeof rowdata !== 'undefined' && rowdata.status === 'Completed') {
        $('#modal_update').modal({ backdrop: 'static' });

        $('#show_update_item').html('<b>Item : </b>' + rowdata.item_id);
        $('#show_update_batch').html('<b>Batch : </b>' + rowdata.batch_no);
        $('input[name=update_location_id_temp]').val(rowdata.location_id);
        $('input[name=update_location_id]').val(rowdata.location_id);
        $('input[name=update_location_desc]').val(rowdata.location);
        $('input[name=update_location_pallet]').val(rowdata.pallet_no);
        $('input[name=update_location_item]').val(rowdata.item_id);
      } else {
        alert('please select row or select complete only.');
      }
    });


    $('#show_update_select_location').on('click', function () {
      $('#modal_select_location').modal({ backdrop: 'static' });
      grid_select_location($('input[name=update_location_item]').val());
    });

    $('#grid_select_location').on('rowdoubleclick', function () {
      var rowdata = row_selected('#grid_select_location');
      if (typeof rowdata !== 'undefined') {
        $('input[name=update_location_id]').val(rowdata.ID);
        $('input[name=update_location_desc]').val(rowdata.Description);

        $('#modal_select_location').modal('hide');
      }
    });

    $('#save_update_location').on('click', function () {
      $('#http-loading').show();
      $('#save_update_location').prop('disabled', true);
      if ($('input[name=update_location_id]').val() === "") {
        alert('please select location.');
      } else {
        gojax('post', '/api/v1/wh_location/update_location', {
          location: $('input[name=update_location_id]').val(),
          location_temp: $('input[name=update_location_id_temp]').val(),
          pallet_no: $('input[name=update_location_pallet]').val()
        }).done(function (data) {
          $('#save_update_location').prop('disabled', false);
          $('#http-loading').hide();
          console.log(data);
          if (data.result === true) {
            $('#top_alert').show();
            $('#top_alert_message').text(data.message);
            $('#modal_alert').modal('hide');
            $('#modal_update').modal('hide');
          } else {
            $('#top_alert').hide();
            $('#modal_alert').modal({ backdrop: 'static' });
            $('#modal_alert_message').text(data.message);
          }

          // if (data.result === false) {
          //   alert(data.message);

          // } else {
          // $('#modal_update').modal('hide');
          $('#grid_pallet_table').jqxGrid('updatebounddata');
          // }
        });
      }
      // end
    });

    $('#pallet_complete').on('click', function () {
      var rowdata = row_selected('#grid_pallet_table');
      if (typeof rowdata !== 'undefined') {
        if (confirm('Are you sure ?')) {
          // $('#modal_loading').modal({backdrop: 'static'});
          $('#http-loading').show();
          gojax('post', '/api/v1/wh_location/pallet_complete', {
            pallet_no: rowdata.pallet_no
          }).done(function (data) {
            // $('#modal_loading').modal('hide');
            $('#http-loading').hide();

            if (data.result === true) {
              $('#top_alert').show();
              $('#top_alert_message').text('Complete Success.');
              $('#modal_alert').modal('hide');
            } else {
              $('#top_alert').hide();
              $('#modal_alert').modal({ backdrop: 'static' });
              $('#modal_alert_message').text(data.message);
            }

            $('#grid_pallet_table').jqxGrid('updatebounddata');
            // alert(data.message);
          });
        }
      } else {
        alert('please select row.');
      }
    });

    $('#confirm_location_update').on('click', function () {
      var rowdata = row_selected('#grid_select_location');
      if (typeof rowdata !== 'undefined') {
        $('input[name=update_location_id]').val(rowdata.ID);
        $('input[name=update_location_desc]').val(rowdata.Description);

        $('#modal_select_location').modal('hide');
      }
    });

    $('#delete_lpn').on('click', function () {
      var rowdata = row_selected('#grid_pallet_table');
      if (typeof rowdata !== 'undefined') {
        if (confirm('Are you sure ?')) {
          gojax('post', '/api/v2/delete_lpn', {
            lpnid: rowdata.LPNID
          }).done(function (data) {
            alert(data.message);
            $('#grid_pallet_table').jqxGrid('updatebounddata');
          });
        }
      } else {
        alert('please select row.');
      }
    });

    $("#grid_pallet_table").bind('rowselect', function (event) {
      if (Array.isArray(event.args.rowindex)) {
        if (event.args.rowindex.length > 0) {
          // alert("All rows selected");
          var __rowdata = $('#grid_pallet_table').jqxGrid('getrows');
          grid_lpn_selected = [];
          $.each(__rowdata, function (i, v) {
            grid_lpn_selected.push(v.LPNID);
          });
        } else {
          // alert("All rows unselected");
          grid_lpn_selected = [];
        }
      }
      // console.log(grid_lpn_selected);
    });

    $('#print_tag').on('click', function () {
      var rowdata = row_selected('#grid_pallet_table');
      if (typeof rowdata !== 'undefined') {
        window.open('/wh_location/tag/' + rowdata.pallet_no, '_blank');
      } else {
        alert('กรุณาเลือกรายการ');
      }
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

  function grid_pallet_line(pallet_no) {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [
        { name: 'barcode', type: 'string' },
        { name: 'username', type: 'string' },
        { name: 'create_date', type: 'date' }
      ],
      url: '/api/v1/wh_location/get_pallet_line/' + pallet_no
    });

    return $("#grid_pallet_line").jqxGrid({
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
        { text: 'Barcode', datafield: 'barcode', width: 150 },
        { text: 'Name', datafield: 'username', width: 100 },
        { text: 'Create Date', datafield: 'create_date', cellsformat: 'yyyy-MM-dd HH:mm', width: 200 }
      ]
    });
  }

  function grid_pallet_table() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [
        { name: 'pallet_no', type: 'string' },
        { name: 'item_id', type: 'string' },
        { name: 'item_name', type: 'string' },
        { name: 'batch_no', type: 'string' },
        { name: 'location', type: 'string' },
        { name: 'location_id', type: 'number' },
        { name: 'qty_per_pallet', type: 'number' },
        { name: 'qty_in_use', type: 'number' },
        { name: 'remain', type: 'number' },
        { name: 'status', type: 'string' },
        { name: 'company', type: 'string' },
        { name: 'create_by', type: 'string' },
        { name: 'create_date', type: 'date' },
        { name: 'update_by', type: 'string' },
        { name: 'update_date', type: 'date' },
        { name: 'complete_by', type: 'string' },
        { name: 'complete_date', type: 'string' }
      ],
      url: '/api/v1/wh_location/get_pallet_table'
    });

    return $("#grid_pallet_table").jqxGrid({
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
      selectionmode: 'checkbox',
      columns: [
        { text: 'Pallet No.', datafield: 'pallet_no', width: 150 },
        { text: 'Item', datafield: 'item_id', width: 100 },
        { text: 'Batch', datafield: 'batch_no', width: 100 },
        { text: 'Location', datafield: 'location', width: 100 },
        { text: 'QTY/Pallet', datafield: 'qty_per_pallet', width: 100 },
        { text: 'QTY in use', datafield: 'qty_in_use', width: 100 },
        { text: 'Remain', datafield: 'remain', width: 100 },
        { text: 'Status', datafield: 'status', width: 100 },
        { text: 'Company', datafield: 'company', width: 100 },
        { text: 'Create By', datafield: 'create_by', width: 100 },
        { text: 'Create Date', datafield: 'create_date', cellsformat: 'yyyy-MM-dd HH:mm', width: 150 },
        { text: 'Update By', datafield: 'update_by', width: 100 },
        { text: 'Update Date', datafield: 'update_date', cellsformat: 'yyyy-MM-dd HH:mm', width: 150 },
        { text: 'Complete By', datafield: 'complete_by', width: 100 },
        { text: 'Complete Date', datafield: 'complete_date', cellsformat: 'yyyy-MM-dd HH:mm', width: 200 }
      ]
    });
  }

  function grid_select_item() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [
        { name: 'ID', type: 'string' },
        { name: 'NameTH', type: 'string' }
      ],
      url: '/p2/api/all_item_fg'
    });

    return $("#grid_select_item").jqxGrid({
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
        { text: 'Item ID', datafield: 'ID', width: 100 },
        { text: 'Item Name', datafield: 'NameTH' }
      ]
    });
  }
</script>