<?php $this->layout("layouts/handheldmobile", ['title' => 'Repair Outcome Mobile']); ?>

<h1 class="head-text">Repair Outcome</h1>

<div class="panel panel-default form-center">
    <div class="panel-body">

        <form id="form_repair_outcome_MB">

            <div class="form-group">
                <label for="barcode">Barcode</label><br>
                <input type="text" class="form-control input-lg" id="barcode" name="barcode" placeholder="Barcode" required>
            </div>

        </form>

    </div>
</div>

<script>
    jQuery(document).ready(function($) {

        $('#barcode').val('').focus();

        $('#form_repair_outcome_MB').submit(function(e) {
            e.preventDefault();
            var _barcode = $('#barcode').val();
            var d = {};

            d = {
                method: "save",
                data: {
                    barcode: $('#barcode').val()
                }
            };

            gojax('post', '/api/repair_outcome/'+d.method, d.data)
            .done(function(data) {
                if (data.result === false) {
                    console.log(data.message);
                    alert(data.message + " Barcode ล่าสุด " + _barcode);     
                } else {
                    console.log(data.message);
                    alert(data.message + " Barcode ล่าสุด " + _barcode);
                }
                $('#form_repair_outcome_MB').trigger('reset');
            }); 
        });
    });
</script>