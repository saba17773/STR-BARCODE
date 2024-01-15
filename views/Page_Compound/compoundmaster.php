<?php $this->layout("layouts/base", ["title" => "Movement Type"]); ?>

<h1>PD SCHEDULE</h1>

<div class="btn-panel">
	<!-- <button class="btn btn-success btn-lg"
		data-backdrop="static" data-toggle="modal" data-target="#modal_create">Create</button> -->
		<button class="btn btn-info btn-lg" id="create">Create</button>
	<button class="btn btn-info btn-lg" id="edit">Update</button>
	<button class="btn btn-danger btn-lg" id="delete">Delete</button>
  <button class="btn btn-warning btn-lg" id="Cancel" >Cancel</button>
</div>

<!-- Modal -->
<div class="modal" id="modal_create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Create</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <form id="formMovementType">
        	<div class="form-group row">
				<input type="text" name="idid" id="idid" value="" class="form-control" required>
        		<label for="id" class="col-sm-4 col-form-label">Mc.</label>
            <div class="col-sm-5">
							<select name="id" id="id" class="form-control" required></select>

          </div>
        	</div>
        	<div class="form-group row">
        		<label for="description" class="col-sm-4 col-form-label">Compound Code.</label>
						<div class="col-sm-5">
							<div class="input-group">
						      <input type="text" class="form-control" name="description" id="description" required readonly>
						      <span class="input-group-btn">
						        <button class="btn btn-info" id="select_press_no" type="button">
						        	<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
						        </button>
						      </span>
						    </div><!-- /input-group -->
						</div>
        	</div>

					<div class="form-group row">
						<label for="Mix" class="col-sm-4 col-form-label">Mix serial.</label>
						<div class="col-sm-5">
							<div class="input-group">
									<input type="text" class="form-control" name="Mix" id="Mix" required readonly>
									<span class="input-group-btn">
										<button class="btn btn-info" data-backdrop="static" data-toggle="modal" data-target="#modal_create1" id="Mix" name="Mix" type="button" onclick="grid_movementType1($data ='Mix')">
											<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
										</button>
									</span>
								</div><!-- /input-group -->
						</div>
					</div>

					<div class="form-group row">
						<label for="Remill" class="col-sm-4 col-form-label">Remill serial.</label>
						<div class="col-sm-5">
							<div class="input-group">
									<input type="text" class="form-control" name="Remill" id="Remill" required readonly>
									<span class="input-group-btn">
										<button class="btn btn-info" data-backdrop="static" data-toggle="modal" data-target="#modal_create1" id="Remill" name="Remill" type="button" onclick="grid_movementType1($data ='Remill')">
											<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
										</button>
									</span>
								</div><!-- /input-group -->
						</div>
					</div>




          <div class="form-group row">
        		<label for="Weight" class="col-sm-4 col-form-label">Weight (Kg.)</label>
            <div class="col-sm-5">
						<input type="text" name="Weight" id="Weight" class="form-control" required>
          </div>
        	</div>


        	<button class="btn btn-primary btn-lg" type="submit">Save</button><button class="btn btn-danger btn-lg" type="reset">Clear</button>

        </form>
      </div>
    </div>
  </div>
</div>
<!-- modul mixxand Remill  -->
<div class="modal" id="modal_create1" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Create</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div id="grid_movementType1"></div>
      </div>
    </div>
  </div>
</div>
<!-- remark  -->
<div class="modal" id="modal_remark" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Cancel</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->

				<div class="form-group">
					<form id="remarkfrom">
					<label for="id">Remark</label>
					<input type="text" name="remark" id="remark" class="form-control" required>
					<br>
					<button class="btn btn-primary btn-lg" type="submit">Save</button><button class="btn btn-danger btn-lg" type="reset">Clear</button>
				</form>
				</div>
      </div>
    </div>
  </div>
</div>
<!-- compound ID -->
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
        <div id="grid_MC"></div>
      </div>
    </div>
  </div>
</div>




<div id="grid_movementType"></div>

<script>
		jQuery(document).ready(function($) {
			grid_movementType();
			grid_movementType1();
			getPressSide()
				.done(function(data) {
				$('select[name=id]').html('');
				$('select[name=id]').append('<option value="">--เลือกรายการ--</option>');
				$.each(data, function(index, val) {
				$('select[name=id]').append('<option value="'+val.ID+'">'+val.Description+'</option>');
			});});
			$('#select_press_no').on('click', function() {
				if($('select[name=id]').val() == "")
				{
					alert("กรุณาเลือกเครื่อง");
					return false;
				}
				$('#modal_select_press_no').modal({backdrop:'static'});
				$('#grid_MC').jqxGrid('clearselection');
				grid_MC();
			});

			$('#grid_MC').on('rowdoubleclick', function() {
				var rowdata = row_selected('#grid_MC');
				$('input[name=description]').val(rowdata.ItemCompound);
				$('#modal_select_press_no').modal('hide');
			});

			$('#grid_movementType').on('click', function(event) {
				event.preventDefault();
				var rowdata = row_selected('#grid_movementType');
				if(typeof rowdata !== 'undefined') {
					if(rowdata.Description !== 'Open' )
					{
       		$('#edit').prop('disabled', true);
			  	$('#delete').prop('disabled', true);
				 	$('#Cancel').prop('disabled', true);
					}
			 		if(rowdata.Description == 'In-progress' )
					{
       		$('#Cancel').prop('disabled', false);
					}
					if(rowdata.Description == 'Open' )
		 			{
			 		$('#edit').prop('disabled', false);
			 		$('#delete').prop('disabled', false);
			 		$('#Cancel').prop('disabled', true);
					}
					if(rowdata.Description == 'Completed' )
			 		{
				 	$('#edit').prop('disabled', true);
				 	$('#delete').prop('disabled', true);
				 	$('#Cancel').prop('disabled', true);
					}
				}
				else {
					alert('กรุณาเลือกรายการ');
				}
			});

			$('#delete').on('click', function(event) {
				event.preventDefault();
				var rowdata = row_selected('#grid_movementType');
				if(typeof rowdata !== 'undefined') {
				alert(rowdata.ID);
				var IDID = rowdata.ID;
				gojax_f('post', base_url+'/api/Compound/deleteCompound/'+ IDID)
				.done(function(data) {
						if (data.status === 200) {
							$('#grid_movementType').jqxGrid('updatebounddata');
							$('#modal_create').modal('hide');
							document.getElementById("formMovementType").reset();
						} else {
							$('#modal_alert').modal({backdrop: 'static'});
							$('#modal_alert_message').text(data.message);
							$('#top_alert').hide();

						}
					});
			} else {
							$('#modal_alert').modal({backdrop: 'static'});
							$('#modal_alert_message').text('กรุณากรอกข้อมูล');
							$('#top_alert').hide();
						}
					});

				$('#Cancel').on('click', function(event) {
					event.preventDefault();
		 		// $("#modal_create").modal();
					var rowdata = row_selected('#grid_movementType');
					if(typeof rowdata !== 'undefined') {
						$('#modal_remark').modal();
						$('#remarkfrom').on('submit', function(event) {
							var IDID = rowdata.ID;
							gojax_f('post', base_url+'/api/Compound/updatestatus/'+ IDID, '#remarkfrom')
							.done(function(data) {
								if (data.status === 200) {
									$('#grid_movementType').jqxGrid('updatebounddata');
									$('#modal_create').modal('hide');
									document.getElementById("formMovementType").reset();
					} else {
						$('#modal_alert').modal({backdrop: 'static'});
						$('#modal_alert_message').text(data.message);
						$('#top_alert').hide();

					}

				});
			});
			} else {
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text('กรุณากรอกข้อมูล');
				$('#top_alert').hide();
			}

		});

			$('#create').on('click', function(event) {
				event.preventDefault();
				$('#idid').val(0);
				$('#modal_create').modal({backdrop: 'static'});


			});

			$('#edit').on('click', function(event) {
				event.preventDefault();
				var rowdata = row_selected('#grid_movementType');
				if (typeof rowdata !== 'undefined') {

					$('#idid').val(rowdata.ID);
					alert($('#idid').value);
					//$('#id').val(rowdata.McID).prop('readonly', true);
					$('#id').val(rowdata.McID);
					$('#description').val(rowdata.CompoundCodeID);
					if(rowdata.Type =='Mix')
					{
						$('#Mix').val(rowdata.Type);
						$('#Remill').val('');
					}
					else{
						$('#Remill').val(rowdata.Type);
						$('#Mix').val('');
					}
					$('#Weight').val(rowdata.Weight_kg);
					$('#modal_create').modal({backdrop: 'static'});

					$('select[name=localhosss]').html('');
					$.each(data, function(index, val) {
					$('select[name=localhosss]').append('<option value="'+val.ID+'">'+val.Description+'</option>');

					});
						}
				});

			$('#formMovementType').on('submit', function(event) {
				event.preventDefault();
				var _id = $('#id').val();
    		var Compound_Code = $('#description').val();
    		var Mix = $('#Mix').val();
    		var Remill = $('#Remill').val();
    		var Weight  = $('#Weight').val();
				if (!!$.trim(_id) && !!$.trim(Compound_Code)) {
				gojax_f('post', base_url+'/api/Compound/save', '#formMovementType')
					.done(function(data) {
						if (data.status == 200) {
							//alert(data.message);
							$('#modal_alert_message').text(data.message);
							$('#grid_movementType').jqxGrid('updatebounddata');
							$('#modal_create').modal('hide');
							document.getElementById("formMovementType").reset();
							} else {
							$('#modal_alert').modal({backdrop: 'static'});
							$('#modal_alert_message').text(data.message);
							$('#top_alert').hide();
							}
						});
					} else {
							$('#modal_alert').modal({backdrop: 'static'});
							$('#modal_alert_message').text('กรุณากรอกข้อมูล');
							$('#top_alert').hide();
						}
					});
				});

				function grid_movementType() {
					var dataAdapter = new $.jqx.dataAdapter({
						datatype: 'json',
        		datafields: [
        		{ name: 'ID', type: 'string'},
        		{ name: 'McID', type: 'string' },
           	{ name: 'CompoundCodeID', type: 'string'},
        		{ name: 'Type1', type: 'string' },
          	{ name: 'CompoundCodeTrans', type: 'string'},
        		{ name: 'Weight_kg', type: 'string' },
          	{ name: 'Palletcal', type: 'string'},
        		{ name: 'Use_Pallet', type: 'string' },
          	{ name: 'Name', type: 'string'},
        		{ name: 'Description', type: 'string' },
          	{ name: 'SCH_Plan', type: 'string'},
        		{ name: 'createdate', type: 'string' },
          	{ name: 'Remark', type: 'string' }

        	],
        	url: base_url + "/api/compound/all"
				});
				return $("#grid_movementType").jqxGrid({
      	width: '100%',
      	source: dataAdapter,
      	autoheight: true,
      	pageSize : 12,
      	// rowsheight : 40,
      	// columnsheight : 40,
      	altrows : true,
      	pageable : true,
      	sortable: true,
      	filterable : true,
      	showfilterrow : true,
      	columnsresize: true,
      	// theme : 'theme',
      	columns: [
        { text: 'ID.', datafield: 'ID', width: 100},
        { text: 'Mc.', datafield: 'McID', width: 100},
        { text: 'Compound Code', datafield: 'CompoundCodeID', width: 100},
        { text: 'Type', datafield: 'Type1', width: 100},
        { text: 'Compound Code', datafield: 'CompoundCodeTrans', width: 100},
        { text: 'Weight (Kg)', datafield: 'Weight_kg', width: 100},
        { text: 'Pallet', datafield: 'Palletcal', width: 100},
        { text: 'Use Pallet', datafield: 'Use_Pallet', width: 100},
				{ text: 'Status', datafield: 'Description', width: 100},
				{ text: 'Operator', datafield: 'Name', width: 100},
        { text: 'SCH Plan', datafield: '.SCH_Plan', width: 100},
        { text: 'create date/time', datafield: 'createdate', width: 100},
				{ text: 'Remark', datafield: 'Remark', width: 100},
				]
    	});
			}
			//  get remill and mix
			function grid_movementType1($data) {
				//alert($data);
				if($data == 'Mix')
				{
					$('#grid_movementType1').on('rowdoubleclick', function() {
						var rowdata = row_selected('#grid_movementType1');
						$('input[name=Mix]').val(rowdata.Compound_Code);
						$('input[name=Remill]').val('');
						$('#modal_create1').modal('hide');
					});
				}
				if($data == 'Remill')
				{
					$('#grid_movementType1').on('rowdoubleclick', function() {
						var rowdata = row_selected('#grid_movementType1');
						$('input[name=Remill]').val(rowdata.Compound_Code);
						$('input[name=Mix]').val('');
						$('#modal_create1').modal('hide');
				});
				}
				var dataserele = $data;
				var dataAdapter = new $.jqx.dataAdapter({
				datatype: 'json',
	        datafields: [
	        	{ name: 'Pallet_ID', type: 'string'},
	        	{ name: 'Compound_Code', type: 'string' },
						{ name: 'Type', type: 'string' }
					],
	        url: base_url + "/api/compound/remillandMix/"+ dataserele
			});

			return $("#grid_movementType1").jqxGrid({
	      width: '100%',
	      source: dataAdapter,
	      autoheight: true,
	      pageSize : 12,
	      // rowsheight : 40,
	      // columnsheight : 40,
	      altrows : true,
	      pageable : true,
	      sortable: true,
	      filterable : true,
	      showfilterrow : true,
	      columnsresize: true,
	      // theme : 'theme',
	      columns: [
	        { text: 'Pallet_ID.', datafield: 'Pallet_ID', width: 100},
	        { text: 'Compound Code.', datafield: 'Compound_Code', width: 100},




	      ]
	    });

		}
		function getPressSide() {
			return $.ajax({
				url : base_url + '/api/compound/Mc',
				type : 'get',
				dataType : 'json',
				cache : false
			});
		}

		function grid_MC() {
			var Mc = $('select[name=id]').val()
			alert(Mc);
			var dataAdapter = new $.jqx.dataAdapter({
					datatype: 'json',
		       datafields: [
		        { name: 'ItemCompound', type: 'string'},
		        { name: 'ItemID', type: 'string'},
	          { name: 'weight', type: 'string'}
		        ],
		        url: base_url+'/api/compound/Compound_Code/'+ Mc
					});
					return $("#grid_MC").jqxGrid({
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
			        	{ text: 'McID', datafield: 'ItemCompound', width: 100},
			        	{ text: 'Compound CodeID', datafield: 'ItemID', width: 100}
			        ]
			    });
		}

</script>
