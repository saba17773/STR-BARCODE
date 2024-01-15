<?php $this->layout("layouts/base", ['title' => 'Mode Light Buff']); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;" id="fromserchWithdrawal">
	<div class="panel-heading">Mode Light Buff</div>
	<div class="panel-body">
		<form id="form_tracking">
			<div class="form-group">
				<input type="text" class="form-control input-lg" name="barcode" id="barcode" placeholder="Barcode" required>
			</div>
		</form>

	</div>
</div>


<script>
	jQuery(document).ready(function($) {

		var barcode = $("input[name=barcode]");
		barcode.val('').focus();
    $('#form_tracking').submit(function(e) {
			 e.preventDefault();
            if ($.trim(barcode.val()) !== '') {
			 		gojax_f('post', base_url+'/api/lightbuff/save', '#form_tracking')
			 		.done(function(data) {
					if (data.status == 200) {
                        $('#top_alert').show();
                        $('#top_alert_message').text(data.message);
                        $('#modal_alert').modal('hide');
					} 
                    else {
                        $('#modal_alert').modal({backdrop: 'static'});
                        $('#modal_alert_message').text(data.message); 
                        $('#top_alert').hide();        
                        $('#barcode').prop('readonly', false);
                    }
                });
            }
                    $('#barcode').prop('readonly', false);
		            $('#barcode').val('').focus();
        });

        $('#modal_alert').on('hidden.bs.modal', function() {
			$('#barcode').val('').focus();
		});
    });


    
 

  



</script>
