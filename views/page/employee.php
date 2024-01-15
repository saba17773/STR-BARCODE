<?php $this->layout("layouts/base", ['title' => 'Employee']); ?>

<h1>Employee</h1>

<div class="btn-panel">
	<button class="btn btn-info" id="syncEmp"><span class="glyphicon glyphicon-save"></span> Sync From Ax</button>
</div>

<div id="grid_employee"></div>

<script>
	jQuery(document).ready(function($) {
		grid_employee();

		$('#syncEmp').click(function(event) {
			$('#syncEmp').prop('readonly', true);
			gojax('post', '/sync_emp').done(function(data) {
				setTimeout(function() {
					$('#syncEmp').prop('readonly', false);
				}, 2000);
				
				if (data.result === true) {
					alert('Sync Employee Successful!');
				} else {
					alert('Sync Employee Failed!');
				}
			});
		});
	});

	function fetchGrid() {
		$('#grid_employee').jqxGrid('updatebounddata');
	}

	function grid_employee() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
	        datafields: [
	        	{ name: 'Code', type: 'string'},
	        	{ name: 'FirstName', type: 'string' },
	        	{ name: 'LastName', type: 'string'},
	        	{ name: 'DivisionCode', type: 'string'},
	        	{ name: 'DepartmentCode', type: 'string'},
	        	{ name: 'DivisionDesc', type: 'string'},
	        	{ name: 'DepartmentDesc', type: 'string'},
	        	{ name: 'EmpStatus', type: 'bool'}
	        ],
	        url: base_url + "/api/employee/all",
	        updaterow: function (rowid, rowdata, commit) {
	        	gojax('post', base_url+'/api/employee/status/save', {
	        		id: rowdata.Code,
	        		status: rowdata.EmpStatus
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

		return $("#grid_employee").jqxGrid({
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
	        editable: true,
	        columns: [
	          { text: 'Code', datafield: 'Code', editable: false, width: 100},
	          { text: 'First Name', datafield: 'FirstName', editable: false, width: 100},
	          { text: 'Last Name', datafield: 'LastName', editable: false, width: 150},
	          { text: 'Division', datafield: 'DivisionDesc', editable: false, width: 200},
	          { text: 'Department', datafield: 'DepartmentDesc', editable: false, width: 200},
	          { text: 'Status', datafield: 'EmpStatus', filtertype: 'bool', columntype: 'checkbox', editable: true, width: 100}
	        ]
	    });
	}
</script>