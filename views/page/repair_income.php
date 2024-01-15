<?php $this->layout("layouts/base", ["title" => "Repair Income"]); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="margin: auto; max-width: 500px;">
    <div class="panel-heading">Repair Income</div>
    <div class="panel-body">

        <form id="form_repair_income">

			<div class="form-group">
                <label for="barcode">Barcode</label>
				<input type="text"  name="barcode" id="barcode" class="form-control input-lg" placeholder="Barcode" required>
			</div>

		</form>

    </div>
</div>

<script>
    jQuery(document).ready(function($) {

        $('#barcode').val('').focus();

        $('#form_repair_income').submit(function(e) {
            e.preventDefault();
            var _barcode = $('#barcode').val();
            var d = {};

            d = {
                method: "save",
                data: {
                    barcode: $('#barcode').val()
                }
            };

            gojax('post', '/api/v1/repair_income/'+d.method, d.data)
            .done(function(data) {
                if (data.result === false) {
                    console.log(data.message);
                    $('#modal_alert').modal({backdrop: 'static'});
                    $('#modal_alert_message').text(data.message); 
                    $('#top_alert').hide();        
                    // $('#barcode').prop('readonly', false);
                } else {
                    console.log(data.message);
                    $('#top_alert').show();
                    $('#top_alert_message').text(data.message + ' Barcode ล่าสุด ' + _barcode);
                    $('#modal_alert').modal('hide');
                }
                $('#form_repair_income').trigger('reset');
            }); 
        });

        $('#barcode').val('').focus('hidden.bs', function() {
            $('#top_alert').hide();
        });
    });
</script>
