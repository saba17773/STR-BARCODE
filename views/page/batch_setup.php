<?php $this->layout("layouts/base", ['title' => 'Batch Setup']); ?>

<h1>Batch Setup</h1>

<hr>

<div class="well well-sm" style="font-size: 1.3em;">
  <input type="checkbox" name="active_batch_setup" id="active_batch_setup"> <b>เปิดใช้งาน?</b>
</div>

<div style="padding-bottom: 20px;">
  <button class="btn btn-primary" id="create">Create</button>
  <button class="btn btn-info" id="update">Update</button>
</div>

<div id="grid_batch"></div>

<!-- Modal -->
<div class="modal" id="modal_update" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
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
        <div class="form-group">
          <label>Format Batch</label>
          <input type="text" name="format_batch" class="form-control" required>  

          <!-- <div class="well" id="demo_format_batch" style="margin-top: 20px;">
            
          </div> -->
        </div>

        <div class="form-group">
          <label>From Date</label>
          <input type="text" name="from_date" class="form-control" required>
        </div>

        <div class="form-group">
          <label>To Date</label>
          <input type="text" name="to_date" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="product_group">Product Group</label>
          <select name="product_group" id="product_group" class="form-control">
            <option value="TBR">TBR</option>
            <option value="RDT">PCR</option>
          </select>
        </div>

        <input type="hidden" name="setup_id" value="">
        <input type="hidden" name="form_type">
        <button class="btn btn-primary" id="save_update">Save</button>
        <button class="btn btn-info" id="reset_update">Reset</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(function() {
    grid_batch();

    gojax('post', '/api/v2/batch/is_active').done(function(data) {
      if (data.result === true) {
        $('#active_batch_setup').prop('checked', true);
      } else {
        $('#active_batch_setup').prop('checked', false);
      }
    });

    $('#create').on('click', function() {
      $('#modal_update').modal({backdrop: 'static'});
      $('.modal-title').text('Create');
      $('input[name=form_type]').val('create');

      $('input[name=from_date]').datetimepicker({
        timeFormat: "hh:mm tt"
      });

      $('input[name=to_date]').datetimepicker({
        timeFormat: "hh:mm tt"
      });

      $('input[name=to_date]').val('');
      $('input[name=from_date]').val('');
      $('input[name=format_batch]').val('').focus();
      // if (confirm('Create new setup ?')) {
      //   gojax('post', '/api/v2/batch/create_new_setup').done(function(data) {
      //     $('#grid_batch').jqxGrid('updatebounddata');
      //   });
      // }
    });

    $('#update').on('click', function() {
      var rowdata = row_selected('#grid_batch');
      if (typeof rowdata !== 'undefined') {
        $('#modal_update').modal({backdrop: 'static'});
        $('.modal-title').text('Update');
        $('input[name=form_type]').val('update');

        $('input[name=from_date]').datetimepicker({
          timeFormat: "hh:mm TT",
          dateFormat: "yy-mm-dd"
        });

        $('input[name=to_date]').datetimepicker({
          timeFormat: "hh:mm TT",
          dateFormat: "yy-mm-dd"
        });


        // $('#demo_format_batch').html('<b>ตัวอย่าง : </b> 20XX-XX');
        $('input[name=setup_id]').val(rowdata.ID);

        $('input[name=to_date]').val( moment(rowdata.ToDate).format('YYYY-MM-DD H:mm A') );
        $('input[name=from_date]').val(moment(rowdata.FromDate).format('YYYY-MM-DD H:mm A'));
        $('input[name=format_batch]').val(rowdata.FormatBatch).focus();
      } else {
        alert('please select row.');
      }
    });

    $('#reset_update').on('click', function() {
      $('input[name=to_date]').val('');
      $('input[name=from_date]').val('');
      $('input[name=format_batch]').val('').focus();
    });

    $('input[name=format_batch]').keyup(function () {
      $('#demo_format_batch').html('<b>ตัวอย่าง : </b> ' + $('input[name=format_batch]').val());
    });

    $('#save_update').on('click', function() {
      gojax('post', '/api/v2/batch/save_batch_setup', {
        format: $('input[name=format_batch]').val(),
        from_date: $('input[name=from_date]').val(),
        to_date: $('input[name=to_date]').val(),
        setup_id: $('input[name=setup_id]').val(),
        form_type: $('input[name=form_type]').val(),
        product_group: $('select[name=product_group]').val()
      }).done(function(data) {
        $('#modal_update').modal('hide');
        $('#grid_batch').jqxGrid('updatebounddata');
        alert(data.message)
      });
    });

    $('#active_batch_setup').on('click', function() {
      var _status_batch_setup = $('#active_batch_setup:checked').val();
      var __value_status = 0;
      if (typeof _status_batch_setup === 'undefined') {
        __value_status = 0;
      } else {
        __value_status = 1;
      }

      gojax('post', '/api/v2/batch/set_batch_setup_active', {
        status: __value_status
      }).done(function(data) {
        alert(data.message);
      });
    });
  });

  function grid_batch() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [
        { name: 'ID', type: 'number' },
        { name: 'FormatBatch', type: 'string' },
        { name: 'FromDate', type: 'date' },
        { name: 'ToDate', type: 'date'},
        { name: 'ProductGroup', type: 'string'},
        { name: 'Active', type: 'bool'}
      ],
      url: '/api/v2/batch/all',
      updaterow: function (rowid, rowdata, commit) {
        gojax('post', '/api/v1/batch/active_setup', {
          id: rowdata.ID,
          active: rowdata.Active,
          product_group: rowdata.ProductGroup
        }).done(function (data) {
          // console.log(data);
          $('#grid_batch').jqxGrid('updatebounddata', 'cells');
        });
        commit(true);
      }
    });

    return $("#grid_batch").jqxGrid({
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
      editable: true,
      columns: [
        { text: 'Format Batch', datafield: 'FormatBatch', editable: false, width: 100 },
        { text: 'From Date', datafield: 'FromDate', cellsformat: 'yyyy-MM-dd HH:mm', editable: false, width: 200 },
        { text: 'To Date', datafield: 'ToDate', cellsformat: 'yyyy-MM-dd HH:mm', editable: false, width: 200},
        { text: 'Product Group', datafield: 'ProductGroup', width: 100, editable: false},
        { text: 'Active', datafield: 'Active', columntype: 'checkbox', filtertype: 'bool', width: 100 }
      ]
    });
  }
</script>