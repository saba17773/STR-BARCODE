<?php $this->layout("layouts/base", ['title' => 'Defect']); ?>

<div class="head-space"></div>

<div class="panel panel-default">
	<div class="panel-heading">Defect</div>
	<div class="panel-body">
		<div class="btn-panel">
			<button onclick="return modal_create_open()" class="btn btn-success" data-backdrop="static" data-toggle="modal" data-target="#modal_create" style="display: none;"> Create </button>
			<button class="btn btn-info" id="edit" style="display: none;">Edit</button>
			<button id="sync_defect" style="display: black;"><span class="glyphicon glyphicon-save"></span> Sync Defect</button>
		</div>

		<div id="grid_defect"></div>
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
        <h4 class="modal-title">Create new</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <form id="form_create" onsubmit="return submit_create()">
        	<div class="form-group">
        		<label for="description">Description</label>
        		<input type="text" name="description" id="description" class="form-control" readonly>
        	</div>
        	<div class="form-group">
        		<label for="description_th">Category Code</label>
        		<input type="text" name="description_th" id="description_th" class="form-control" readonly>
        	</div>
        	<div class="form-group" >
        		<label>Defect</label>
        		<div>
        			<select name="scrap[]" id="scrap" class="_select" multiple="multiple" style="width:300px;">
	        			<option value="1">Greentire Scrap</option>
	        			<option value="2">Curetire Scrap</option>
	        		</select>
        		</div>
        	</div>

        	<div class="form-group" style="display: none;">
        		<label>Company</label>
        		<div>
        			<select name="company[]" id="company" class="_select" multiple="multiple" style="width:300px;">
	        			<option value="1">DSL</option>
	        			<option value="2">DRB</option>
	        			<option value="3">DSI</option>
	        			<option value="4">SVO</option>
	        			<option value="5">STR</option>
	        		</select>
        		</div>
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
		grid_defect();

		$('#sync_defect').on('click', function(event) {
			gojax('post', '/api/v1/defect/sync').done(function(data) {
				alert(data.message);
			});
		});

		$('#edit').on('click', function(e) {
			var rowdata = row_selected('#grid_defect');
			if (typeof rowdata !== 'undefined') {
				$('#modal_create').modal({backdrop: 'static'});
				$('.modal-title').text('Update');
				$('#form_create').trigger('reset');
				$('input[name=form_type]').val('update');
				$('input[name=_id]').val(rowdata.ID);
				$('input[name=description]').val(rowdata.Description);
				$('input[name=description_th]').val(rowdata.CategoryCode);

				if (rowdata.GT_Inspection == 1) {
					$('select#scrap>option:eq(0)').prop('selected', true);
				}

				if (rowdata.Xray_Inspection == 1) {
					$('select#scrap>option:eq(1)').prop('selected', true);
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

				$('select._select').multipleSelect({placeholder:'กรุณาเลือกข้อมูล'});
			}
		});
	});

	function submit_create() {
		gojax_f('post', base_url+'/api/defect/create', '#form_create')
			.done(function(data) {
				if (data.status == 404) {
					gotify('ทำรายการไม่สำเร็จ', 'danger');
				} else {
					$('#grid_defect').jqxGrid('updatebounddata');
					$('#modal_create').modal('hide');
				}
			})
			.fail(function() {
				gotify('ไม่สามารถส่งข้อมูลได้', 'danger');
			});
		return false;
	}

	function modal_create_open() {
		$('form#form_create').trigger('reset');
		$('input[name=form_type]').val('create');
		$('.modal-title').text('Create new');
		$('select._select').multipleSelect({placeholder:'กรุณาเลือกข้อมูล'});
	}

	function fetchGrid() {
		$("#grid_defect").jqxGrid('updatebounddata', 'cells');
	}

	function grid_defect() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
	        datafields: [
	        	{ name: 'ID', type: 'string'},
	        	{ name: 'Description', type: 'string' },
	        	{ name: 'CategoryCode', type: 'string' },
	        	{ name: 'GT_Inspection', type: 'bool' },
	        	{ name: 'Xray_Inspection', type: 'bool' },
	        	{ name: 'Curing_Inspection', type: 'bool' },
	        	{ name: 'QTech', type: 'bool' },
	        	{ name: 'DSL', type: 'bool' },
	        	{ name: 'DRB', type: 'bool' },
	        	{ name: 'DSI', type: 'bool' },
	        	{ name: 'SVO', type: 'bool' },
	        	{ name: 'STR', type: 'bool' }
	        ],
	        url: base_url + "/api/defect/master/all",
	        updaterow: function (rowid, rowdata, commit) {
	        	gojax('post', base_url+'/api/defect/update', {
	        		id: rowdata.ID,
	        		gt: rowdata.GT_Inspection,
	        		fn: rowdata.Xray_Inspection,
	        		df: rowdata.Curing_Inspection
	        	})
	        	.done(function(data) {
	        		if (data.status === 200) {
	        			fetchGrid();
	        		} else {
	        			$('#modal_alert').modal({backdrop: 'static'});
	        		  $('#modal_alert_message').text('ไม่สามารถอัพเดทสถานะได้');
	        		}
	        	})
	        	.fail(function() {
	        		$('#modal_alert').modal({backdrop: 'static'});
	        		$('#modal_alert_message').text('ไม่สามารถอัพเดทสถานะได้');
	        	});
            commit(true);
          }
		});

		return $("#grid_defect").jqxGrid({
	        width:'100%',
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
	          { text: 'ID', datafield: 'ID', editable: false, width: 80},
	          { text: 'Description', datafield: 'Description', editable: false, width: 300},
	          { text: 'Category Code', datafield: 'CategoryCode', editable: false, width: 120},
	          { text: 'GT Inspection', datafield: 'GT_Inspection', filtertype: 'bool', columntype: 'checkbox', editable: true, width: 150},
	          { text: 'Final Inspection', datafield: 'Xray_Inspection', filtertype: 'bool', columntype: 'checkbox', editable: true, width: 200},
	          { text: 'Curing Inspection', datafield: 'Curing_Inspection', filtertype: 'bool', columntype: 'checkbox', editable: true, width: 150}
	          // { text: 'DSL', datafield: 'DSL', filtertype: 'bool', columntype: 'checkbox', editable: false, width: 100},
	          // { text: 'DRB', datafield: 'DRB', filtertype: 'bool', columntype: 'checkbox', editable: false, width: 100},
	          // { text: 'DSI', datafield: 'DSI', filtertype: 'bool', columntype: 'checkbox', editable: false, width: 100},
	          // { text: 'SVO', datafield: 'SVO', filtertype: 'bool', columntype: 'checkbox', editable: false, width: 100},
	          // { text: 'STR', datafield: 'STR', filtertype: 'bool', columntype: 'checkbox', editable: false, width: 100},
	        ]
	    });
	}
</script>