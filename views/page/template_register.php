<?php $this->layout("layouts/base", ['title' => 'Serial Register']); ?>

<div class="head-space"></div>

<div class="panel panel-default">
	<div class="panel-heading">Serial Register</div>
	<div class="panel-body">
		<div class="btn-panel">
			<a onclick="return open_modal()" href="#" class="btn btn-success" data-backdrop="static" data-toggle="modal" data-target="#modal_create">Create</a>
			<!-- <a onclick="return print()" href="#" class="btn btn-info">Print</a> -->
			<button class="btn btn-default" id="print">Print</button>
		</div>

		<div id="grid_template"></div>
	</div>
</div>



<!-- Create Modal -->
<div class="modal" id="modal_create" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Create new</h4>
			</div>
			<div class="modal-body">
				<form id="form_create" onsubmit="return submit_create_template()">
					<table class="table">
						<tr>
							<td>Date</td>
							<td>
								<input type="text" onchange="return on_date_change()" id="date_register" name="date_register" class=form-control required placeholder="เลือกวันที่..." />
							</td>
						</tr>
						<tr>
							<td>Start Barcode</td>
							<td>
								<input type="text" class="form-control" name="start_barcode" readonly>
							</td>
						</tr>
						<tr>
							<td>QTY</td>
							<td>
								<input type="text" class="form-control" name="qty" id="qty" onkeyup="return gen_barcode()" required>
							</td>
						</tr>
						<tr>
							<td>Finish Barcode</td>
							<td>
								<input type="text" class="form-control" name="finish_barcode" readonly required>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div class="modal-footer">
				<button onclick="return form_create_click_submit()" id="save_template" type="button" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('#date_register').focus();
		grid_template();
		setInt('#qty');

		$('#print').on('click', function() {
			var rowdata = row_selected('#grid_template');
			if (typeof rowdata !== 'undefined') {
				window.open(base_url + '/serial/print/' + rowdata.StartBarcode + '/' + rowdata.FinishBarcode, '_blank');
			}

		});

		// $('#qty').keydown(function(event) {
		// 	if ($('#qty').val() === '' ||
		// 		$('#qty').val() === null ||
		// 		typeof $('#qty').val() === 'undefined' ||
		// 		$('#qty').val() === 0) {
		// 		$('#save_template').prop('disabled', true);
		// 	} else {
		// 		$('#save_template').prop('disabled', false);
		// 	}
		// });
	});

	function open_modal() {
		$('form#form_create').trigger('reset');
		$("#date_register").datepicker({
			dateFormat: 'dd-mm-yy'
		});
		$('#save_template').prop('disabled', true);
	}

	function print() {
		var rowdata = row_selected('#grid_template');
		// window.open(base_url + '/generator/' + rowdata.);
		return false;
	}

	function on_date_change() {
		$('input[name=qty]').val('').focus();
		$('input[name=finish_barcode]').val('');
		var _date = $('input[name=date_register]').val().split('-');
		var y = _date[2].substring(3, 4);
		var m = parseInt(_date[1] - 1);
		var d = _date[0];
		var seq_num = '';

		var y_format = [
			"Z", "N", "W", "H",
			"U", "F", "S", "V",
			"E", "I"
		];

		var m_format = [
			'SN', 'EK',
			'KC', 'SP',
			'ST', 'WC',
			'MK', 'PS',
			'WS', 'KS',
			'PC', 'KK'
		]

		var seq_format = y_format[y] + m_format[m] + d;

		gojax('get', base_url + '/api/template/last', {
				seq_format: seq_format
			})
			.done(function(data) {
				seq_num += data.number;
				$('input[name=start_barcode]').val(seq_format + seq_num);
			})
			.fail(function() {
				alert('error');
			});
	}

	function gen_barcode() {
		var start_barcode = $('input[name=start_barcode]').val();
		var qty = $('input[name=qty]').val().replace(',', '');
		var finish_barcode = $('input[name=finish_barcode]');
		var barcode_qty = parseInt(start_barcode.substring(5, 9)) + (qty - 1);
		if (!!qty || qty <= 1000 || qty !== 0 || qty !== '' || qty !== null || typeof qty !== 'undefined') {

			finish_barcode.val(start_barcode.substring(0, 5) + ("0000" + barcode_qty).slice(-4));
			$('#save_template').prop('disabled', false);
		} else {
			$('#save_template').prop('disabled', true);
			finish_barcode.val('');
			$('input[name=qty]').val('');

		}
	}

	function submit_create_template() {
		var qty = $('input[name=qty]').val();
		var start = $('input[name=start_barcode]').val();
		var end = $('input[name=finish_barcode]').val();
		if (!!qty) {
			window.open(base_url + '/template/generator/' + start + '/' + end);
			$('#grid_template').jqxGrid('updatebounddata');
		}
		return false;
	}

	function form_create_click_submit() {
		var qty = $('input[name=qty]').val();
		var date_register = $('input[name=date_register]').val();
		if (!!qty && !!date_register && parseInt(qty) > 0) {
			$('#form_create').submit();
			$('#modal_create').modal('hide');
		}

	}

	function grid_template() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [{
					name: 'StartBarcode',
					type: 'string'
				},
				{
					name: 'FinishBarcode',
					type: 'string'
				}
			],
			url: base_url + "/api/template/all"
		});

		return $("#grid_template").jqxGrid({
			width: '100%',
			source: dataAdapter,
			autoheight: true,
			pageSize: 10,
			// rowsheight : 40,
			// columnsheight : 40,
			altrows: true,
			pageable: true,
			sortable: true,
			filterable: true,
			showfilterrow: true,
			columnsresize: true,
			// theme : 'theme',
			columns: [{
					text: 'StartBarcode',
					datafield: 'StartBarcode',
					width: 150
				},
				{
					text: 'FinishBarcode',
					datafield: 'FinishBarcode',
					width: 150
				}
			]
		});
	}
</script>