<?php $this->layout("layouts/base", ['title' => 'Scrap Component']); ?>

<style>
  td {
    padding: 10px;
  }
</style>

<h1>Scrap Component</h1>

<hr />

<div style="margin-bottom: 30px;">
  <button class="btn btn-primary" id="new">New</button>
  <button class="btn btn-success" id="complete">Complete</button>
  <button class="btn btn-danger" id="cancel">Cancel</button>
</div>

<div id="grid"></div>

<!-- Modal Create -->
<div class="modal" id="modal_new" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">New</h4>
      </div>
      <div class="modal-body">
        <form id="form_new_scrap">
        <table style="width: 100%;">
          <tr>
            <td>Area</td>
            <td>
              <label><input type="radio" name="area" value="1" required> Zone A </label>
            </td>
            <td>
              <label><input type="radio" name="area" value="2"> Zone B </label>
            </td>
          </tr>
          <tr>
            <td>Part Code</td>
            <td>
              <input type="text" name="part_code" id="part_code" class="form-control" required>
            </td>
            <td>
              <input type="text" name="part_code_display" id="part_code_display" class="form-control" readonly>
            </td>
          </tr>
          <tr>
            <td>QTY</td>
            <td>
              <input type="text" name="qty" id="qty" class="form-control" required>
            </td>
            <td>

            </td>
          </tr>
          <tr>
            <td>Defect</td>
            <td>
              <select name="defect" id="defect" class="form-control" required></select>
            </td>
            <td>
              <input type="text" name="defect_display" id="defect_display" class="form-control" readonly>
            </td>
          </tr>
          <tr>
            <td>Scrap Location</td>
            <td>
              <select class="form-control" name="scrap_location" id="scrap_location" required>
                <option value="1">Zone A</option>
                <option value="2">Zone B</option>
              </select>
            </td>
            <td></td>
          </tr>
          <tr>
            <td></td>
            <td><button type="submit" class="btn btn-primary">Submit</button></td>
            <td></td>
          </tr>
        </table>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Complete -->
<div class="modal" id="modal_complete" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Complete</h4>
      </div>
      <div class="modal-body">
        <!--  -->
        <form id="formComplete">
          <div class="form-group">
            <label for="sch_date">SCH Date</label>
            <input type="text" name="sch_date" id="sch_date" class="form-control">
          </div>
          <button type="button" class="btn btn-primary" id="submitComplete">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php $this->push('scripts'); ?>
<script>
  jQuery(document).ready(function ($) {

    grid();
    inputWeight('#qty');

    $('#cancel').on('click', function () {
      if (confirm('Are you sure?')) {
        var rowdata = row_selected('#grid');
        if (typeof rowdata !== 'undefined') {
          gojax('post', '/scrap_component/cancel', {
            scrap_id: rowdata.ID
          }).done(function(data) {
            alert(data.message);
            $("#grid").jqxGrid('updatebounddata');
          });
        }
      }
    });

    $('#submitComplete').on('click', function () {
      if (confirm('Are you sure?')) {
        var rowdata = row_selected('#grid');
        gojax('post', '/scrap_component/complete', {
          sch_date: $('#sch_date').val(),
          part_code: rowdata.PartCode
        }).done(function(data) {
          alert(data.message);
          $('#modal_complete').modal('hide');
          $("#grid").jqxGrid('updatebounddata');
        });
      }
    });

    $('#form_new_scrap').submit(function (e) {
      e.preventDefault();
      gojax('post', '/scrap_component/save', {
        area: $('input[name=area]:checked').val(),
        part_code: $('input[name=part_code]').val(),
        qty: $('input[name=qty]').val(),
        defect: $('input[name=defect]').val(),
        scrap_location: $('select[name=scrap_location]').val()
      }).done(function(data) {
        alert(data.message);
        $("#grid").jqxGrid('updatebounddata');
      });
    });

    $('#new').on('click', function () {
      $('#modal_new').modal({ backdrop: 'static' });
      
    });

    $('#complete').on('click', function () {
      var rowdata = row_selected('#grid');
      if (typeof rowdata !== 'undefined') {
        $('#modal_complete').modal({ backdrop: 'static' });
        $('#sch_date').datepicker({
          dateFormat: 'yy-mm-dd',
        });
      } else {
        alert('please select row!');
      }
    });
  });

  function grid() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [
        { name: 'ID', type: 'number' },
        { name: 'PartCode', type: 'string' },
        { name: 'ScrapVolume', type: 'number' },
        { name: 'Unit', type: 'string'},
        { name: 'Defect', type: 'string'},
        { name: 'DefectName', type: 'string'},
        { name: 'UserName', type: 'string'},
        { name: 'CreateDate', type: 'date'},
        { name: 'Status', type: 'string'}
      ],
      url: '/scrap_component/all'
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
      columns: [
        { text: 'Part Code', datafield: 'PartCode', width: 100 },
        { text: 'Scrap Volume', datafield: 'ScrapVolume', width: 120 },
        { text: 'Unit', datafield: 'Unit', width: 100},
        { text: 'Defect', datafield: 'Defect', width: 100},
        { text: 'Defect Description', datafield: 'DefectName', width: 200 },
        { text: 'UserName', datafield: 'UserName', width: 100},
        { text: 'Create Date', datafield: 'CreateDate', width: 200, cellsformat: 'yyyy-MM-dd HH:mm:ss'},
        { text: 'Status', datafield: 'Status', width: 100 }
      ]
    });
  }
</script>
<?php $this->end(); ?>