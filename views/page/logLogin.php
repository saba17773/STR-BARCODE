  <?php $this->layout("layouts/base", ["title" => "Log Login STR-barcode"]); ?>
  <h1>Log Login</h1>
  <div id="grid_movementType"></div>
  <script>
		jQuery(document).ready(function($) {
    grid_movementType();
		});
    function grid_movementType() {
      var source =
           {
                  datatype: 'json',
                  datafields: [
                    { name: 'EmployeeID', type: 'string'},
                    { name: 'EMPNAME', type: 'string'},
                    { name: 'EMPLASTNAME', type: 'string'},
                    { name: 'COMPANYNAME', type: 'string'},
                    { name: 'DIVISIONNAME', type: 'string'},
                    { name: 'POSITIONNAME', type: 'string'},
                    { name: 'DEPARTMENTNAME', type: 'string'},
                    // { name: 'TimeLogin', type: 'string'},
                    { name: 'LoginDate', type: 'date'},
                    { name: 'typeLog', type: 'string'},
                    { name: 'Username', type: 'string'}
                ],
                  url: base_url + "/api/LogLogin/all"
                };
                var adapter = new $.jqx.dataAdapter(source);
                var firstNameColumnFilter = function () {
                var filtergroup = new $.jqx.filter();
                var filter_or_operator = 1;
                var filtervalue = 'Nancy';
                var filtercondition = 'contains';
                var filter = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
                filtergroup.addfilter(filter_or_operator, filter);
                return filtergroup;
                }();
                return $("#grid_movementType").jqxGrid({
      	           width: '100%',
                   source: adapter,
                   showfilterrow: true,
                   filterable: true,
                   //   selectionmode: 'multiplecellsextended',
                   //   width: '100%',
      	           //source: dataAdapter,
      	           autoheight: true,
      	           pageSize : 12,
      	           // rowsheight : 40,
      	          // columnsheight : 40,
      	          altrows : true,
      	          pageable : true,
      	          sortable: true,
      	          //filterable : true,
      	          //showfilterrow : true,
      	          columnsresize: true,
      	          // theme : 'theme',
                  columns: [
                    { text: 'Employee', datafield: 'EmployeeID',filtertype: 'input', datafield: 'EmployeeID', width: 100 },
                { text: 'Oparetor', datafield: 'Username',filtertype: 'input', datafield: 'Username', width: 100 },
                { text: 'Name' ,datafield: 'EMPNAME',filtertype: 'input', datafield: 'EMPNAME', width: 100},
                { text: 'Surname', datafield: 'EMPLASTNAME',filtertype: 'input', datafield: 'EMPLASTNAME', width: 100},
                { text: 'Company', datafield: 'COMPANYNAME',filtertype: 'input', datafield: 'COMPANYNAME', width: 100 },
                { text: 'Division', datafield: 'DIVISIONNAME', filtertype: 'input', datafield: 'DIVISIONNAME', width: 100},
                { text: 'Position', datafield: 'POSITIONNAME', filtertype: 'input', datafield: 'POSITIONNAME', width: 100 },
                { text: 'Department', datafield: 'DEPARTMENTNAME', filtertype: 'input', datafield: 'DEPARTMENTNAME', width: 180 },
                { text: 'Login By', datafield: 'typeLog',filtertype: 'input', datafield: 'typeLog', width: 100 },
                { text: 'Login Date', datafield: 'LoginDate',filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 180 },
                  // { text: 'DateLogin', datafield: 'DateLogin', columntype: 'datetimeinput', filtertype: 'date', width: 100, cellsalign: 'left', cellsformat: 'd' },
                ]
              });
            }
			</script>
