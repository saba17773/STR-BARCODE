<?php $this->layout("layouts/base", ['title' => 'Barcode Curing']); ?>

<div class="head-space"></div>

<div class="panel panel-default form-center">
	<div class="panel-heading">Barcode Curing</div>
	<div class="panel-body">
		<form onsubmit="return form_barcode_curing_submit()">
			<div class="form-group">
				<div class="row">
					<div class="col-md-12">
						<label for="press_no">Press No.</label>
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
						</div><!-- /input-group -->
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="press_side">Press Side</label>
				<select name="press_side" id="press_side" class="form-control" required></select>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-md-12">
						<label for="mold_no">Mold No.</label>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="input-group">
							<input type="text" class="form-control" name="mold_no" id="mold_no" required readonly>
							<span class="input-group-btn">
								<button class="btn btn-info" id="select_mold_no" type="button">
									<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
								</button>
							</span>
						</div><!-- /input-group -->
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-md-12">
						<label for="curing_code">Curing Code</label>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="input-group">
							<input type="text" class="form-control" name="curing_code" id="curing_code" required readonly>
							<span class="input-group-btn ">
								<button class="btn btn-info" id="select_curing_code" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
							</span>
						</div><!-- /input-group -->
					</div>
				</div>
			</div>
			<button type="submit" class="btn btn-lg btn-block btn-primary">
				<span class="glyphicon glyphicon-print"></span>
				Print
			</button>
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

<!-- Modal Select Mold No -->
<div class="modal" id="modal_select_mold_no" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Select Mold No.</h4>
			</div>
			<div class="modal-body">
				<!-- Content -->
				<div id="grid_mold_no"></div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Select Cure No -->
<div class="modal" id="modal_select_curing_code" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Select Curing No.</h4>
			</div>
			<div class="modal-body">
				<!-- Content -->
				<div id="grid_curing_code"></div>
			</div>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {

		getPressSide()
			.done(function(data) {
				$('select[name=press_side]').html('');
				$.each(data, function(index, val) {
					$('select[name=press_side]').append('<option value="' + val.ID + '">' + val.Description + '</option>');
				});
			});

		$('#select_press_no').on('click', function() {
			$('#modal_select_press_no').modal({
				backdrop: 'static'
			});
			$('#grid_press_no').jqxGrid('clearselection');
			grid_press_no();
		});

		$('#grid_press_no').on('rowdoubleclick', function() {
			var rowdata = row_selected('#grid_press_no');
			$('input[name=press_no]').val(rowdata.ID);
			$('#modal_select_press_no').modal('hide');
		});

		$('#select_mold_no').on('click', function() {
			$('#modal_select_mold_no').modal({
				backdrop: 'static'
			});
			$('#grid_mold_no').jqxGrid('clearselection');
			grid_mold_no();
		});

		$('#grid_mold_no').on('rowdoubleclick', function() {
			var rowdata = row_selected('#grid_mold_no');
			$('input[name=mold_no]').val(rowdata.ID);
			$('#modal_select_mold_no').modal('hide');
		});

		$('#select_curing_code').on('click', function() {
			$('#modal_select_curing_code').modal({
				backdrop: 'static'
			});
			$('#grid_curing_code').jqxGrid('clearselection');
			grid_curing_code();
		});

		$('#grid_curing_code').on('rowdoubleclick', function() {
			var rowdata = row_selected('#grid_curing_code');
			$('input[name=curing_code]').val(rowdata.Name);
			$('#modal_select_curing_code').modal('hide');
		});
	});

	function grid_curing_code() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [{
					name: 'ID',
					type: 'string'
				},
				{
					name: 'Name',
					type: 'string'
				}
			],
			url: base_url + '/api/curetire/all'
		});

		return $("#grid_curing_code").jqxGrid({
			width: '100%',
			source: dataAdapter,
			autoheight: true,
			pageSize: 10,
			altrows: true,
			pageable: true,
			sortable: true,
			filterable: true,
			showfilterrow: true,
			columnsresize: true,
			// theme: 'theme',
			columns: [{
					text: 'ID',
					datafield: 'ID',
					width: 100
				},
				{
					text: 'Description',
					datafield: 'Name',
					width: 100
				}
			]
		});
	}

	function grid_mold_no() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [{
					name: 'ID',
					type: 'string'
				},
				{
					name: 'Description',
					type: 'string'
				}
			],
			url: base_url + '/api/mold/all'
		});

		return $("#grid_mold_no").jqxGrid({
			width: '100%',
			source: dataAdapter,
			autoheight: true,
			pageSize: 10,
			altrows: true,
			pageable: true,
			sortable: true,
			filterable: true,
			showfilterrow: true,
			columnsresize: true,
			// theme: 'theme',
			columns: [{
					text: 'ID',
					datafield: 'ID',
					width: 100
				},
				{
					text: 'Description',
					datafield: 'Description',
					width: 100
				}
			]
		});
	}

	function grid_press_no() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [{
					name: 'ID',
					type: 'string'
				},
				{
					name: 'Description',
					type: 'string'
				}
			],
			url: base_url + '/api/press/all'
		});

		return $("#grid_press_no").jqxGrid({
			width: '100%',
			source: dataAdapter,
			autoheight: true,
			pageSize: 10,
			altrows: true,
			pageable: true,
			sortable: true,
			filterable: true,
			showfilterrow: true,
			columnsresize: true,
			// theme: 'theme',
			columns: [{
					text: 'ID',
					datafield: 'ID',
					width: 100
				},
				{
					text: 'Description',
					datafield: 'Description',
					width: 100
				}
			]
		});
	}

	function form_barcode_curing_submit() {

		var press_no = $('input[name=press_no]').val();
		var press_side = $('select[name=press_side]').val();
		var mold_no = $('input[name=mold_no]').val();
		var curing_code = $('input[name=curing_code]').val();

		var barcode = press_no + '@' + press_side + '@' + mold_no + '@' + curing_code;

		if (!!press_no && !!press_side && !!mold_no && !!curing_code) {
			window.open(base_url + '/generator/curing/' + barcode, '_blank');
		} else {
			// alert("กรุณากรอกข้อมูลให้ครบถ้วน");
			$('#modal_alert').modal({
				backdrop: 'static'
			});
			$('#modal_alert_message').text('กรุณากรอกข้อมูลให้ครบถ้วน');
		}

		return false;
	}

	function getPressSide() {
		return $.ajax({
			url: base_url + '/api/press_arm/all',
			type: 'get',
			dataType: 'json',
			cache: false
		});
	}
</script>