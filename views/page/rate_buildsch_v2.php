<?php $this->layout("layouts/base", ['title' => 'Rate Building Schedule']); ?>

<div class="head-space"></div>
<h4>
  รายละเอียด
  วันที่ : <?php echo $date; ?>
  กะ : <?php if ($shift == 'day') {
          echo 'กลางวัน';
        } else {
          echo 'กลางคืน';
        }  ?>
</h4>

<div class="panel panel-default" id="panel2">
  <div class="panel-heading">Rate Building Schedule</div>
  <div class="panel-body">
    <div class="btn-panel">
      <button class="btn btn-success" id="line"><span class="glyphicon glyphicon-list"></span> Line</button>
      <a href="<?php echo APP_ROOT; ?>/import/schbuild" class="btn btn-info" id="import" style="background: #9457eb;" target="_blank"><span class="glyphicon glyphicon-import"></span> Import Rate Build Schedule</a>
      <button class="btn btn-info" id="download"><span class="glyphicon glyphicon-save"></span> Download Template</button>
    </div>

    <div id="grid_sch"></div>
  </div>
</div>

<div class="modal" id="modal_detail" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">รายละเอียด เครื่อง :
          <span id="machine_modal"></span> วันที่ :
          <span id="date_modal"></span> กะ :
          <span id="shift_modal"></span>
        </h4>
      </div>
      <div class="modal-body">
        <div id="grid_detail"></div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    date_inter = "<?php echo $date_inter; ?>";
    s = "<?php echo $shift; ?>";
    if (s === "day") {
      shift = 1;
    } else {
      shift = 2;
    }

    bindGridSch(shift, date_inter)

    $('#line').on('click', function() {
      event.preventDefault();
      var rowdata = row_selected('#grid_sch');
      if (typeof rowdata !== 'undefined') {
        $('#modal_detail').modal({
          backdrop: 'static'
        });
        machine = rowdata.Machine;
        datesch = rowdata.DateRateBuild;
        shift = rowdata.Shift;

        $('#machine_modal').text(machine);
        $('#date_modal').text(datesch);
        $('#shift_modal').text(shift);

        bindGridLine();

      } else {
        $('#modal_alert').modal({
          backdrop: 'static'
        });
        $('#modal_alert_message').text('กรุณาเลือกข้อมูล');
      }
    });

    $('#download').on('click', function() {
      window.open("/download/schbuild/" + date_inter + "/" + shift);
    });

    function bindGridSch(shift, date_inter) {
      var dataAdapter = new $.jqx.dataAdapter({
        datatype: "json",
        datafields: [{
            name: "Machine",
            type: "string"
          },
          {
            name: "DateRateBuild",
            type: "string"
          },
          {
            name: "Shift",
            type: "int"
          },
          {
            name: "Name",
            type: "string"
          },
          {
            name: "CreateDate",
            type: "string"
          }
        ],
        url: base_url + '/api/schbuild/bindGrid/' + shift + '/' + date_inter
      });

      return $("#grid_sch").jqxGrid({
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
        columns: [{
            text: "Machine",
            datafield: "Machine",
            width: 100
          },
          {
            text: "DateRateBuild",
            datafield: "DateRateBuild",
            width: 120
          },
          {
            text: "Shift",
            datafield: "Shift",
            width: 100
          },
          {
            text: "Name",
            datafield: "Name",
            width: 200
          },
          {
            text: "CreateDate",
            datafield: "CreateDate",
            width: 200
          }
        ]
      });
    }

    function bindGridLine() {
      var dataAdapter = new $.jqx.dataAdapter({
        datatype: 'json',
        datafields: [{
            name: "Machine",
            type: "string"
          },
          {
            name: "Code",
            type: "string"
          },
          {
            name: "DateRateBuild",
            type: "string"
          },
          {
            name: "Shift",
            type: "int"
          },
          {
            name: "Total",
            type: "int"
          },
          {
            name: "Name",
            type: "string"
          },
          {
            name: "CreateDate",
            type: "string"
          },
          {
            name: "UpdateName",
            type: "string"
          },
          {
            name: "UpdateDate",
            type: "string"
          },
          {
            name: "Active",
            type: "int"
          }
        ],
        url: base_url + "/api/schbuild/bindGridLine/" + machine + "/" + datesch + "/" + shift
      });
      return $("#grid_detail").jqxGrid({
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
          // { text: 'Machine', datafield: 'Machine', width: 70},
          {
            text: 'Code',
            datafield: 'Code',
            width: 70
          },
          // { text: 'DateRateBuild', datafield: 'DateRateBuild', width: 100},
          // { text: 'Shift', datafield: 'Shift', width: 50},
          {
            text: 'Total',
            datafield: 'Total',
            width: 70
          },
          {
            text: 'CreateBy',
            datafield: 'Name',
            width: 200
          },
          {
            text: 'CreateDate',
            datafield: 'CreateDate',
            width: 200
          }
          // { text: 'UpdateBy', datafield: 'UpdateName', width: 200},
          // { text: 'UpdateDate', datafield: 'UpdateDate', width: 120}
          // { text: 'Active', datafield: 'Active', width: 70}
        ]
      });
    }


  });
</script>