<?php

$this->layout("layouts/handheldmobile", ['title' => 'Handheld Login']);
//$this->layout("layouts/base", ['title' => 'Handheld Curing']);

// if (isset($_SESSION["user_login"])) {
//     header("Location: " . APP_ROOT . "/curing");
// }

?>
    <div>
<div class="panel-heading"><h1>Create Sent To warehouse</h1></div>

        <form id="form_issue_wh">
            <BR><BR>

            <label for="">Truck</label><br>
            <input type="text" name="truck" id="truck">
            <input type="hidden" value="Save" name="truckdes" id="truckdes" disabled>

            <input type="hidden" name="form_type">
            <input type="hidden" name="_id">

        </form>

    </div>

<div id="result"></div>

<script>
    jQuery(document).ready(function($) {
		$('input[name=truck]').val('').focus();
		//$('truck').val('').focus();

        //$('#truckdes').hide();
        // var truck = $('select[name=truck]');
        // $('input[type=radio]').on('click', function(event) {
        //     $('#hh_username').val('').focus();
        // });

        // $('#section').html("");


        // gojax('get', base_url + '/api/press/alltruck')
        //     .done(function(data) {
        //         $('select[name=truck]').html('<option value="">= เลือกข้อมูล =</option>');
        //         $.each(data, function(index, val) {
        //             $('select[name=truck]').append('<option value="' + val.PlateNumber + '">' + val.PlateNumber + '</option>');
        //         });
        //         // $('select._select').multipleSelect({placeholder:'เลือกข้อมูล'});
        //     });

        $('form#form_issue_wh').on('submit', function(event) {
            var truck = $('#truck').val();

            event.preventDefault();
            gojax('post', base_url + '/api/warehousesendtable/create', {

                    truck: truck

                })
                .done(function(data) {
                    if (data.status == 200) {
                        alert(data.message);
                        // $('#show-ok').show();
                        // $('#show-result-text').text(data.message);
                    } else {
                        // window.location = '?error='+data.message+'&barcode='+$('#barcode').val();
                        if (data.status == 405) {
                            alert('error');
                        } else {
                            alert(data.message);
                            // $('#show-error').show();
                            // // $('#show-ok').show();
                            // $('#show-error-text').text(data.message);
                            $('#truck').val('').focus();

                        }

                        // $('#showCureCode').hide();
                    }
                     //alert(data.message);
                    // console.log(data.message);
                    $('#truck').prop('readonly', false);
                    $('#truck').val('').focus();

                    // console.log(data);
                });

        });



        $('#show-error').on('click', function() {
            $('#show-error').hide();
            $('#top_alert').hide();
            $('#truck').val('').focus();
        });

        $('#show-ok').on('click', function() {
            $('#show-ok').hide();
            $('#top_alert').hide();
            $('#truck').val('').focus();
        });





    });
</script>