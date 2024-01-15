<?php $this->layout("layouts/base", ['title' => 'Onhand']); ?>
<h1>Report Onhand</h1>
<hr>
<!-- <div class="alert alert-danger" role="alert">
  ทีมพัฒนาตรวจสอบพบข้อผิดพลาดเล็กน้อยและกำลังเร่งแก้ไขโดยเร็ว ขออภัยในความไม่สะดวก
</div> -->
<hr>
<div id="gridonhand"></div>
<hr>
<script type="text/javascript">
	jQuery(document).ready(function($){
    
    gridonhand();

    setInterval(function() {
      $("#gridonhand").jqxGrid('updatebounddata', 'cells');
    }, 60000);
		
	});
	function gridonhand(){
		var dataAdapter = new $.jqx.dataAdapter({
		datatype: "json",
        datafields: [
            //{ name: "ID", type: "string"},
            { name: "CodeID", type: "string" },
            { name: "Batch", type:"string"},
            { name: "QTY", type:"int"},
            { name: "Location", type:"string"},
            { name: "Warehouse", type:"string"},
            { name: 'ItemName', type: 'string'},
        ],
        url : base_url+'/api/onhand/all'
	});

	return $("#gridonhand").jqxGrid({
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
        // theme : 'theme',
        columns: [
         // { text:"ID", datafield: "ID"},
          { text:"Code", datafield: "CodeID", width: 100},
          { text: "Item Name", datafield: 'ItemName', width: 600},
          { text:"Warehouse", datafield: "Warehouse", width: 150},
          { text:"Location", datafield: "Location", width: 150},
          { text:"Batch", datafield: "Batch", width: 100},
          { text:"QTY", datafield: "QTY", width: 100}               
          ]
	    });

	}
</script>