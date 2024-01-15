<?php $this->layout("layouts/base", ['title' => 'Pallet Master']); ?>

<h1>Pallet Master</h1>

<div style="margin: 20px 0;">
  <button class="btn btn-primary" id="create">Create</button>
  <button class="btn btn-info" id="print">Print</button>
</div>

<div id="grid"></div>

<!-- Create Modal -->
<div class="modal" id="modal_new_pallet" tabindex="-1" role="dialog"> 
  
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">New Pallet</h4>
      </div>
      <div class="modal-body">
        <form id="formNewPallet">
          <h1>จำนวน Pallet ปัจจุบัน : <span id="latestPalletNo"></span></h1>
          <div class="form-group">
            <label for="palletQty">ใส่จำนวน Pallet</label>
            <input type="number" id="palletQty" name="palletQty" class="form-control">
          </div>

          <button class="btn btn-primary" id="btnSubmitNewPallet" type="submit">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  jQuery(document).ready(function ($) {

    grid();

    $('#create').on('click', function () {
      $('#modal_new_pallet').modal({ backdrop: 'static' });
      $('#palletQty').val('').focus();

      setInterval(function () {
        gojax('get', '/api/v1/wh_location/last_seq').done(function (lastSeq) {
          $('#latestPalletNo').html(lastSeq);
        });
      }, 1000);

    });

    $('#print').on('click', function () {
      var rowdata = row_selected('#grid');
      if ( typeof rowdata !== 'undefined') {
        // console.log(rowdata);
        window.open('/wh_location/print/pallet/' + rowdata.pallet_no, '_blank');
      } else {
        alert('กรุณาเลือกรายการ');
      }
    });

    
  });

  $('#formNewPallet').submit(function (e) {
    e.preventDefault();

    var qty = $('#palletQty').val();

    $('#btnSubmitNewPallet').prop('disabled', true).text('กำลังประมวลผล...');

    if (!!qty) {
      gojax('post', '/api/v1/wh_location/create_pallet', {
        qty: qty
      }).done(function (data) {
        $('#btnSubmitNewPallet').prop('disabled', false).text('Submit');
        $('#palletQty').val('').focus();
        if (data.result === true) {
          alert(data.message);
          $('#grid').jqxGrid('updatebounddata');
        } else {
          alert(data.message);
        }
      });
    }
  });

  function grid() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [
        { name: 'id', type: 'number' },
        { name: 'pallet_no', type: 'string' },
        { name: 'pallet_status', type: 'bool' },
        { name: 'pallet_item', type: 'string' }
      ],
      url: "/api/v1/wh_location/get_all_pallet",
      updaterow: function (rowid, rowdata, commit) {
        //  
        commit(false);
      },
    });

    return $("#grid").jqxGrid({
      width: '100%',
      source: dataAdapter,
      autoheight: true,
      pageSize: 10,
      // rowsheight : 40,
      // columnsheight : 40,
      altrows: true,
      pageable: true,
      sortable: true,
      filterable: true,
      showfilterrow: true,
      columnsresize: true,
      editable: false,
      // theme: 'theme',
      columns: [
        { text: 'Pallet No', datafield: 'pallet_no', width: 150 },
        { text: 'Item', datafield: 'pallet_item', width: 100 },
        { text: 'Status', datafield: 'pallet_status', filtertype: 'bool', columntype: 'checkbox', width: 100 }
      ]
    });
  }
</script>