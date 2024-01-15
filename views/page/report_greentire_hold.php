<?php $this->layout("layouts/base", ["title" => "Report Greentire Hold"]); ?>

<h1>Report Greentire Hold</h1>

<div id="grid_greentire_hold"></div>

<script type="text/javascript">
	jQuery(document).ready(function($){
		grid_greentire_hold();
	});
	function grid_greentire_hold(){
		var dataAdapter = new $.jqx.dataAdapter({
		datatype: "json",
      datafields: [
          //{ name: "ID", type: "string"},
          { name: "CodeID", type: "string" },
          { name: "Barcode", type: "string" },
          { name: "GT_Code", type:"string"},
          { name: "Batch", type:"string"},
          { name: "DefectID", type:"string"},
          { name: "DefectDesc", type:"string"},
          { name: "UpdateDate", type:"date"},
          { name: "BuildingNo", type:"string"},
          { name: "DateBuild", type:"date"},
          { name: 'Shift', type: 'string'},
          { name: 'Disposal', type: 'string'}
      ],
      url : base_url+'/api/report/greentire/hold'
	});

	return $("#grid_greentire_hold").jqxGrid({
        width: '100%',
        source: dataAdapter,
       autoheight: true,
        altrows : true,
        sortable: true,
        filterable : true,
        showfilterrow : true,
        columnsresize: true,
        pageable: true,
        pageSize: 20,
        columns: [
         { text: 'No.', width: 50,
         	cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
          		return '<div style=\'padding: 5px; color:#000000;\'> '+ (index+1) +' </div>';
            }
       	},
          { text:"วันที่", datafield: "UpdateDate", cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 180},
          { text:"Barcode", datafield: "Barcode", width: 150},
          { text:"GT Code", datafield: "GT_Code", width: 150},
          { text: 'Date Build', width: 180, datafield: 'DateBuild', cellsformat: 'yyyy-MM-dd HH:mm:ss'},
          { text: 'Build MC.', width: 100, datafield: 'BuildingNo'},
          { text: 'Shift', width: 100, datafield: 'Shift'},
          // { text: 'Disposition', width: 100, datafield: 'Disposal'},
          { text:"Description", datafield: 'DefectDesc'}             
          ]
	    });

	}
</script>