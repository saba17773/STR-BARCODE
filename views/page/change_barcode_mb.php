<?php $this->layout("layouts/handheldmobile", ["title" => "Change Barcode mb"]); ?>

<div class="head-space"></div>
<br>
<div class="panel panel-default">
    <div class="panel-heading"><h1>Change Barcode</h1></div>
    <div class="panel-body">
        <form id="form_change_barcode">
            <div class="form-group">
                <label>Old Barcode</label><br>
                <input type="text" class="form-control inputs" name="old_barcode" id="old_barcode" autofocus autocomplete="off">  
            </div>
			<br>
            <div class="form-group">
                <label>New Barcode</label><br>
                <input type="text" class="form-control inputs" name="new_barcode" id="new_barcode" autocomplete="off">
            </div>
        </form>
    </div>
</div>

<script>
	jQuery(document).ready(function($) {

		$('input[name=old_barcode]').val('').focus();


		$('#modal_alert').on('hidden.bs.modal', function() {
      $(onFocus).focus();
  	});

		$('#new_barcode').keydown(function(event) {
			if (event.which === 13) {
				gojax('post', '/change_barcode/save', {
					old_barcode: $.trim($('#old_barcode').val()),
					new_barcode: $.trim($('#new_barcode').val())
				}).done(function(data) {
					if (data.result !== true ) {
						alert(data.message);

						// $('#top_alert').hide();
						// $('#modal_alert').modal({backdrop: 'static'});
						// $('#modal_alert_message').text(data.message);
					} else {
						alert("Barcode ใหม่ : "  + $('#new_barcode').val());

						// $('#top_alert').show();
						// $('#modal_alert').modal('hide');
						// $('#top_alert_message').text('Barcode ใหม่ : ' + $('#new_barcode').val());
						// setTimeout(function(){
						// 	$('#top_alert').hide();
						// }, 2000);
					}
					$('#form_change_barcode').trigger('reset');
					$('#old_barcode').focus();
					//onFocus = '#old_barcode';
				});
			}
		});
	});
</script>