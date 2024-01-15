<?php $this->layout("layouts/base", ['title' => 'Journal PCR']); ?>

<div class="head-space">
  <h1>Journal PCR</h1>
  <hr>
  <p>
    <form id="formCreateJournal" class="well">
      Description <input type="text" name="journal_description" id="journal_description" autocomplete="off" > Truck 
      <select name="journal_truck" id="journal_truck" required></select>
      <button id="btn_create_journal" class="btn btn-primary" type="submit">Submit</button>
    </form>
  </p>
  <p>
    <button id="line" class="btn btn-primary">Line</button>

    <?php $detect = new \Mobile_Detect;
    if ($detect->isMobile()) { ?>
      <input type="hidden" name="device" value="mobile">
      <button id="complete_journal" class="btn btn-success">Complete</button>
    <?php } else { ?>
      <input type="hidden" name="device" value="desktop">
      <button id="print_journal_detail" class="btn btn-success">Print</button>
    <?php
    } ?>


  </p>
  <p>
    <div id="grid"></div>
  </p>

  <!-- Modal เลือก item set-->
  <div class="modal" id="modal_line" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
            <span class="glyphicon glyphicon-remove"></span>
            Close
          </button>
          <h4 class="modal-title">Item Set</h4>
        </div>
        <div class="modal-body">
          <!-- Content -->
          <div id="grid_line"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal" id="modal_inprogress" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          กำลังประมวลผล...
        </div>
      </div>
    </div>
  </div>

  <!-- <div id="dialog_line" title="Line" style="display: none;">
    <div id="grid_line"></div>
  </div> -->
</div>

<?php $this->push('scripts'); ?>
<script>
  jQuery(document).ready(function($) {

    grid();

    gojax('get', '/api/v1/transfer/get_truck')
      .done(function(data) {
        $('#journal_truck').html("<option value=''>= เลือก =</option>");
        $.each(data, function(i, v) {
          $('#journal_truck').append('<option value-"' + v.ID + '">' + v.PlateNumber + '</option>');
        });
        $("#journal_truck").select2();
      });

    $('#formCreateJournal').submit(function(e) {
      e.preventDefault();

      $("#btn_create_journal").prop("disabled", true).text("กำลังประมวลผล");
      
      gojax('post', '/api/v1/journal_pcr/create_journal', {
        desc: $('#journal_description').val(),
        truck: $("#journal_truck").val()
        
      }).done(function(data) {
        // console.log(data);
        
        // setTimeout(() => {
        //   $("#btn_create_journal").prop("disabled", false).text("Submit");
        // }, 2000);
        // $("#btn_create_journal").prop("disabled", false).text("Submit");
        setTimeout(function() {
          $("#btn_create_journal").prop("disabled", false).text("Submit");
        }, 200);


        $('#formCreateJournal').trigger('reset');
        $("#grid").jqxGrid('updatebounddata');
      });
    });

    $('#line').on('click', function() {

      var rowdata = row_selected('#grid');

      if (typeof rowdata !== 'undefined') {

        $('#modal_line').modal({
          backdrop: 'static'
        });
        $('.modal-title').html('Journal : ' + rowdata.JournalID);

        // $('#dialog_line').dialog({
        //   modal: true,
        //   width: 700,
        //   resizable: false
        // });

        // $('#dialog_line').dialog('option', 'title', 'Journal : ' + rowdata.JournalID);

        grid_line(rowdata.JournalID);
      } else {
        alert('กรุณาเลือกข้อมูล');
      }

    });

    $('#complete_journal').on('click', function() {
      if (confirm('Are you sure?')) {
        var rowdata = row_selected('#grid');
        if (typeof rowdata !== 'undefined') {
          gojax('post', '/api/v1/journal_pcr/complete', {
            journal_id: rowdata.JournalID
          }).done(function(data) {
            // alert(data.message);
            $("#grid").jqxGrid('updatebounddata', 'cells');

            $("#modal_inprogress").modal({ backdrop: "static" });

            $.ajax({
              url: "http://lungryn.deestonegrp.com:4400/interface/create_transfer_journal",
              type: "post",
              dataType: "json",
              cache: false,
              success: function (data) {
                if (data.Result === false) {
                  alert(data.Data);
                }

                $.ajax({
                  url: "http://lungryn.deestonegrp.com:4400/interface/create_update_transfer_journal",
                  type: "post",
                  dataType: "json",
                  cache: false,
                  success: function (res) {
                    if (res.Result === false) {
                      alert(res.Data);
                    }
                  }
                });

                $("#modal_inprogress").modal("hide");
              }
            });

            
          });
        } else {
          alert('กรุณาเลือกรายการ');
        }
      }
    });

    $('#print_journal_detail').on('click', function() {
      var rowdata = row_selected('#grid');

      if (typeof rowdata !== 'undefined') {
        window.open('/transfer/journal_pcr/print/' + rowdata.JournalID, '_blank');
      } else {
        alert('กรุณาเลือกข้อมูล');
      }

    });
  });

  function grid() {

    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [{
          name: 'JournalID',
          type: 'string'
        },
        {
          name: 'JournalDescription',
          type: 'string'
        },
        {
          name: 'TruckID',
          type: 'string'
        },
        {
          name: 'CreateDate',
          type: 'date'
        },
        {
          name: 'UserName',
          type: 'string'
        },
        {
          name: 'Count',
          type: 'number'
        },
        {
          name: 'Complete',
          type: 'bool'
        },
        {
          name: 'CompleteDate',
          type: 'date'
        },
        {
          name: 'CompleteBy',
          type: 'string'
        },
        {
          name: 'AX_JournalId',
          type: 'string'
        },
        {
          name: 'AxConfirmed',
          type: 'bool'
        },
        {
          name: 'AxConfirmedDate',
          type: 'date'
        },
        {
          name: 'AxPosted',
          type: 'bool'
        },
        {
          name: 'AxPostedDate',
          type: 'date'
        }
      ],
      url: '/api/v1/journal/' + $('input[name=device]').val(),
      updaterow: function(rowid, rowdata, commit) {
        gojax('post', '/api/v1/journal/update', {
          journal_id: rowdata.JournalID,
          journal_description: rowdata.JournalDescription
        }).done(function(data) {
          if (data.result === true) {
            commit(true);
          } else {
            alert(data.message);
            commit(false);
          }
        });
      }
    });

    return $("#grid").jqxGrid({
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
      theme: 'default',
      columns: [{
          text: 'Journal ID',
          datafield: 'JournalID',
          width: 100,
          editable: false
        },
        {
          text: 'Description',
          datafield: 'JournalDescription',
          width: 200
        },
        {
          text: 'Truck',
          datafield: 'TruckID',
          width: 150,
          editable: false
        },
        {
          text: 'Count',
          datafield: 'Count',
          width: 100,
          editable: false
        },
        {
          text: 'Create Date',
          datafield: 'CreateDate',
          width: 150,
          editable: false,
          filtertype: 'range',
          columntype: 'datetimeinput',
          cellsformat: 'yyyy-MM-dd HH:mm:ss'
        },
        {
          text: 'Create By',
          datafield: 'UserName',
          width: 200,
          editable: false
        },
        {
          text: 'Complete',
          datafield: 'Complete',
          width: 100,
          editable: false,
          columntype: 'checkbox',
          filtertype: 'bool'
        },
        {
          text: 'Complete Date',
          datafield: 'CompleteDate',
          width: 150,
          editable: false,
          filtertype: 'range',
          columntype: 'datetimeinput',
          cellsformat: 'yyyy-MM-dd HH:mm:ss'
        },
        {
          text: 'Complete By',
          datafield: 'CompleteBy',
          width: 200,
          editable: false
        },
        {
          text: 'AX Journal Id',
          datafield: 'AX_JournalId',
          width: 200,
          editable: true
        },
        {
          text: 'Ax Confirmed',
          datafield: 'AxConfirmed',
          width: 100,
          editable: false,
          columntype: 'checkbox',
          filtertype: 'bool'
        },
        {
          text: 'Ax Confirmed Date',
          datafield: 'AxConfirmedDate',
          width: 150,
          editable: false,
          filtertype: 'range',
          columntype: 'datetimeinput',
          cellsformat: 'yyyy-MM-dd HH:mm:ss'
        },
        {
          text: 'Ax Posted',
          datafield: 'AxPosted',
          width: 100,
          editable: false,
          columntype: 'checkbox',
          filtertype: 'bool'
        },
        {
          text: 'Ax Posted Date',
          datafield: 'AxPostedDate',
          width: 150,
          editable: false,
          filtertype: 'range',
          columntype: 'datetimeinput',
          cellsformat: 'yyyy-MM-dd HH:mm:ss'
        }
      ]
    });
  }

  function grid_line(journal_id) {

    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [{
          name: 'JournalID',
          type: 'string'
        },
        {
          name: 'Barcode',
          type: 'string'
        },
        {
          name: 'ItemID',
          type: 'string'
        },
        {
          name: 'ItemName',
          type: 'string'
        },
        {
          name: 'Batch',
          type: 'string'
        },
        {
          name: 'CreateDate',
          type: 'date'
        },
        {
          name: 'UserName',
          type: 'string'
        }
      ],
      url: '/api/v1/journal/line/' + journal_id
    });

    return $("#grid_line").jqxGrid({
      width: '100%',
      source: dataAdapter,
      autoheight: true,
      altrows: true,
      pageable: true,
      sortable: true,
      filterable: true,
      showfilterrow: true,
      columnsresize: true,
      pageSize: 10,
      editable: false,
      theme: 'default',
      columns: [
        // {
        //   text: '#', sortable: false, filterable: false, editable: false,
        //   groupable: false, draggable: false, resizable: false,
        //   datafield: '', columntype: 'number', width: 40,
        //   cellsrenderer: function (row, column, value) {
        //     return "<div style='margin:4px;'>" + (value + 1) + "</div>";
        //   }
        // },
        // { text: 'Journal ID', datafield: 'JournalID', width: 100 },
        {
          text: 'Barcode',
          datafield: 'Barcode',
          width: 100
        },
        {
          text: 'Item',
          datafield: 'ItemID',
          width: 100
        },
        {
          text: 'Item Name',
          datafield: 'ItemName',
          width: 200
        },
        {
          text: 'Batch',
          datafield: 'Batch',
          width: 100
        },
        {
          text: 'Create Date',
          datafield: 'CreateDate',
          width: 200,
          filtertype: 'range',
          columntype: 'datetimeinput',
          cellsformat: 'yyyy-MM-dd HH:mm:ss'
        },
        {
          text: 'Create By',
          datafield: 'UserName',
          width: 150
        }
      ]
    });
  }
</script>
<?php $this->end() ?>