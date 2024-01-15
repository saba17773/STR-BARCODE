<?php $this->layout("layouts/base", ['title' => 'Tracking']); ?>

<div class="head-space"></div>

<div class="panel panel-default form-center">
	<div class="panel-heading">Tracking</div>
	<div class="panel-body">
		
		<form id="barcodeSearch" onsubmit="return barcodeSearch()">
			<div class="form-group">
				<input type="text" class="form-control input-lg" name="search" placeholder="ค้นหาจาก Barcode">
			</div>
		</form>

		<h3 id="txtSearchBox" style="display:none;">ผลการค้นหา : <span id="txtSearch" ></span></h3>
			<div id="search_result" style="padding:30px;"></div>

	</div>
</div>

<div id="headResult"></div>
<div id="lineResult"></div>

<script type="text/javascript">	
$("#modal_menu").modal({backdrop: 'static'});
$('input[name=search]').focus();
function barcodeSearch() {
	
	var barcode = $('input[name=search]').val();

	$('#txtSearchBox').show();
	$('#txtSearch').text('หมายเลข ' + barcode);

	search_bc(barcode)
		.done(function (data) {
			if (data.status == 404) {
				$('#search_result').show().html(data.message);
				$('#headResult').hide();
				$('#lineResult').hide();
				//$('#txtDetail').hide();
        
			} else {
				
				$('#headResult').show().css('margin-top', '20px');
				$('#lineResult').show().css('margin-top', '20px');
        		//$('#txtDetail').show();
				$('#search_result').hide();
				grid_head(data[0]);
				
				getTransLine(data[0].Barcode)
		          .done(function(data) {
		            grid_line(data);
		          })
		          .fail(function() {
		            $('#lineResult').hide();
		          });

			}
			
		});

	$('input[name=search]').val('').focus();

	return false;
}

function search_bc(barcode) {
	return $.ajax({
		url : base_url+'/api/search/barcode',
		type : 'post',
		cache : false,
		dataType : 'json',
		data : $('#barcodeSearch').serialize()
	});
}

function getTransLine(barcodeline) {
	return $.ajax({
	    url : base_url+'/api/search/barcode/line',
	    type : 'post',
	    cache : false,
	    dataType : 'json',
	    data : {
	      barcode : barcodeline
	    }
	});
}

	function grid_head(localData) {

		var dataAdapter = new $.jqx.dataAdapter({
			datatype: "json",
	        datafields: [
	            { name: "ID", type: "int" },
	            { name: "Barcode", type: "string"},
	            { name: "DateBuild", type: "date" },
	            { name: "BuildingNo", type: "string" },
	            { name: "GT_Code", type:"string"},
	            { name: "CuringDate", type:"date"},
	            { name: "CuringCode", type:"string"},
	            { name: "ItemID", type:"string"},
	            { name: "Bacth", type:"string"},
	            { name: "QTY", type:"int"},
	            { name: "Unit", type:"int"},
	            { name: "PressNo", type:"string"},
	            { name: "PressSide", type:"string"},
	            { name: "MoldNo", type:"string"},
	            { name: "TemplateSerialNo", type:"string"}
	        ],
	     	localData : localData
		});

		return $("#headResult").jqxGrid({
	        width: '100%',
	        source: dataAdapter,
	        autoheight: true,
	        sortable: true,
	        columnsresize: true,
	        columns: [
	          { text:"Barcode", datafield: "Barcode"},
	          { text:"DateBuild", datafield: "DateBuild",cellsformat: 'dd-MM-yyyy' },
	          { text:"BuildingNo", datafield: "BuildingNo"},
	          { text:"GT_Code",datafield: "GT_Code"},
	          { text:"CuringDate",datafield: "CuringDate",cellsformat: 'dd-MM-yyyy'},
	          { text:"CuringCode",datafield: "CuringCode"},
	          { text:"ItemID",datafield: "ItemID"},
	          { text:"Bacth",datafield: "Bacth"},
	          { text:"QTY",datafield: "QTY"},
	          { text:"Unit",datafield: "Unit"},
	          { text:"PressNo",datafield: "PressNo"},
	          { text:"PressSide",datafield: "PressSide"},
	          { text:"MoldNo",datafield: "MoldNo"},
	          { text:"TemplateSerialNo",datafield: "TemplateSerialNo"}
	        ]
	    });
	}

	function grid_line(localData) {

	var dataAdapter = new $.jqx.dataAdapter({
		datatype: "json",
        datafields: [
            { name: "TransID", type: "string"},
            { name: "Barcode", type: "string" },
            { name: "CodeID", type: "string" },
            { name: "Batch", type:"string"}
        ],
        localdata: localData
	});

	return $("#lineResult").jqxGrid({
        width: '100%',
        source: dataAdapter,
        autoheight: true,
        sortable: true,
        columnsresize: true,
        columns: [
          { text:"TransID", datafield: "TransID"},
          { text:"Barcode", datafield: "Barcode"},
          { text:"CodeID", datafield: "CodeID"},
          { text:"Batch", datafield: "Batch"}
        ]
	    });
	}
</script>