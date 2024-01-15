<?php $this->layout("layouts/base", ['title' => 'Item Master']); ?>

<h1>Item Master</h1>

<hr>



<div class="btn-panel">
  <button id="sync_defect" style="display: black;"><span class="glyphicon glyphicon-save"></span> Sync Master</button>
</div>

<div id="grid_item"></div>


<div>


</div>

<script>
  $(function() {
    grid_item();

    $('#sync_defect').on('click', function(event) {
      gojax('post', '/api/v1/item/sync').done(function(data) {
        alert(data.message);
        $('#grid_item').jqxGrid('updatebounddata');
      });
    });


  });

  function grid_item() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [{
          name: 'ID',
          type: 'string'
        },
        {
          name: 'NameTH',
          type: 'string'
        },
        {
          name: 'QtyPerPallet',
          type: 'number'
        },
        {
          name: 'ManualBatch',
          type: 'bool'
        },
        {
          name: 'CheckSerial',
          type: 'bool'
        },
        {
          name: 'Channel',
          type: 'number'
        },
        {
          name: 'ItemQ',
          type: 'string'
        }
      ],
      url: '/api/v2/item/all',
      filter: function(data) {
        $('#grid_item').jqxGrid('updatebounddata', 'filter');
      },
      updaterow: function(rowid, rowdata, commit) {
        gojax('post', '/api/v1/item/update_master', {
          itemId: rowdata.ID,
          manualBatch: rowdata.ManualBatch,
          checkSerial: rowdata.CheckSerial,
          channel: rowdata.Channel
        }).done(function(data) {
          // console.log(data);
        });
        commit(true);
      }
    });

    return $("#grid_item").jqxGrid({
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
      columns: [{
          text: 'Item ID',
          datafield: 'ID',
          editable: false,
          width: 120
        },
        {
          text: 'Item Q',
          datafield: 'ItemQ',
          editable: false,
          width: 120
        },
        {
          text: 'Item Name',
          datafield: 'NameTH',
          editable: false,
          width: 500
        },
        {
          text: 'Qty/Pallet',
          datafield: 'QtyPerPallet',
          editable: false,
          width: 100
        },
        {
          text: 'Channel',
          datafield: 'Channel',
          width: 100,
          editable: true,
          filterable: false
        },
        {
          text: 'Manual Batch',
          datafield: 'ManualBatch',
          columntype: 'checkbox',
          filtertype: 'bool',
          width: 140
        },
        {
          text: 'Don\'t Check Serial',
          datafield: 'CheckSerial',
          columntype: 'checkbox',
          filtertype: 'bool',
          width: 140
        }
      ]
    });
  }
</script>