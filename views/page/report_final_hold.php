<?php $this->layout("layouts/base", ["title" => "Report Final Hold"]); ?>

<!-- <h1>Report Final Hold</h1> -->


<div id="getdata">

  <font size="6"> <b> Report Final Hold </b></font>
  <label>
    <input type="radio" name="type_curing" value="All" id="selec2"> All
  </label>
  <label>
    <input type="radio" name="type_curing" value="RDT" id="selec1"> RDT
  </label>
  <label>
    <input type="radio" name="type_curing" value="TBR" id="selec3"> TBR
  </label>

</div>

<button type="button" class="btn btn-success btn-xl " id="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button>

<hr>

<div id="grid_final_hold"></div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    //grid_final_hold();
    $('#to_excel').attr("disabled", true);
    $("#getdata").on('click', function() {

      var ProductGroup = $('input[name=type_curing]:checked').val();

      if (ProductGroup !== "" || ProductGroup !== "undefined") {
        grid_final_hold(ProductGroup);
        $('#to_excel').attr("disabled", false);
      } else {


        alert("กรุณาเลือกรายการ");

      }

    });
  });

  $('#to_excel').on('click', function(event) {
    var ProductGroup = $('input[name=type_curing]:checked').val();

    if (ProductGroup) {
        window.location.href = "/report/final/hold/excel/" + ProductGroup
      // $.get("/report/final/hold/excel/" + ProductGroup,
      //   function(data, status) {
      //     console.log(data)
      //   });
    } else {


      alert("กรุณาเลือกรายการ");

    }

  });

  function grid_final_hold(data) {


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
      datafields: [{
          name: "CodeID",
          type: "string"
        },
        {
          name: "Barcode",
          type: "string"
        },
        {
          name: "CuringCode",
          type: "string"
        },
        {
          name: "Batch",
          type: "string"
        },
        {
          name: "DefectID",
          type: "string"
        },
        {
          name: "DefectDesc",
          type: "string"
        },
        {
          name: "UpdateDate",
          type: "date"
        },
        {
          name: 'NameTH',
          type: 'string'
        },
        // { name: 'ProductGroup', type:'string'},
        {
          name: "PressNo",
          type: "string"
        },
        {
          name: "PressSide",
          type: "string"
        },
        {
          name: "GT_Code",
          type: "string"
        },
        {
          name: 'DateBuild',
          type: 'date'
        },
        {
          name: 'Disposal',
          type: 'string'
        },
        {
          name: 'Shift',
          type: 'string'
        }
      ],
      // filter : function () {
      //   $('#grid_final_hold').jqxGrid('updatebounddata', 'filter');
      // },
      url: base_url + '/api/report/final/hold/' + data
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

      // theme : 'theme',
      columns: [{
          text: 'No.',
          width: 50,
          cellsrenderer: function(index, datafield, value, defaultvalue, column, rowdata) {
            return '<div style=\'padding: 5px; color:#000000;\'> ' + (index + 1) + ' </div>';
          }
        },
        {
          text: "วันที่",
          datafield: "UpdateDate",
          cellsformat: 'yyyy-MM-dd HH:mm:ss',
          width: 150
        },
        {
          text: "Barcode",
          datafield: "Barcode",
          width: 100
        },
        {
          text: "Curing Code",
          datafield: "CuringCode",
          width: 100
        },
        {
          text: 'Item Name',
          datafield: 'NameTH',
          width: 400
        },
        // { text: 'ProductGroup', datafield: 'ProductGroup', width: 100},
        {
          text: 'Batch',
          datafield: 'Batch',
          width: 100
        },
        {
          text: "Date Build",
          datafield: 'DateBuild',
          cellsformat: 'yyyy-MM-dd HH:mm:ss',
          width: 180
        },
        {
          text: "GT Code",
          datafield: 'GT_Code',
          width: 100
        },
        {
          text: "Press No.",
          datafield: 'PressNo',
          width: 100
        },
        {
          text: "Press Side",
          datafield: 'PressSide',
          width: 100
        },
        {
          text: "Shift",
          datafield: 'Shift',
          width: 100
        },
        // { text: 'Disposition', datafield: 'Disposal', width: 100},
        {
          text: "Description",
          datafield: 'DefectDesc',
          width: 300
        }
      ]
    });

  }
</script>