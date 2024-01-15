<?php $this->layout("layouts/base", ['title' => 'Disposal to Use In']); ?>

<div class="head-space"></div>

<div class="panel panel-default">
	<div class="panel-heading">Disposal to use in</div>
	<div class="panel-body">
		<div class="btn-panel">
			<button onclick="return modal_create_open()"  class="btn btn-success" data-backdrop="static" data-toggle="modal" data-target="#modal_create">Create</button>
			<button class="btn btn-info" id="edit">Edit</button>
		</div>

		<div id="grid_disposal"></div>
	</div>
</div>

<!-- Create Modal -->
<div class="modal" id="modal_create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Create new</h4>
      </div>
      <div class="modal-body">
      	<form id="form_create" onsubmit="return submit_create()">

      		<div class="form-group">
      			<label for="desc">Description</label>
      			<input type="text" name="desc" id="desc" class="form-control" autocomplete="off" required>
      		</div>

      		<div class="form-group">
      			<select type="text" name="disposal_action[]" id="disposal_action" class="_select" multiple="multiple" style="width:300px;">
      				<option value="1">GT Build</option>
      				<option value="2">GT Inspection</option>
      				<option value="3">GT Scrap</option>
      				<option value="4">Curing</option>
      				<option value="5">X-ray Inspection</option>
      				<option value="6">X-ray</option>
      				<option value="7">X-ray Scrap</option>
      				<option value="8">X-ray Q-Tech</option>
      				<option value="9">FG</option>
      				<option value="10">Loading</option>
      			</select>
      		</div>

      		<div class="form-group">
      			<label for="company">Company</label><br>
      			<select name="company[]" id="company" class="_select" multiple="multiple" style="width:300px;">
      				<option value="1">DSL</option>
      				<option value="2">DRB</option>
      				<option value="3">DSI</option>
      				<option value="4">SVO</option>
      				<option value="5">STR</option>
      			</select>
      		</div>
      		<input type="hidden" name="form_type">
      		<input type="hidden" name="_id">
      		<button class="btn btn-primary" type="submit">Save</button>
      	</form>
      </div>
    </div>
  </div>
</div>

<script>
	jQuery(document).ready(function($) {
		grid_disposal();

		$('#edit').on('click', function(e) {
			var rowdata = row_selected("#grid_disposal");
			if (typeof rowdata !== 'undefined') {
				$('#modal_create').modal({backdrop: 'static'});
				$('input[name=form_type]').val('update');
				$('.modal-title').text('Update');
				$('input[name=_id]').val(rowdata.ID);
				$('input[name=desc]').val(rowdata.DisposalDesc);

				if (rowdata.GT_Build == 1) {
					$('select#disposal_action>option:eq(0)').prop('selected', true);
				}

				if (rowdata.GT_Inspection == 2) {
					$('select#disposal_action>option:eq(1)').prop('selected', true);
				}

				if (rowdata.GT_Scrap == 3) {
					$('select#disposal_action>option:eq(2)').prop('selected', true);
				}

				if (rowdata.Curing == 4) {
					$('select#disposal_action>option:eq(3)').prop('selected', true);
				}

				if (rowdata.Xray_Inspection == 5) {
					$('select#disposal_action>option:eq(4)').prop('selected', true);
				}

				if (rowdata.Xray == 6) {
					$('select#disposal_action>option:eq(5)').prop('selected', true);
				}

				if (rowdata.Xray_Scrap == 7) {
					$('select#disposal_action>option:eq(6)').prop('selected', true);
				}

				if (rowdata.Xray_Qtech == 8) {
					$('select#disposal_action>option:eq(7)').prop('selected', true);
				}

				if (rowdata.FG == 9) {
					$('select#disposal_action>option:eq(8)').prop('selected', true);
				}

				if (rowdata.Loading == 10) {
					$('select#disposal_action>option:eq(9)').prop('selected', true);
				}

				if (rowdata.DSL == 1) {
					$('select#company>option:eq(0)').prop('selected', true);
				}

				if (rowdata.DRB == 1) {
					$('select#company>option:eq(1)').prop('selected', true);
				}

				if (rowdata.DSI == 1) {
					$('select#company>option:eq(2)').prop('selected', true);
				}

				if (rowdata.SVO == 1) {
					$('select#company>option:eq(3)').prop('selected', true);
				}

				if (rowdata.STR == 1) {
					$('select#company>option:eq(4)').prop('selected', true);
				}

				$('select._select').multipleSelect({ placeholder: "เลือกข้อมูล"});
			}
			
		});
	});

	function modal_create_open() {
		$('#form_create').trigger('reset');
		$('input[name=form_type]').val('create');
		$('.modal-title').text('Create new');
		$('select._select').multipleSelect({ placeholder: "เลือกข้อมูล"});
	}

	function submit_create() {
		gojax_f('post', base_url + '/api/disposal/create', '#form_create')
			.done(function(data) {
				if (data.status == 404) {
					// gotify(data.message, 'danger');
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text(data.message);
					$('#top_alert').hide();
				} else {
					$('#modal_create').modal('hide');
					$('#grid_disposal').jqxGrid('updatebounddata');
				}
			});
		return false;
	}

	function grid_disposal() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
	        datafields: [
	        	{ name: 'ID', type: 'string'},
	        	{ name: 'DisposalDesc', type: 'string' },
	        	{ name: 'GT_Build', type: 'bool'},
	        	{ name: 'GT_Inspection', type: 'bool'},
	        	{ name: 'GT_Scrap', type: 'bool'},
	        	{ name: 'Curing', type: 'bool'},
	        	{ name: 'Xray', type: 'bool'},
	        	{ name: 'Xray_Inspection', type: 'bool'},
	        	{ name: 'Xray_Scrap', type: 'bool'},
	        	{ name: 'Xray_Qtech', type: 'bool'},
	        	{ name: 'FG', type: 'bool'},
	        	{ name: 'Loading', type: 'bool'},
	        	{ name: 'DSL', type: 'bool'},
	        	{ name: 'DRB', type: 'bool'},
	        	{ name: 'DSI', type: 'bool'},
	        	{ name: 'SVO', type: 'bool'},
	        	{ name: 'STR', type: 'bool'}
	        ],
	        url: base_url + "/api/disposal/all"
		});

		return $("#grid_disposal").jqxGrid({
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
	        // theme: 'theme',
	        columns: [
	          { text: 'ID', datafield: 'ID', width: 50},
	          { text: 'Disposal Description', datafield: 'DisposalDesc', width: 150},
	          { text: 'GT Build', datafield: 'GT_Build', width:100, filtertype: 'bool', columntype: 'checkbox'},
	          { text: 'GT Inspection', datafield: 'GT_Inspection', width:100, filtertype: 'bool', columntype: 'checkbox'},
	          { text: 'GT Scrap', datafield: 'GT_Scrap', width: 80,filtertype: 'bool', columntype: 'checkbox'},
	          { text: 'Curing', datafield: 'Curing',  width:50, filtertype: 'bool', columntype: 'checkbox'},
	          { text: 'X-ray Inspection', datafield: 'Xray', width:140, filtertype: 'bool', columntype: 'checkbox'},
	          { text: 'X-ray Scrap', datafield: 'Xray_Scrap', width:100, filtertype: 'bool', columntype: 'checkbox'},
	          { text: 'X-ray Q-Tech', datafield: 'Xray_Qtech', width:100, filtertype: 'bool', columntype: 'checkbox'},
	          { text: 'FG', datafield: 'FG', width:50, filtertype: 'bool', columntype: 'checkbox'},
	          { text: 'Loading', datafield: 'Loading', width:100, filtertype: 'bool', columntype: 'checkbox'},
	          { text: 'DSL', datafield: 'DSL',  width:50, filtertype: 'bool', columntype: 'checkbox'},
	          { text: 'DRB', datafield: 'DRB',  width:50, filtertype: 'bool', columntype: 'checkbox'},
	          { text: 'DSI', datafield: 'DSI',  width:50, filtertype: 'bool', columntype: 'checkbox'},
	          { text: 'SVO', datafield: 'SVO',  width:50, filtertype: 'bool', columntype: 'checkbox'},
	          { text: 'STR', datafield: 'STR',  width:50, filtertype: 'bool', columntype: 'checkbox'}
	        ]
	    });
	}
</script>
