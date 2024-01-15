<?php $this->layout("layouts/handheldmobile", ['title' => 'Hold']); ?>

<h1 class="head-text">Hold</h1>

<div class="panel panel-default form-center">
    <div class="panel-body">
        <form id="holdForm" onsubmit="return hold()">


            <label class="radio-inline">
                <input type="radio" name="holdtype" value="1" style="width: 1.5em; height: 1.5em;" >
                <span><b> Normal</b></span>
            </label>
            <br>
            <label class="radio-inline">
                <input type="radio" name="holdtype" value="2" style="width: 1.5em; height: 1.5em;" >
                <span><b> Mode Light Buff</b></span>
            </label>
            <br>
            <br>
            <div class="form-group">
                <label for="defectCode">Defect Code</label><br>
                <select name="defectCode" id="defectCode" class="form-control input-lg inputs" ></select>
            </div><br>
            <div class="form-group">
                <label for="holdInput">Barcode</label><br>
                <input type="text" class="form-control input-lg" name="holdInput" id="holdInput" autocomplete="off">
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {


        $('#holdInput').focus();

        $('#app_alert').on('click', function() {
            $('#holdInput').val('').focus();
        });

        $('#modal_alert').on('hidden.bs.modal', function() {
            $('#holdInput').val('').focus();
        });

        $('select[name=defectCode]').on('change', function() {
            $('#holdInput').val('').focus();
        });

        gojax('get', base_url + '/api/defect/all').done(function(data) {
            $('select[name=defectCode]').html("<option value=''>= Please Select =</option>");
            $.each(data, function(index, val) {
                $('select[name=defectCode]').append('<option value="' + val.ID + '">' + val.ID + ' - ' + val.Description + '</option>');
            });
        });

    });

    function hold() {

        var holdtype = $('input[name=holdtype]:checked').val();
       
        // alert(position_scrap); exit();

        var holdInput = $('#holdInput');
        //var holdtype = 1;

       
        // if ($('input[name=holdtype]:checked').val() == 2) {
        //     var holdtype = 2;
        // }
        if (!!holdInput.val()) {
            var barcode_hold = holdInput.val();
            gojax('post', base_url + '/api/hold', {
                    barcode: holdInput.val(),
                    defect: $('#defectCode').val(),
                    holdtype: holdtype
                })
                .done(function(data) {
                    if (data.status == 200) {
                        alert("Barcode ล่าสุด" + barcode_hold);
                        // $('#top_alert').show();
                        // $('#top_alert_message').text('Barcode ล่าสุด ' + barcode_hold);
                        // $('#modal_alert').modal('hide');
                    } else {
                        // window.location = '?mode=danger&error='+data.message+'&barcode='+barcode_hold;
                        // $('#top_alert').hide();
                        // $('#modal_alert').modal({
                        //     backdrop: 'static'
                        // });
                        alert(data.message);
                        // $('#modal_alert_message').text(data.message);
                        $('#holdInput').val(barcode_hold);
                    }

                    $('#holdForm').trigger('reset');
                    $('#holdInput').val('').focus();

                });
        } else {
            alert("กรุณากรอกข้อมูลให้ครบถ้วน");
            $('#holdInput').val('').focus();
            // $('#top_alert').hide();
        }

        holdInput.val('').focus();

        return false;
    }
</script>