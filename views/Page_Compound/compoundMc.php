<?php $this->layout("layouts/base", ['title' => 'Barcode Curing']); ?>

<div class="head-space"></div>
 <!-- form genarator -->
<div class="panel panel-default form-center">
	<div class="panel-heading">Select MC.</div>
  <div class="panel-body">
	<form onsubmit="return form_barcode_curing_submit()">
		<div class="form-group">
			<div class="row">
				<div class="col-md-12">
					<label for="press_no">MC.</label>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="input-group">
				      <input type="text" class="form-control" name="press_no" id="press_no" required readonly>
				      <span class="input-group-btn">
				        <button class="btn btn-info" id="select_press_no" type="button">
				        	<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
				        </button>
				      </span>
				    </div>
				</div>
			</div>
		</div>



		<button type="submit" class="btn btn-lg btn-block btn-primary">
			<span class="glyphicon glyphicon-plus"></span>
			ตกลง
		</button>
	</form>
  </div>
</div>

<!-- Modal Select Genarator -->
<div class="modal" id="modal_select_press_no" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Select Press No.</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div id="grid_press_no"></div>
      </div>
    </div>
  </div>
</div>




<script>
	jQuery(document).ready(function($) {
		$('#select_press_no').on('click', function() {
		$('#modal_select_press_no').modal({backdrop:'static'});
		$('#grid_press_no').jqxGrid('clearselection');
		grid_press_no();
		});

		$('#grid_press_no').on('rowdoubleclick', function() {
		var rowdata = row_selected('#grid_press_no');
		$('input[name=press_no]').val(rowdata.ID);
		$('#modal_select_press_no').modal('hide');
		});
		});

		function grid_press_no() {
		var dataAdapter = new $.jqx.dataAdapter({
		datatype: 'json',
	   	datafields: [
	    							{ name: 'ID', type: 'string'},
	      						{ name: 'Description', type: 'string'},
        						{ name: 'CompoundCodeID', type: 'string'}
	        				],
	  url: base_url+'/api/compound/Mc'
		});
		return $("#grid_press_no").jqxGrid({
		        width: '100%',
		        source: dataAdapter,
		        autoheight: true,
		        pageSize : 10,
		        altrows : true,
		        pageable : true,
		        sortable: true,
		        filterable : true,
		        showfilterrow : true,
		        columnsresize: true,
		        // theme: 'theme',
		        columns: [
		        					{ text: 'McID', datafield: 'ID', width: 100},
		        					{ text: 'Compound CodeID', datafield: 'Description', width: 100}
		        				]
		    });
			}

		function form_barcode_curing_submit()
		{
		//window.open(base_url + '/master/CompoundMcPallet', '_blank');
		var McID = $('input[name=press_no]').val();
		if (!!McID ) {
		gojax_f('post', base_url+'/api/Compound/tb/'+ McID)
		.done(function(data) {
		if (data.status === 200) {
		window.open(base_url + '/Page_compound/CompoundMcPallet/' + McID, '_blank');
		} else {
				//	alert(data.status);
		$('#modal_alert').modal({backdrop: 'static'});
		$('#modal_alert_message').text(data.message);
		$('#top_alert').hide();
		}
		});
		} else {
				// alert("กรุณากรอกข้อมูลให้ครบถ้วน");
		$('#modal_alert').modal({backdrop: 'static'});
		$('#modal_alert_message').text('กรุณากรอกข้อมูลให้ครบถ้วน');
		}
		return false;
		}


		</script>
