<?php $this->layout("layouts/base", ["title" => "Requsition Note"]); ?>

<div class="head-space"></div>

<div class="panel panel-default">
  <div class="panel-heading">Requisition Note</div>
  <div class="panel-body">
    <div class="btn-panel">
      <button class="btn btn-primary" id="create">Create</button>
      <!-- <button class="btn btn-info btn-lg">Edit</button> -->
    </div>

    <div id="grid_requsition"></div>
  </div>
</div>


<!-- Modal -->
<div class="modal" id="modal_create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Create</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <form id="formRequsitionNote">
        	<div class="form-group">
        		<label for="description">Description</label>
        		<input type="text" class="form-control" id="description" name="description" required>
        	</div>
        	<div class="form-group">
        		<label for="selectWarehouse">Warehouse</label> <br>
        		<select name="selectWarehouse[]" multiple="multiple" id="selectWarehouse" class="_select" style="width: 300px;" required>
        			<option value="1">Final</option>
        			<option value="2">Finish Good</option>
        		</select>
        	</div>
        	<input type="hidden" name="_id" id="_id" value="">
        	<button class="btn btn-primary btn-lg" type="submit">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>

jQuery(document).ready(function($) {

	grid_requsition();

  $('#create').on('click', function(event) {

    $('#modal_create').modal({backdrop: 'static'});
    $('#formRequsitionNote').trigger('reset');

    $('select._select').multipleSelect({placeholder:'เลือกข้อมูล'});
  });

	$('#formRequsitionNote').on('submit', function(event) {
		event.preventDefault();

		gojax_f('post', base_url+'/api/requsition_note/save', '#formRequsitionNote')
			.done(function(data) {
				if (data.status == 200) {
          $('#modal_create').modal('hide');
          $('#grid_requsition').jqxGrid('updatebounddata');
        } else {
          $('#modal_alert').modal({backdrop: 'static'});
          $('#modal_alert_message').text(data.message);
          $('#top_alert').hide();
        }
			});

	});

});

function grid_requsition() {
	var dataAdapter = new $.jqx.dataAdapter({
    datatype: 'json',
        datafields: [
          { name: 'ID', type: 'number'},
          { name: 'Description', type: 'string' },
          { name: 'Final', type: 'bool' },
          { name: 'FinishGood', type: 'bool' },
          { name: 'UpdateDate', type: 'date'}
        ],
        updaterow: function (rowid, rowdata, commit) {

          var wh = [0, 0];

          if (rowdata.Final === true && rowdata.FinishGood === false) {
            wh = [1];
          }

          if (rowdata.Final === false && rowdata.FinishGood === true) {
            wh = [2];
          }

          if (rowdata.Final === true && rowdata.FinishGood === true) {
            wh = [1, 2];
          }

          if (rowdata.Final === false && rowdata.FinishGood === false) {
            wh = [0, 0];
          }

          gojax('post', base_url+'/api/requsition_note/save', {
            _id: rowdata.ID,
            description: rowdata.Description,
            selectWarehouse: wh
          })
          .done(function(data) {
            if (data.status == 404) {
              $('#modal_alert').modal({backdrop: 'static'});
              $('#modal_alert_message').text(data.message);
              $('#top_alert').hide();
            } else {
              $('#grid_requsition').jqxGrid('updatebounddata');
            }
          });
          commit(true);
        },
        url: base_url + "/api/requsition_note/all"
  });

  return $("#grid_requsition").jqxGrid({
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
        editable: true,
        // theme : 'theme',
        columns: [
          { text: 'Description', datafield: 'Description', width: 200},
          { text: 'Final', datafield: 'Final', filtertype: 'bool', columntype: 'checkbox', width: 100},
          { text: 'Finish Good', datafield: 'FinishGood', filtertype: 'bool', columntype: 'checkbox', width: 100}

        ]
  });
}

</script>
