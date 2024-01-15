<?php $this->layout("layouts/base", ["title" => "Report Greentire Curing Final"]); ?>

<!-- <h1>Report Final Hold</h1> -->
<style type="text/css">
  input[type=radio] {
    height: 1.3em;
    width: 1.3em;
  }
  th, td {
  padding: 5px;
  text-align: left;
}
</style>

<div id ="getdata">
	<font size="6"> <b>  รายงานยอดผลิต Greentire Curing Final </b></font>
  <br><br>

  <table>
    <tr>
      <td>
        <label>
          <input type="radio" name="type_location" value="gt" id="gt" > 
          <i style="color: green;">Greentire</i>
        </label>
      </td>
      <td>
        <label>
          <input type="radio" name="type_location" value="cure" id="cure"  > 
          <i style="color: orange;">Curing</i>
        </label>
      </td>
      <td>
        <label>
          <input type="radio" name="type_location" value="final" id="final"  > 
          <i style="color: red;">Final</i>
        </label>
      </td>
      <td>
        <label> | </label>
      </td>
    <!-- </tr>
    <tr> -->
      
      <td>
        <div id="prdg">
          <label>
            <input type="radio" name="type_curing" value="All" id="selec2" > 
            <i>All</i>
          </label>

          <label>
            <input type="radio" name="type_curing" value="RDT" id="selec1"  > 
            <i>RDT</i>
          </label>
        
          <label>
            <input type="radio" name="type_curing" value="TBR" id="selec3"  > 
            <i>TBR</i>
          </label>
        </div>
      </td>
      
    </tr>
  </table>

</div>

<hr>

<div id="grid_final_hold"></div>

<script type="text/javascript">



	jQuery(document).ready(function($){
		//grid_final_hold();
    $('#prdg').hide();
		$("#getdata").on('click',function (){

			var ProductGroup = $('input[name=type_curing]:checked').val();
      var LocationType = $('input[name=type_location]:checked').val();

      // console.log(ProductGroup);
      // console.log(LocationType);

			 if(LocationType)
			 {
          if (LocationType=="cure" || LocationType=="final") {
            $('#prdg').show();
          }else{
            $('#prdg').hide();
          }

          if (LocationType=="gt") {
            grid_final_hold(ProductGroup,"SEM");
          }
          
          if (LocationType && ProductGroup) {
            grid_final_hold(ProductGroup,LocationType);
          }
				 	
			 }

		});
	});
	function grid_final_hold(data,data2){


	// if(document.getElementById('selec1').checked == true)
	// {
	//
	// 	$data = 1;
	// }
	// if(document.getElementById('selec3').checked == true)
	// {
	//
	// 	$data = 2;
	// }

		var dataAdapter = new $.jqx.dataAdapter({
		datatype: "json",
      datafields: [
          { name: "CodeID", type: "string" },
          { name: "Barcode", type: "string" },
          { name: "CuringCode", type:"string"},
          { name: "Batch", type:"string"},
          { name: "DefectID", type:"string"},
          { name: "DefectDesc", type:"string"},
          { name: "UpdateDate", type:"date"},
          { name: 'NameTH', type:'string'},
					// { name: 'ProductGroup', type:'string'},
          { name: "PressNo", type:"string"},
          { name: "PressSide", type:"string"},
          { name: "GT_Code", type:"string"},
          { name: 'DateBuild', type:'date'},
          { name: 'Disposal', type: 'string'},
          { name: 'Shift', type: 'string'}
      ],
      // filter : function () {
      //   $('#grid_final_hold').jqxGrid('updatebounddata', 'filter');
      // },
      url : base_url+'/api/report/gtcurefinal/'+data+'/'+data2
	});

        return $("#grid_final_hold").jqxGrid({
            width: '100%',
            source: dataAdapter,
            autoheight: true,
            altrows: true,
            sortable: true,
            filterable: true,
            showfilterrow: true,
            columnsresize: true,
            pageable: true,
            pageSize: 20,
            editable: true,

        // theme : 'theme',
        columns: [
         { text: 'No.', width: 50,
         	cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
          		return '<div style=\'padding: 5px; color:#000000;\'> '+ (index+1) +' </div>';
            }
       	},
         { text:"วันที่", datafield: "UpdateDate", cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 150},
          { text:"Barcode", datafield: "Barcode", width: 100},
          { text:"Curing Code", datafield: "CuringCode", width: 100},
          { text: 'Item Name', datafield: 'NameTH', width: 400},
					  // { text: 'ProductGroup', datafield: 'ProductGroup', width: 100},
          { text: 'Batch', datafield: 'Batch', width: 100},
          { text:"Date Build", datafield: 'DateBuild', cellsformat: 'yyyy-MM-dd HH:mm:ss',  width: 180} ,
          { text:"GT Code", datafield: 'GT_Code', width: 100} ,
          { text:"Press No.", datafield: 'PressNo', width: 100} ,
          { text:"Press Side", datafield: 'PressSide', width: 100} ,
           { text:"Shift", datafield: 'Shift', width: 100} ,
           // { text: 'Disposition', datafield: 'Disposal', width: 100},
          { text:"Description", datafield: 'DefectDesc',width: 300}
          ]
	    });

	}
</script>
