<?php $this->layout("layouts/base", ['title' => 'Curing']); ?>

<div class="head-space"></div>

<div class="panel panel-default form-center">
	<div class="panel-heading">ใบรายงานจำนวนยางที่อบ</div>
  <div class="panel-body">
    <form id="form_curing" method="post" action="<?php echo APP_ROOT; ?>/api/pdf/curingpress"
		onsubmit="return form_curing()" >

		<div class="form-group">
			<div class="row">
				<div class="col-md-12">
					<label for="date">Date</label>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="input-group">
				      <input type="text" class="form-control input-lg" name="date_curing" id="date_curing" required placeholder="เลือกวันที่..." >
				      <span class="input-group-btn">
				        <button class="btn btn-info btn-lg" id="date_curing_show" type="button">
				        	<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
				        </button>
				      </span>
				    </div>
				</div>
			</div>
	    </div>

		<div class="form-group">
			<div class="row">
				<div class="col-md-12">
					<label for="press_no">Press No.</label>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="input-group">
				      <input type="text" class="form-control input-lg" name="press_no" id="press_no" required readonly>
				      <span class="input-group-btn">
				        <button class="btn btn-info btn-lg" id="select_press_no" type="button">
				        	<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
				        </button>
				      </span>
				    </div>
				</div>
			</div>
		</div>

		

		<div class="form-group">
			<label for="shift">Shift</label>
			<select name="shift" id="shift" class="form-control input-lg" required>
				  <option value="day">กลางวัน</option>
				  <option value="night">กลางคืน</option>
				</select>
		</div>

		<button type="submit" class="btn btn-primary btn-lg btn-block">Print</button>

	</form>
  </div>
</div>

<!-- Modal Select Press No -->
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

<script type="text/javascript">
	jQuery(document).ready(function($) {

		$( "#date_curing" ).datepicker({dateFormat: 'dd-mm-yy'});

		$('#date_curing_show').click(function() {

		   $('#date_curing').datepicker('show');
		});

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
	        	{ name: 'Description', type: 'string'}
	        ],
	        url: base_url+'/api/press/all'
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
		        	{ text: 'ID', datafield: 'ID', width: 100},
		        	{ text: 'Description', datafield: 'Description', width: 100}
		        ]
		    });
	}

</script>
