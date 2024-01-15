<?php $this->layout("layouts/base", ['title' => 'Tracking']); ?>

<style>
	.td-bold {
		font-weight: bold;
	}
</style>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
	<div class="panel-heading">Tracking</div>
	<div class="panel-body">
		<form id="form_tracking">
			<div class="form-group">
				<input type="text" class="form-control input-lg" name="search" placeholder="Barcode" autocomplete="off">
			</div>
		</form>		

		<p id="show_loading" style="display: none; padding: 10px 0px; text-align: center;">
			<img src="/assets/images/ajax-loader.gif"/> กำลังประมวณผล...
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
       			<td class="td-bold" width="30%">SALE ORDER</td>
       			<td id="v3_so"></td>
       		</tr>
					<tr>
       			<td class="td-bold">CUSTOMER CODE</td>
       			<td id="v3_customer_code"></td>
       		</tr>
					<tr>
       			<td class="td-bold">CUSTOMER NAME</td>
       			<td id="v3_customer"></td>
       		</tr>
					<tr>
       			<td class="td-bold">ITEM ID</td>
       			<td id="v3_item_id"></td>
       		</tr>
					<tr>
       			<td class="td-bold">ITEM NAME</td>
       			<td id="v3_item_name"></td>
       		</tr>
					 <tr>
       			<td class="td-bold">PICKINGLIST ID</td>
       			<td id="v3_picking_list_id"></td>
       		</tr>
					<tr>
       			<td class="td-bold">PICKINGLIST DATE</td>
       			<td id="v3_picking_list_date"></td>
       		</tr>
					<tr>
       			<td class="td-bold">BATCH</td>
       			<td id="v3_batch"></td>
       		</tr>
					<tr>
       			<td class="td-bold">LOADING DATE</td>
       			<td id="v3_loading_date"></td>
       		</tr>
					<tr>
       			<td class="td-bold">SERIAL NO</td>
       			<td id="v3_serial"></td>
       		</tr>
					<tr>
       			<td class="td-bold">BARCODE FOIL</td>
       			<td id="v3_barcode_foil"></td>
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
				gojax_f('post', base_url+'/api/search/barcode2', '#form_tracking')
				.done(function(data) {

					$('#show_loading').hide();
					$('input[name=search]').fadeIn();
					if (data.status !== 404 && data[0].DATE_TYPE === 'SALE') {

						$('#modal_detail_2').modal({backdrop: 'static'});
						$('.modal-title').text('Barcode : ' + data[0].BARCODE);

						if ( data[0].SO_DSC == null ) { data[0].SO_DSC = data[0].SO_FACTORY; }
						if ( data[0].ITEM_ID == null ) { data[0].ITEM_ID = "-"; }
						if ( data[0].ITEM_NAME == null ) { data[0].ITEM_NAME = "-"; }
						if ( data[0].PICKINGLIST_ID == null ) { data[0].PICKINGLIST_ID = "-"; }
						if ( data[0].PICKINGLIST_DATE == null ) { data[0].PICKINGLIST_DATE = "-"; }
						if ( data[0].BATCH == null ) { data[0].BATCH = "-"; }
						if ( data[0].CREATE_DATE == null ) { data[0].CREATE_DATE = "-"; }
						if ( data[0].CUSTOMER_CODE == null ) { data[0].CUSTOMER_CODE = "-"; }
						if ( data[0].CUSTOMER_NAME == null ) { data[0].CUSTOMER_NAME = data[0].DELIVERY_NAME; }
						if ( data[0].SERIALNO == null ) { data[0].SERIALNO = "-"; }
						if ( data[0].BARCODE_FOIL == null ) { data[0].BARCODE_FOIL = "-"; }

						$('#v3_so').text(data[0].SO_DSC);
						$('#v3_item_id').text(data[0].ITEM_ID);
						$('#v3_item_name').text(data[0].ITEM_NAME);
						$('#v3_picking_list_id').text(data[0].PICKINGLIST_ID);
						$('#v3_picking_list_date').text( (data[0].PICKINGLIST_DATE).substring(0, 10) );
						$('#v3_batch').text(data[0].BATCH);
						$('#v3_loading_date').text( (data[0].CREATE_DATE).substring(0, 19) );
						$('#v3_customer_code').text(data[0].CUSTOMER_CODE);
						$('#v3_customer').text(data[0].CUSTOMER_NAME);
						$('#v3_serial').text(data[0].SERIALNO);
						$('#v3_barcode_foil').text(data[0].BARCODE_FOIL);
						
						barcode.val('').focus();		
						onFocus = 'input[name=search]';
						
					} else if (data.status !== 404 && data[0].DATE_TYPE === 'PROD') {
						$('#modal_detail').modal({backdrop: 'static'});
						$('.modal-title').text('Barcode : ' + data[0].BARCODE);
            
            var status_color = 'black';

            if (data[0].STATUSID === 1) {
              status_color = 'green';
            } else if (data[0].STATUSID === 2) {
              status_color = 'orange';
            } else if (data[0].STATUSID === 3) {
              status_color = 'red';
            } else if (data[0].STATUSID === 4) {
              status_color = 'red';
            } else if (data[0].STATUSID === 5) {
              status_color = 'orange';
            } else {
              status_color = 'black';
            }

						$('#d_disposal').text(data[0].DISPOSAL).css({
              'color': 'green',
              'font-weight': 'bold'
            });

						if ( data[0].BARCODEFOIL == null ) { data[0].BARCODEFOIL = "-"; }
						if ( data[0].BUILDINGMC == null ) { data[0].BUILDINGMC = "-"; }
						if ( data[0].BUILDINGDATE == null ) { data[0].BUILDINGDATE = "-"; }
						if ( data[0].GTCODE == null ) { data[0].GTCODE = "-"; }
						if ( data[0].CURINGDATE == null ) { data[0].CURINGDATE = "-"; }
						if ( data[0].CURINGCODE == null ) { data[0].CURINGCODE = "-"; }
						if ( data[0].ITEMID == null ) { data[0].ITEMID = "-"; }
						if ( data[0].ITEMNAME == null ) { data[0].ITEMNAME = "-"; }
						if ( data[0].BATCH == null ) { data[0].BATCH = "-"; }
						if ( data[0].TEMPLATE == null ) { data[0].TEMPLATE = "-"; }
						if ( data[0].STATUS == null ) { data[0].STATUS = "-"; }

            $('#d_barcode_foil').text(data[0].BARCODEFOIL);
						$('#d_building_mc').text(data[0].BUILDINGMC);
						$('#d_date_build').text(data[0].BUILDINGDATE);
						$('#d_gt_code').text(data[0].GTCODE);
						$('#d_curing_date').text(data[0].CURINGDATE);
						$('#d_curing_code').text(data[0].CURINGCODE);
						$('#d_item_id').text(data[0].ITEMID);
						$('#d_item_name').text(data[0].ITEMNAME);
						$('#d_batch_no').text(data[0].BATCH);
						$('#d_template_no').text(data[0].TEMPLATE);
            $('#d_status').text(data[0].STATUS).css({
              'font-weight': 'bold',
              'color': status_color
            });
						onFocus = 'input[name=search]';
					} else {
						$('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text("ไม่พบข้อมูล");
						onFocus = 'input[name=search]';
					}
				});
			} else {
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text("ไม่พบข้อมูล");
				onFocus = 'input[name=search]';
			}
		});
	});
</script>