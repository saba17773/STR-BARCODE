<?php $this->layout("layouts/handheldmobile", ['title' => 'Repair']); ?>

<h1 class="head-text">Repair</h1>

<div class="panel panel-default form-center">
    <div class="panel-body">
        <form id="formRepair" onsubmit="return onRepair()">
            <div class="form-group">
                <label for="defectCode">Defect Code</label><br>
                <select name="defectCode" id="defectCode" class="form-control input-lg inputs" ></select>
            </div><br>
            <div class="form-group">
                <label for="barcode">Barcode</label><br>
                <input type="text" class="form-control input-lg" id="barcode" name="barcode" autocomplete="off">
            </div>
        </form>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        $('#barcode').focus();

        $('#modal_alert').on('hidden.bs.modal', function() {
            $('#barcode').val('').focus();
        });

        $('select[name=defectCode]').on('change', function() {
            $('#barcode').val('').focus();
        });

        gojax('get', base_url + '/api/defect/all').done(function(data) {
            $('select[name=defectCode]').html("<option value=''>= Please Select =</option>");
            $.each(data, function(index, val) {
                $('select[name=defectCode]').append('<option value="' + val.ID + '">' + val.ID + ' - ' + val.Description + '</option>');
            });
        });
    });

    function onRepair() {
        if ($.trim($('#barcode').val()) !== '') {

            $('#barcode').prop('readonly', true);
            var barcode_repair = $('#barcode').val();

            gojax('post', base_url + '/api/repair', {
                    barcode: $('#barcode').val(),
                    defect_code: $('#defectCode').val()
                })
                .done(function(data) {
                    if (data.status == 200) {
                        alert("Barcode ล่าสุด " + barcode_repair);
                        // $('#modal_alert').modal('hide');
                        // $('#top_alert').show();
                        // $('#top_alert_message').text('Barcode ล่าสุด ' + barcode_repair);
                    } else {
                        alert(data.message);
                        // $('#modal_alert').modal({
                        //     backdrop: 'static'
                        // });
                        // $('#modal_alert_message').text(data.message);
                        // $('#top_alert').hide();
                    }
                    $('#barcode').prop('readonly', false);
                    $('#barcode').val('').focus();
                })
                .fail(function() {
                    alert("ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้");
                    // $('#modal_alert').modal({
                    //     backdrop: 'static'
                    // });
                    // $('#modal_alert_message').text('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
                    // $('#top_alert').hide();

                    $('#barcode').prop('readonly', false);
                    $('#barcode').val('').focus();
                });
            $('#formRepair').trigger('reset');
        } else {
            // window.location = '?mode=danger&error=กรุณากรอกข้อมูล';
            alert("กรุณากรอกข้อมูลให้ครบถ้วน");
            // $('#modal_alert').modal({
            //     backdrop: 'static'
            // });
            // $('#modal_alert_message').text('กรุณากรอกข้อมูล');
            // $('#top_alert').hide();

            $('#barcode').prop('readonly', false);
            $('#barcode').val('').focus();

        }

        return false;
    }
</script>