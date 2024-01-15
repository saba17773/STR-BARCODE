<?php $this->layout("layouts/base", ['title' => 'Unrepair']); ?>

<div class="modal" id="modal_barcode" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Barcode</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <form id="formBarcode" onsubmit="return postUnrepair()">
        	<div class="form-group">
        		<label for="barcode">Barcode</label>
        		<input type="text" id="barcode" name="barcode" class="form-control inputs" autocomplete="off">
        	</div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="panel panel-default" style="margin: auto; max-width: 600px;">
  <div class="panel-heading">Authorize</div>
  <div class="panel-body">
    <form id="formAuthen">
    	<div class="form-group">
    		<label for="authen_code">Authorize Code</label>
    		<input type="text" id="authen_code" name="authen_code" class="form-control inputs" autocomplete="off">
    	</div>
    	<div class="form-group">
    		<label for="authen_pass">Password</label>
    		<input type="password" id="authen_pass" name="authen_pass" class="form-control inputs" autocomplete="off">
    	</div>
    </form>
  </div>
</div>


<!-- Modal -->
<!-- <div class="modal" id="modal_authen" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Authorize</h4>
      </div>
      <div class="modal-body">
        <form id="formAuthen">
        	<div class="form-group">
        		<label for="authen_code">Authorize Code</label>
        		<input type="text" id="authen_code" name="authen_code" class="form-control inputs" autocomplete="off">
        	</div>
        	<div class="form-group">
        		<label for="authen_pass">Password</label>
        		<input type="password" id="authen_pass" name="authen_pass" class="form-control inputs" autocomplete="off">
        	</div>
        </form>
      </div>
    </div>
  </div>
</div> -->

<script>
	jQuery(document).ready(function($) {
		$('#modal_authen').modal({backdrop:'static'});
		$('#authen_code').focus();

		$('#authen_pass').keydown(function(event) {
			/* Act on the event */
			if (event.which === 13) {
				var authen_code = $('#authen_code');
				var authen_pass = $('#authen_pass');
				if (!!authen_code.val() && !!authen_pass.val()) {
					$('#formAuthen').submit();
				} else {
					alert('กรุณากรอกข้อมูลให้ครบถ้วน');
				}
				
			}
		});

		$('#formAuthen').on('submit', function(event) {
			event.preventDefault();
			/* Act on the event */
			gojax('post', base_url+'/api/unrepair/authorize', {
				code: $('#authen_code').val(),
				pass: $('#authen_pass').val()
			})
			.done(function(data) {
				if (data.status == 200) {
					$('#modal_authen').modal('hide');
					$('#modal_barcode').modal({backdrop: 'static'});
					$('#barcode').focus();
				} else {
					$('#formAuthen').trigger('reset');
					alert(data.message);
				}
			});
		});

		$('form#formBarcode').on('submit', function(event) {
			event.preventDefault();
			/* Act on the event */
			var barcode = $('#barcode');
			if (!!barcode.val()) {
				gojax('post', base_url+'/api/unrepair', {
					barcode: barcode.val()
				})
				.done(function(data) {
					alert(data.message);
					$('#barcode').val('');
				});	
			} else {
				alert('กรุรากรอกข้อมูล');
			}
		});

	});
</script>