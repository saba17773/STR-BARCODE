<?php $this->layout("layouts/base", ['title' => 'Component']);

use App\Services\ComponentService;
$PermissionService = new App\Services\PermissionService;
$getunit    = (new ComponentService)->getUnitData();
$getsection = (new ComponentService)->getSectionData();

$unitgood   = $getunit[0]['UnitGood'];
$unitscrap  = $getunit[0]['UnitScrap'];

?>
<style type="text/css">
  td { 
    padding: 10px;
    text-align: right;
  }
  .inner { 
    padding: 2px;
    text-align: left;
  }
  .tdunit {
    padding: 10px;
    text-align: left;
  }
  /*.datepicker{z-index:9999 !important;}*/
</style>

<h1 class="head-text">Component<br>
	<b style="font-size: 0.5em;">(
		<?php 
			$arr_secname=[];
			$getsection_ = json_decode($getsection);
			foreach ($getsection_ as $data) {
				// echo $data->SectionName.",";
				$arr_secname[] = $data->SectionName;
			}
			echo implode(",", $arr_secname);
		?>
	)</b>
</h1>

<form id="form_filter">
    <table align="center">
      <tr>
        <td>
          <label>วันที่&nbsp;&nbsp;&nbsp;&nbsp;</label>
        </td>
        <td>
          <div class="row">
            <div class="input-group">
	            <input type="text" id="date_component" name="date_component" class=form-control required  placeholder="เลือกวันที่..." />
	            <span class="input-group-btn">
		            <button class="btn btn-info" id="date_component_show" type="button">
		              <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
		            </button>
		        </span>
		    </div>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          	<div style="text-align: left;">
	          <label class="radio-inline">
	          <input type="radio" name="shift" id="shift1" value="1" style="width: 1.5em; height: 1.5em;"> 
	          <span style="padding-left: 10px;"><b>กะทำงาน กลางวัน</b></span>
	          </label>
	          <label class="radio-inline">
	          <input type="radio" name="shift" id="shift2" value="2" style="width: 1.5em; height: 1.5em;"> 
	          <span style="padding-left: 10px;"><b>กะทำงาน กลางคืน</b></span>
	          </label>
	        </div>
        </td>
      </tr>
    </table>
</form>

<?php 
if (isset($_SESSION["user_componentcomplete"])) :
// if ($PermissionService->getUserAction($_SESSION['user_permission'], 'completed_component') === true) :
?>
<!-- <button class="btn btn-primary" id="complete">Complete</button> -->
<!-- <h4><p id="txtcomplete"></p></h4>	 -->
<button onclick="return modal_complete_open()" class="btn btn-success" id="complete_list">Complete List</button>
<?php endif ?>

<!-- <button class="btn btn-lg btn-primary" id="btn_update"> แก้ไข </button> -->

<hr>
<div id="gridcomponent"></div>

<!-- dialog complete list -->
<div class="modal" id="modal_complete_list" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Section Component</h4>
      </div>
      <div class="modal-body">
      	<form id="form_complete_list" onsubmit="return submit_complete_list()">

      		<select name="section_list[]" multiple="multiple" id="section_list" style="width:300px;"></select>

      		<button class="btn btn-success">Complete</button>
      	</form>
      </div>
    </div>
  </div>
</div>

<!-- dialog time -->
<div class="modal" id="modal_time" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="glyphicon glyphicon-remove-circle"></span>
        </button>
        <h4 class="modal-title">Time </h4>
      </div>
      
		<div class="panel panel-default form-center">
		<div class="panel-body">

		    <form id="form_time">
		    <table align="center">
		      <tr>
		        <td colspan="5">
		          <div class="row">
		            
		            <div class="col-md-2">
		            <label>Start</label>
		            </div>

		            <div class="col-md-4">
		                  <div id="starttime"></div>
		            </div>
		            
		            <div class="col-md-2">
		            <label>End</label>
		            </div>
		            
		            <div class="col-md-4">
		                  <div id="endtime"></div>
		            </div>
		            
		          </div>
		        </td>
		      </tr>
		      <tr>
		        <td colspan="5"> 
		        <input type="hidden" name="id_time" id="id_time">
		        <button type="submit" class="btn btn-primary" id="btn_savetime"><i class="glyphicon glyphicon-save"></i> บันทึก</button>
		        <!-- <button type="reset" id="reset" class="btn btn-default"><i class="glyphicon glyphicon-refresh"></i> ล้างข้อมูล</button> -->
		        </td>
		        <td>
		        </td>
		      </tr>
		    </table>

		    </form>
		    
		</div>
		</div>

    </div>
  </div>
</div>

<!-- dialog update -->
<div class="modal" id="modal_update" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="glyphicon glyphicon-remove-circle"></span>
        </button>
        <h4 class="modal-title"> <p id="textcode"></p> </h4>
      </div>
      
		<div class="panel panel-default form-center">
		<div class="panel-body">

		    <form id="form_update">
		    <table align="center">
		      <tr>
		        <td>
		          <label>Act</label>
		        </td>
		        <td>
		          <input type="hidden" name="code" id="code" readonly>
		          <input type="hidden" name="item" id="item" readonly>
		          <input type="hidden" name="id" id="id" readonly>
		          <input class="form-control inputs" type="text" name="good" id="good" autocomplete="off" autofocus required>
		        </td>
		        <td class="tdunit">
		          <label><?php echo $unitgood; ?></label>
		        </td>
		      </tr>
		      <tr>
		        <td>
		          <label>Scrap</label>
		        </td>
		        <td>
		          <input class="form-control inputs" type="text" name="error" id="error" autocomplete="off" required>
		        </td>
		        <td class="tdunit">
		          <label><?php echo $unitscrap; ?></label>
		        </td>
		      </tr>
		      <tr>
		        <td>
		          <label>Defect</label>
		        </td>
		        <td>
		          <div class="row">
		          <div class="col-md-12">
		              <div class="input-group">
		                  <input type="text" class="form-control inputs" name="defectid" id="defectid" required>
		                  <span class="input-group-btn">
		                    <button class="btn btn-info" id="btn_scrap" type="button">
		                      <span class="glyphicon glyphicon-search" aria-hidden="true">เลือก</span>
		                    </button>
		                  </span>
		                </div>
		            </div>
		          </div>
		        </td>
		        <td colspan="2" align="left" ><h4><p id="defect_noexist"></p></h4></td>
		      </tr>
		      <tr>
		        <td valign="top">
		            <label>Start</label>
		        </td>
		        <td colspan="5">
		          <div class="row">
		            <div class="col-md-4">
		                  <div id="starttime"></div>
		            </div>
		            <div class="col-md-1">
		            <label>End</label>
		            </div>
		            <div class="col-md-2">
		                  <div id="endtime"></div>
		            </div>
		            
		          </div>
		        </td>
		      </tr>
		      <tr>
		        <td colspan="2"> 
		        <button type="submit" class="btn btn-lg btn-primary" id="btn_save"><i class="glyphicon glyphicon-save"></i> บันทึก</button>
		        <button type="reset" id="reset" class="btn btn-lg btn-default"><i class="glyphicon glyphicon-refresh"></i> ล้างข้อมูล</button>
		        </td>
		        <td>
		        </td>
		      </tr>
		    </table>

		    <div>
		      <label><h4>
		        <p id="submit_pastcode"></p>
		        <p id="submit_date"></p></h4>
		      </label>
		    </div>

		    </form>
		    
		</div>
		</div>

    </div>
  </div>
</div>

<!-- dialog defect -->
<div class="modal" id="modal_defect" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="glyphicon glyphicon-remove-circle"></span>
        </button>
        <h4 class="modal-title">สาเหตุ</h4>
      </div>
      <div class="modal-body">
      <form id="form_defect">
        <div class="form-group">
          <input type="hidden" name="id_defect" id="id_defect">
          <div id="griddefect"></div>
        </div>
      </form>
    </div>
  </div>
</div>


<script type="text/javascript">
	
	jQuery(document).ready(function($) {

		var date_set = '<?php echo date('d-m-Y') ?>';
	    $( "#date_component").val(date_set);

		$('#date_component').datepicker({
		    dateFormat: 'dd-mm-yy',
		    beforeShow: function (input) {
                $(input).css({
                    "position": "relative",
                    "z-index": 999
                });
            },
            onClose: function () { $('.ui-datepicker').css({ 'z-index': 0  } ); }  ,
		    onSelect: function(dateText){
	            	if ($("input[name=shift]:checked").val() == 1) {
	            		var shift = 1;
	            	}else if($("input[name=shift]:checked").val() == 2){
	            		var shift = 2;
	            	}
	            	loadgridcomponent($("#date_component").val(),shift);
		        }
		});

	    $('#date_component_show').click(function() {
	      $('#date_component').datepicker('show');
	    });

	    var time_set = '<?php echo date('H:i'); ?>';

	    if (time_set>='08:01' && time_set<='20:00') {
	      $("input[name=shift][value='1']").attr('checked', 'true'); 
	      var shift = 1;
	    }else{
	      $("input[name=shift][value='2']").attr('checked', 'true'); 
	      var shift = 2;
	    }

	    loadgridcomponent($("#date_component").val(),shift);

	    $("#shift1").on('change', function() {
	      if ($(this).is(':checked')) {
	         loadgridcomponent($("#date_component").val(),1);
	      }
	    });

	    $("#shift2").on('change', function() {
	      if ($(this).is(':checked')) {
	         loadgridcomponent($("#date_component").val(),2);
	      }
	    });

		$("#starttime").jqxDateTimeInput({
	        formatString: "HH:mm", 
	        // showTimeButton: true,
	        showCalendarButton: false, 
	        width: '80px',
	        height: '30px'
	  	});
	  	$("#endtime").jqxDateTimeInput({
		    formatString: "HH:mm", 
		    // showTimeButton: true,
		    showCalendarButton: false, 
		    width: '80px',
		    height: '30px'
	  	});

		$('#btn_update').on('click',function(event) {
			var selectedrowindex = $("#gridcomponent").jqxGrid('getselectedrowindex');
      		var rowdata = $("#gridcomponent").jqxGrid('getrowdata', selectedrowindex);
			
			if (rowdata) {
				$('#modal_update').modal({backdrop: 'static'});
				document.getElementById("textcode").innerHTML = "คุณกำลังแก้ไขโค้ด "+rowdata.PastCodeID;
				$('#pastcode').attr('readonly',true);
				$('#item_noexist').hide();
				$('#defect_noexist').hide();
				$('#code').val(rowdata.PastCodeID);
				$('#pastcode').val(rowdata.PastCodeID);
				$('#item').val(rowdata.ItemID);
				$('#good').val(rowdata.GoodQty);
				$('#error').val(rowdata.ErrorQty);
				$('#defectid').val(rowdata.DefectID);
				$('#starttime').val(rowdata.st);
				$('#endtime').val(rowdata.et);
				$('#id').val(rowdata.ID);
			}

		});

		$('#btn_code').on('click', function(event) {
			$('#pastcode').val('');
			$('#pastcode').attr('readonly',false);
			$('#pastcode').focus();
		});

		$('#pastcode').keydown(function(event) {
		    if (event.which === 13) {
		      if ($('#pastcode').val() === $('#code').val()) {
		      	// alert("Code เดิม");
		      	return false;
		      }

		      var item = $('#pastcode').val();
		      gojax('get', base_url + '/component/pastcode?item='+item)
		          .done(function(data) {
		            if (data.length > 0) {
		              $.each(data, function(index, val) {
		                $('#pastcode').val(val.PastCodeID);
		                $('#item').val(val.ItemID);
		                $('#item_noexist').hide();
		              });  
		            }else{
		              document.getElementById("item_noexist").style.color = "red";
		              document.getElementById("item_noexist").innerHTML = "ไม่พบโค๊ดนี้";
		              $('#item_noexist').show();
		              $('#pastcode').val("");
		              $('#pastcode').focus();
		            }
		      });
		    
		    }
		});

		$('#btn_scrap').on('click', function() {

			$('#modal_defect').modal({backdrop: 'static'});
		    griddefect();
		    $('#griddefect').on('rowdoubleclick', function (event){
		        var args = event.args;
		        var boundIndex = args.rowindex;        
		        var datarow = $("#griddefect").jqxGrid('getrowdata', boundIndex);
		        $('#defectid').val(datarow.DefectID);
		        $('#modal_defect').modal('hide');

		    }); 
		});

		$('#reset').on('click', function() {
	      $('#pastcode').focus();
	    });

		$('#btn_save').on('click',  function(event) {

			if($('#good').val()==""){
				$('#good').focus();
				return false;
			}else if($('#error').val()==""){
				$('#error').focus();
				return false;
			}else if($('#defectid').val()==""){
				$('#modal_defect').modal({backdrop: 'static'});
				griddefect();
			    $('#griddefect').on('rowdoubleclick', function (event){
			        var args = event.args;
			        var boundIndex = args.rowindex;        
			        var datarow = $("#griddefect").jqxGrid('getrowdata', boundIndex);
			        $('#defectid').val(datarow.DefectID);
			        $('#modal_defect').modal('hide');
			        $("#starttime").jqxDateTimeInput('focus');
			    });
				return false;
			}else if($('#starttime').val()==""){
				$("#starttime").jqxDateTimeInput('focus');
				return false;
			}else if($('#endtime').val()==""){
				$("#endtime").jqxDateTimeInput('focus');
				return false;
			}

      		gojax('post', '/component/update/barcode', {
	          	item 	 : $.trim($('#item').val()),
				good 	 : $.trim($('#good').val()),
				error 	 : $.trim($('#error').val()),
				defectid : $.trim($('#defectid').val()),
				starttime: $.trim($('#starttime').val()),
				endtime  : $.trim($('#endtime').val()),
				id 		 : $.trim($('#id').val())
	        }).done(function(data) {
	        	if (data.status==200) {
	        		$('#modal_update').modal('hide');
	        		$('#defect_noexist').hide();
	        		$('#gridcomponent').jqxGrid('updatebounddata');
	        		alert(data.message);
	        	}else{
	        		if(data.message=='defect_null'){
		              $('#defect_noexist').show();
		              $('#defectid').val("");
		              $('#defectid').focus();
		              document.getElementById("defect_noexist").style.color = "red";
		              document.getElementById("defect_noexist").innerHTML = "ไม่พบโรคนี้";
	        		}else{
	        		  alert(data.message);
	        		}
	        	}
	        });
	        return false;
		});

		$('#btn_savetime').on('click',  function(event) {
      		gojax('post', '/component/update/time', {
	          	starttime: $.trim($('#starttime').val()),
				endtime  : $.trim($('#endtime').val()),
				id 		 : $.trim($('#id_time').val())
	        }).done(function(data) {
	        	$('#gridcomponent').jqxGrid('updatebounddata');
	        	$('#modal_time').modal('hide');
	        	// console.log(data);
	        });
	        return false;
		});

		$('#griddefect').on('rowdoubleclick', function (event){
	        var args = event.args;
	        var boundIndex = args.rowindex;        
	        var datarowd = $("#griddefect").jqxGrid('getrowdata', boundIndex);
	        // console.log($('#id_defect').val());
	        gojax('post', '/component/update/defect', {
	          	defectid : datarowd.DefectID,
				id 		 : $('#id_defect').val()
	        }).done(function(data) {
	        	if (data.status==200) {
	        		$('#gridcomponent').jqxGrid('updatebounddata','cells');
	        		$('#modal_defect').modal('hide');
	        	}else{
	        		$('#gridcomponent').jqxGrid('updatebounddata','cells');
	        		$('#modal_defect').modal('hide');
	        	}
	        });

	    }); 

	    $('#complete').click(function() {
	    	if ($("input[name=shift]:checked").val() == 1) {
        		var shift = 1;
        		var shiftname = "กลางวัน";
        	}else if($("input[name=shift]:checked").val() == 2){
        		var shift = 2;
        		var shiftname = "กลางคืน";
        	}
	    	var date_component = $("#date_component").val();

	    	gojax('post', '/component/update/status', {
		        date_component  : date_component,
		        shift  			: shift
		    }).done(function (data) {
		        if (data.status==200) {
		        	alert("ดำเนิการสำเร็จ");
		        	$('#gridcomponent').jqxGrid('updatebounddata');
		        	// document.getElementById("txtcomplete").style.color = "red";
        			// document.getElementById("txtcomplete").innerHTML = "วันที่: "+date_component+ " กะ: "+shiftname+" ดำเนินการ Complete เสร็จสิ้น (ไม่สามารถแก้ไขได้)";
		        	// $('#txtcomplete').show();
		        	$('#complete').hide();
		        }else{
		        	alert("เกิดข้อผิดพลาด");
		        }

		    });
		    // console.log(date_component);

	    });

	    // $('#complete_list').click(function() {
	    // 	if ($("input[name=shift]:checked").val() == 1) {
     //    		var shift = 1;
     //    		var shiftname = "กลางวัน";
     //    	}else if($("input[name=shift]:checked").val() == 2){
     //    		var shift = 2;
     //    		var shiftname = "กลางคืน";
     //    	}
	    // 	var date_component = $("#date_component").val();

	    // 	console.log(date_component+"_"+shift);
	    // 	// gojax('post', '/component/update/status', {
		   //  //     date_component  : date_component,
		   //  //     shift  			: shift
		   //  // }).done(function (data) {
		   //  //     if (data.status==200) {
		   //  //     	alert("ดำเนิการสำเร็จ");
		   //  //     	$('#gridcomponent').jqxGrid('updatebounddata');
		   //  //     	$('#complete_list').hide();
		   //  //     }else{
		   //  //     	alert("เกิดข้อผิดพลาด");
		   //  //     }
		   //  // });
	    // });

	});

	$("#good").keypress(function (e) {
	    var character = String.fromCharCode(e.keyCode)
	    var newValue = this.value + character;
	    if (isNaN(newValue) || hasDecimalPlace(newValue, 2)) {
	        e.preventDefault();
	        return false;
	    }
	});

	$("#error").keypress(function (e) {
	    var character = String.fromCharCode(e.keyCode)
	    var newValue = this.value + character;
	    if (isNaN(newValue) || hasDecimalPlace(newValue, 2)) {
	        e.preventDefault();
	        return false;
	    }
	});

	$('#modal_defect').on('hidden.bs.modal', function () {
      // alert("defect close");

      	gojax('post', '/component/update/error', {
			id 		 : $('#id_defect').val()

        }).done(function(data) {
          if (data.status === 200) {
            $('#gridcomponent').jqxGrid('updatebounddata');
         
          // }else if(data.status === 201){
          // 	$('#gridcomponent').jqxGrid('updatebounddata');
          //   commit(true);
          }else{
          	$('#gridcomponent').jqxGrid('updatebounddata');
            
          }

        }).fail(function() {
          	alert("เกิดข้อผิดพลาด");
        // console.log(data);
        });
    });

	function hasDecimalPlace(value, x) {
	      var pointIndex = value.indexOf('.');
	      return  pointIndex >= 0 && pointIndex < value.length - x;
	}
	  
	$("#good").on("input", function() {
	    if (/^0/.test(this.value)) {
	      this.value = this.value.replace(/^0/, "")
	    }
	});
	  
	$("#error").on("input", function() {
	    if (/^0/.test(this.value)) {
	      this.value = this.value.replace(/^0/, "")
	    }
	});

	function griddefect(item) {

		var dataAdapter = new $.jqx.dataAdapter({
		datatype: "json",
		datafields: [
	      { name: "GroupID", type: "int" },
	      { name: "GroupName", type: "string" },
  		  { name: "GroupDescription", type: "string" },
          { name: "GroupDescriptionDetail", type: "string" },
          { name: "DefectID", type: "string" }
		],
		url : '/component/defect?item='+item
		});

		return $("#griddefect").jqxGrid({
			width: '100%',
			source: dataAdapter,  
			pageable: true,
			autoHeight: true,
			filterable: true,
			showfilterrow: true,
			enableanimations: false,
      		columnsresize: true,
			sortable: true,
			pagesize: 10,
			columns: [
        		{ text:"Group Scrap", datafield: "GroupName", width:'20%'},
			  	{ text:"Descriptiion", datafield: "GroupDescription", width:'50%'},
        		{ text:"DescriptionDetail", datafield: "GroupDescriptionDetail"}
			]
		});
	}

	function setDefectModal(row) {
	    var selectedrowindex = $("#gridcomponent").jqxGrid('getselectedrowindex');
	    var datarow = $("#gridcomponent").jqxGrid('getrowdata', row);
	    // console.log(datarow.ID);
	    $('#modal_defect').modal({backdrop: 'static'});
	    $('#id_defect').val(datarow.ID);
	    griddefect(datarow.ItemID);
	}

	function setShiftModal(row) {
	    var selectedrowindex = $("#gridcomponent").jqxGrid('getselectedrowindex');
	    var datarow = $("#gridcomponent").jqxGrid('getrowdata', row);
	    
	    if (confirm('คุณต้องการจะย้ายใช่ไหม ?')) {
	    	// alert(datarow.ID+","+datarow.Shift);
	    	gojax('post', '/component/update/shift', {
	          	id 		: datarow.ID,
				shift   : datarow.Shift
	        }).done(function(data) {
	        	if (data.status==200) {
	        		$('#gridcomponent').jqxGrid('updatebounddata');
	        		alert(data.message);
	        	}else{
	        		$('#gridcomponent').jqxGrid('updatebounddata');
	        		alert(data.message);
	        	}
	        	// console.log(data);
	        });
	        return false;
	    }

	}

	function setStartDateModal(row) {
	    var selectedrowindex = $("#gridcomponent").jqxGrid('getselectedrowindex');
	    var datarow = $("#gridcomponent").jqxGrid('getrowdata', row);
	    // console.log(datarow.ID);
	    $('#modal_time').modal({backdrop: 'static'});
	    $('#id_time').val(datarow.ID);
	}

	function loadgridcomponent(date_component,shift) {
			
		// check btn complete
		gojax('get', '/component/check/status', {
	        date_component  : date_component,
	        shift  			: shift
	    }).done(function (data) {
	    	if (data.status==200) {
	    		$('#complete').show();
	    		// $('#txtcomplete').hide();
	    	}else{
	    		// gojax('get', '/component/check/status/number', {
			    //     date_component  : date_component,
			    //     shift  			: shift
			    // }).done(function (data) {
			    // 	if (data.status==200) {
			    // 		document.getElementById("txtcomplete").style.color = "red";
       //  				document.getElementById("txtcomplete").innerHTML = "วันที่: "+date_component+ " กะ: "+data.shiftname+" ดำเนินการ Complete เสร็จสิ้น (ไม่สามารถแก้ไขได้)";
	    		// 		$('#txtcomplete').show();
			    // 	}else{
			    // 		$('#txtcomplete').hide();
			    // 	}
			    // });

	    		
	    		$('#complete').hide();
	    	}
	    });

	    // txt alert complete

	    // gojax('get', '/component/check/status/number', {
	    //     date_component  : date_component,
	    //     shift  			: shift
	    // }).done(function (data) {
	    // 	if (data.status==200) {
	    // 	document.getElementById("txtcomplete").style.color = "red";
     //    	document.getElementById("txtcomplete").innerHTML = "วันที่: "+date_component+ " กะ: "+data.shiftname+" ดำเนินการ Complete เสร็จสิ้น (ไม่สามารถแก้ไขได้)";
	    // 		$('#txtcomplete').show();
	    // 		$('#complete').hide();
	    // 	}else{
	    // 		$('#txtcomplete').hide();
	    // 		$('#complete').show();
	    // 	}
	    // });

	    var dataAdapter = new $.jqx.dataAdapter({
	    datatype: "json",
	    updaterow: function (rowid, rowdata, commit) {
	        gojax('post', '/component/update/barcode', {
				good 	 : rowdata.GoodQty,
				error 	 : rowdata.ErrorQty,
				starttime: rowdata.StartTime,
				endtime  : rowdata.EndTime,
				item     : rowdata.ItemID,
				id 		 : rowdata.ID

	        }).done(function(data) {
	          if (data.status === 200) {

	            if (rowdata.DefectID === null) {
	            	$('#modal_defect').modal({backdrop: 'static'});
	            	$('#id_defect').val(rowdata.ID);
	    			griddefect(rowdata.ItemID);

	    			$('#gridcomponent').jqxGrid('updatebounddata', 'cells');
	            	commit(true);
	            }else{
	            	$('#gridcomponent').jqxGrid('updatebounddata', 'cells');
	            	commit(true);
	            }

	          }else if(data.status === 201){
	          	$('#gridcomponent').jqxGrid('updatebounddata', 'cells');
	            commit(true);
	          }else if(data.status === 202){
	          	$('#gridcomponent').jqxGrid('updatebounddata', 'cells');
	            commit(false);
	          }else{
	          	$('#gridcomponent').jqxGrid('updatebounddata', 'cells');
	          }

	        }).fail(function() {
	          	commit(false);
	        // console.log(data);
	        });
	    },
	    datafields: [
	      { name: "ID", type: "int" },
	      { name: "ItemID", type: "string" },
	      { name: "SCH", type: "float" },
          { name: "GoodQty", type: "float" },
          { name: "ErrorQty", type: "float" },
          { name: "Batch", type: "string" },
          { name: "DefectID", type: "string" },
          { name: "PastCodeID", type: "string" },
          { name: "GroupDescription", type: "string" },
          { name: "Name", type: "string"},
          { name: "CreateDate", type: "date"},
          { name: "SectionName", type: "string"},
          { name: "StartTime", type: "date"},
          { name: "EndTime", type: "date"},
          { name: "UnitPD", type: "string"},
          { name: "UnitScrap", type: "string"},
          { name: "Status", type: "int"},
          { name: "Shift", type: "int"},
          { name: "SCHDate", type: "date"}
        ],
	      url : '/component/load?date_component='+date_component+'&shift='+shift
	    });

	    var setDefect = function (row, column, value) {
	      if (value !== "") {
	        return "<div style='padding:4px;'>" + value + "</div>";
	      } else {
	        return "<div style='font-size: 0.9em; padding:-2px 1px 1px 40px;'><button onclick='return setDefectModal("+row+")'>ใส่ Defect</button></div>";
	      }
	      
	    }

	    var setShift = function (row, column, value) {
	      if (value !== "") {
	        return "<div style='padding:4px;'>" + value + "</div>";
	      } else {
	        return "<div style='font-size: 0.9em; padding:4px 1px 1px 40px;'><button onclick='return setShiftModal("+row+")'>ย้ายกะ</button></div>";
	      }
	      
	    }

	    var setStartDate = function (row, column, value) {
	      if (value !== "") {
	        return "<div style='padding:4px;'>" + value + "</div>";
	      } else {
	        return "<div style='font-size: 0.9em; padding:-2px 1px 1px 40px;'><button onclick='return setStartDateModal("+row+")'>ใส่เวลา</button></div>";
	      }
	      
	    }
	    var unitgood = '<?php echo $unitgood; ?>';
	    var unitscrap = '<?php echo $unitscrap; ?>';

	    return $("#gridcomponent").jqxGrid({
	        width: '100%',
	        source: dataAdapter,
	        autoheight: true,
	        columnsresize: true,
	        pageable: true,
	        filterable: true,
	        showfilterrow: true,
	        editable : true,
	        pagesize: 10,
	      columns: [
		        { text:"Code", datafield: "PastCodeID", width:'8%', editable:false},
		        { text:"SCH.", datafield: "SCH", width:'6%', editable:false},
				{ text:"ACT.", datafield: "GoodQty", width:'6%'},
				{ text:"Unit", datafield: "UnitPD", width:'3%', editable:false},
		        { text:"Scrap", datafield: "ErrorQty", width:'6%'},
		        { text:"Unit", datafield: "UnitScrap", width:'3%', editable:false},
		        { text:"Batch", datafield: "Batch", width:'6%', editable:false},
		        { text:"Defect", datafield: "GroupDescription",editable:false,width:'12%'},
		        { text:"Defect",  cellsrenderer : setDefect, editable:false,width:'6%'},
		        { text:"ไม่ได้ผลิต/ผลิตไม่ทัน",  cellsrenderer : setShift, editable:false,width:'10%'},
		        { text:"SCHDate", datafield: "SCHDate", width:'8%', cellsformat: 'yyyy-MM-dd' , filtertype: 'range', editable:false},
		        { text:"CreateBy", datafield: "Name", width:'10%', editable:false},
		        { text:"CreateDate", datafield: "CreateDate", width:'10%', cellsformat: 'yyyy-MM-dd HH:mm' , filtertype: 'range', editable:false},
		        
		        { text: 'Status', datafield: 'Status',filtertype: 'checkedlist',width:'6%',editable:false,filteritems: ['Open','Completed'],
                    
                    // cellsrenderer: function (row) {
                    cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata){
	                      var status;
	                         if (value =="1") {
	                             status =  "<div style='padding: 5px; background:LightBlue ; color:#000000; font-size:13px;'> Open </div>";
	                         }else if(value =="3"){
	                              status =  "<div style='padding: 5px; background:Chartreuse ; color:#000000; font-size:13px;'> Completed </div>";
	                         }
	                         return status;
	                  }
                }

			]
	    });
	}

	function modal_complete_open() {
		$('#section_list').html("");
		$('#section_list').multipleSelect();

		$('#modal_complete_list').modal({backdrop: 'static'});
		$('#form_complete_list').trigger('reset');

	 	getSectionData()
	    .done(function(data) {
	        $.each(data, function(k, v) {
	            $('#section_list').append('<option value="'+ v.SectionID+'">'+v.SectionName+'</option>');
	      	});
	      	$('#section_list').multipleSelect();
	  	});
		
	}

	function getSectionData() {
	    return $.ajax({
	      url : base_url+'/component/get/section',
	      type : 'get',
	      dataType : 'json',
	      cache : false
	    });
	}

	function submit_complete_list() {
		if ($("input[name=shift]:checked").val() == 1) {
    		var shift = 1;
    	}else if($("input[name=shift]:checked").val() == 2){
    		var shift = 2;
    	}
    	var date_component = $("#date_component").val();

		$.ajax({
	        url : '/component/update/status',
	        type : 'post',
	        cache : false,
	        dataType : 'json',
	        // data : $('form#form_complete_list').serialize()
	        data : {
	        	date_component : date_component,
	        	shift : shift,
	        	section_list : $('#section_list').val()
	        }
	      })
	      .done(function(data) {
	        // console.log(data);
	        alert(data.message);
	        $('#gridcomponent').jqxGrid('updatebounddata');
		    $('#modal_complete_list').hide();
	    });

		return false;
	}

</script>