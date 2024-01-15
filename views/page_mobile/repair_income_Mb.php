<?php $this->layout("layouts/handheldmobile", ['title' => 'Repair Income Mobile']); ?>

<h1 class="head-text">Repair Income</h1>

<div class="panel panel-default form-center">
    <div class="panel-body">

        <form id="form_repair_income_MB">

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

        $('#form_repair_income_MB').submit(function(e) {
            e.preventDefault();
            var _barcode = $('#barcode').val();
            var d = {};

            d = {
                method: "save",
                data: {
                    barcode: $('#barcode').val()
                }
            };

            gojax('post', '/api/repair_income/'+d.method, d.data)
            .done(function(data) {
                if (data.result === false) {
                    console.log(data.message);
                    alert(data.message + " Barcode ล่าสุด " + _barcode);    
                } else {
                    console.log(data.message);
                    alert(data.message + " Barcode ล่าสุด " + _barcode);
                }
                $('#form_repair_income_MB').trigger('reset');
            }); 
        });
    });
</script>