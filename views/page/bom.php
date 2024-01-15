<?php $this->layout("layouts/base", ["title" => "Bom"]); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="margin: auto; max-width: 500px;">
    <div class="panel-heading">Bom</div>
    <div class="panel-body">
        <form id="form_bom">
            <div class="form-group">
                <label>Item Set ID</label>
                <input type="text" class="form-control inputs" name="item_id" id="item_id" autofocus autocomplete="off">  
            </div>
            <div class="form-group">
                <label>Barcode</label>
                <input type="text" class="form-control inputs" name="barcode" id="barcode" autocomplete="off">
            </div>
        </form>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        //do jQuery stuff when DOM is ready
        //
        $('#modal_alert').on('hidden.bs.modal', function() {
            $('#item_id').focus();
        });
        
        $('#barcode').keydown(function(event) {
           if (event.which === 13) {
            $('#form_bom').submit();
           }
        });

        $('#form_bom').submit(function(e) {
            e.preventDefault();
            var _barcode = $('#barcode').val();
            var d = {};

            d = {
                method: "save",
                data: {
                    item_id: $('#item_id').val(),
                    barcode: $('#barcode').val()
                }
            };

            gojax('post', '/api/v1/bom/'+d.method, d.data)
            .done(function(data) {
                if (data.result === false) {
                    $('#top_alert').hide();
                    $('#modal_alert').modal({backdrop: 'static'});
                    $('#modal_alert_message').text(data.message);
                } else {
                    $('#top_alert').show();
                    $('#modal_alert').modal('hide');
                    $('#top_alert_message').text('Barcode ล่าสุด : ' + _barcode);
                }
                $('#form_bom').trigger('reset');
                 $('#item_id').focus();
                // onFocus = '#item_id';
            }); 
        });
    });
</script>