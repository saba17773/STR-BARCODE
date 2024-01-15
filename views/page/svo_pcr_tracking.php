<?php $this->layout("layouts/base", ['title' => 'Tracking']); ?>

<style>
	.td-bold {
		font-weight: bold;
	}
</style>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
	<div class="panel-heading">WMS Barcode Tracking</div>
	<div class="panel-body">
		<form id="form_tracking">
			<div class="form-group">
				<input type="text" class="form-control input-lg" name="search" placeholder="Barcode" autocomplete="off">
			</div>
		</form>

		<p id="show_loading" style="display: none; padding: 10px 0px; text-align: center;">
			<img src="/assets/images/ajax-loader.gif" /> กำลังประมวณผล...
		</p>
	</div>
</div>

<!-- Modal -->
<div class="modal" id="modal_detail" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
					<span class="glyphicon glyphicon-remove"></span>
					ปิดหน้าต่างนี้
				</button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<!-- Content -->
				<table class="table">
					<tr style="font-size: 2em;">
						<td width="20%">Disposition</td>
						<td id="d_disposal"></td>
					</tr>

					<tr>
						<td class="td-bold">Status</td>
						<td id="d_status"></td>
					</tr>
					<tr>
						<td class="td-bold">Barcode Foil</td>
						<td id="d_barcode_foil"></td>
					</tr>
					<tr>
						<td class="td-bold">Building MC.</td>
						<td id="d_building_mc"></td>
					</tr>
					<tr>
						<td class="td-bold">Date Build</td>
						<td id="d_date_build"></td>
					</tr>
					<tr>
						<td class="td-bold">GT Code</td>
						<td id="d_gt_code"></td>
					</tr>
					<!--  -->
					<tr>
						<td class="td-bold">Curing Date</td>
						<td id="d_curing_date"></td>
					</tr>
					<tr>
						<td class="td-bold">Curing Code</td>
						<td id="d_curing_code"></td>
					</tr>
					<tr>
						<td class="td-bold">Item Id</td>
						<td id="d_item_id"></td>
					</tr>
					<tr>
						<td class="td-bold">Item Name</td>
						<td id="d_item_name"></td>
					</tr>
					<tr>
						<td class="td-bold">Batch No.</td>
						<td id="d_batch_no"></td>
					</tr>
					<tr>
						<td class="td-bold">Template Serial No.</td>
						<td id="d_template_no"></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Modal 2-->
<div class="modal" id="modal_detail_2" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
					<span class="glyphicon glyphicon-remove"></span>
					ปิดหน้าต่างนี้
				</button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<!-- Content -->
				<table class="table">
					<tr>
						<td class="td-bold" width="30%">AX ORDER</td>
						<td id="ax_order"></td>
					</tr>
					<tr>
						<td class="td-bold">ORDER NUMBER</td>
						<td id="order_number"></td>
					</tr>
					<tr>
						<td class="td-bold">LOAD ID</td>
						<td id="load_id"></td>
					</tr>
					<tr>
						<td class="td-bold">TRAILER ID</td>
						<td id="trailer_id"></td>
					</tr>
					<tr>
						<td class="td-bold">CUSTOMER ID</td>
						<td id="customer_id"></td>
					</tr>
					<tr>
						<td class="td-bold">CUSTOMER NAME</td>
						<td id="customer_name"></td>
					</tr>
					<tr>
						<td class="td-bold">ITEM ID</td>
						<td id="item_id"></td>
					</tr>
					<tr>
						<td class="td-bold">ITEM NAME</td>
						<td id="item_name"></td>
					</tr>
					<tr>
						<td class="td-bold">BRAND</td>
						<td id="brand"></td>
					</tr>
					<tr>
						<td class="td-bold">BATCH</td>
						<td id="batch"></td>
					</tr>
					<tr>
						<td class="td-bold">ORDER DATE</td>
						<td id="order_date"></td>
					</tr>
				
					<tr>
						<td class="td-bold">SHIP DATE</td>
						<td id="ship_date"></td>
					</tr>
				
					<tr>
						<td class="td-bold">STATUS</td>
						<td id="status"></td>
					</tr>
					
					<tr>
						<td class="td-bold">TEMPALTE SERIAL NO</td>
						<td id="template"></td>
					</tr>
				</table>


			</div>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		var barcode = $("input[name=search]");
		barcode.val('').focus();

		$('#modal_detail').on('hidden.bs.modal', function() {
			$('#show_loading').hide();
			$('input[name=search]').fadeIn();
			$(onFocus).val('').focus();
		});

		$('#modal_detail_2').on('hidden.bs.modal', function() {
			$('#show_loading').hide();
			$('input[name=search]').fadeIn();
			$(onFocus).val('').focus();
		});

		$('#modal_alert').on('hidden.bs.modal', function() {
			$('#show_loading').hide();
			$('input[name=search]').fadeIn();
			$(onFocus).val('').focus();
		});

		$('#form_tracking').submit(function(e) {
			e.preventDefault();
			$('#show_loading').show();
			$('input[name=search]').fadeOut();
			if ($.trim(barcode.val()) !== '') {
				gojax_f('post', base_url + '/api/search/svopcr', '#form_tracking')
					.done(function(data) {

					
						
						if (data == "") {
							$('#modal_alert_message').text("ไม่พบข้อมูล");
							$('#modal_alert').modal({
								backdrop: 'static'
							});
							onFocus = 'input[name=search]';
						} else {

							Date.prototype.addHours= function(h){
								this.setHours(this.getHours()+h);
								return this;
							}

							orderDate = new Date(data[0].ORDERDATE);
							orderDateTime =
							("00" + orderDate.getDate()).slice(-2) + "-" +
							("00" + (orderDate.getMonth() + 1)).slice(-2) + "-" +
							orderDate.getFullYear() + " " +
							("00" + orderDate.getHours()).slice(-2) + ":" +
							("00" + orderDate.getMinutes()).slice(-2) + ":" +
							("00" + orderDate.getSeconds()).slice(-2);

							shipDate = new Date(data[0].ACTUALSHIPDATE).addHours(7);
							shipDateTime =
							("00" + shipDate.getDate()).slice(-2) + "-" +
							("00" + (shipDate.getMonth() + 1)).slice(-2) + "-" +
							shipDate.getFullYear() + " " +
							("00" + shipDate.getHours()).slice(-2) + ":" +
							("00" + shipDate.getMinutes()).slice(-2) + ":" +
							("00" + shipDate.getSeconds()).slice(-2);

							$('#show_loading').hide();
							$('input[name=search]').fadeIn();
							$('#modal_detail_2').modal({
								backdrop: 'static'
							});
							$('.modal-title').text('Barcode : ' + data[0].SERIALNUMBER);
							$('#ax_order').text(data[0].EXTERNORDERKEY);
							$('#order_number').text(data[0].ORDERKEY);
							$('#load_id').text(data[0].LOADID);
							$('#trailer_id').text(data[0].TRAILERNUMBER);
							$('#customer_id').text((data[0].CONSIGNEEKEY));
							$('#customer_name').text(data[0].C_COMPANY);
							$('#item_id').text((data[0].SKU));
							$('#item_name').text(data[0].DESCR);
							$('#brand').text(data[0].SUSR7);
							$('#batch').text(data[0].LOTTABLE01);
							$('#order_date').text(orderDateTime);
							$('#ship_date').text(shipDateTime);
							$('#status').text(data[0].DESCRIPTION);
							$('#template').text(data[0].TEMPLATE);
							barcode.val('').focus();
							onFocus = 'input[name=search]';
						}

						// if (data.status != 404) {
						// 	$('#show_loading').hide();
						// 	$('input[name=search]').fadeIn();
						// 	$('#modal_detail_2').modal({
						// 		backdrop: 'static'
						// 	});
						// 	$('.modal-title').text('Barcode : ' + barcode.val());
						// 	$('#ax_order').text(data[0].EXTERNORDERKEY);
						// 	$('#order_number').text(data[0].ORDERKEY);
						// 	$('#load_id').text(data[0].LOADID);
						// 	$('#trailer_id').text(data[0].TRAILERNUMBER);
						// 	$('#customer_id').text((data[0].CONSIGNEEKEY));
						// 	$('#customer_name').text(data[0].C_COMPANY);
						// 	$('#item_id').text((data[0].SKU));
						// 	$('#item_name').text(data[0].DESCR);
						// 	$('#brand').text(data[0].SUSR7);
						// 	$('#batch').text(data[0].LOTTABLE01);
						// 	$('#order_date').text(data[0].ORDERDATE);
						// 	$('#ship_date').text(data[0].ACTUALSHIPDATE);
						// 	$('#status').text(data[0].DESCRIPTION);
						// 	barcode.val('').focus();
						// 	onFocus = 'input[name=search]';
						// } else {
						// 	$('#modal_alert').modal({
						// 		backdrop: 'static'
						// 	});
						// 	$('#modal_alert_message').text("ไม่พบข้อมูล");
						// 	onFocus = 'input[name=search]';
						// }

					});

			} else {
				$('#modal_alert').modal({
					backdrop: 'static'
				});
				$('#modal_alert_message').text("ไม่พบข้อมูล");
				onFocus = 'input[name=search]';
			}
		});
	});
</script>