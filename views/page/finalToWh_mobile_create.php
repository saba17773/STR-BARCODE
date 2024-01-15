<?php

$this->layout("layouts/mobile", ['title' => 'Handheld Login']);
//$this->layout("layouts/base", ['title' => 'Handheld Curing']);

// if (isset($_SESSION["user_login"])) {
//     header("Location: " . APP_ROOT . "/curing");
// }

?>




<center>
    <div>
        <form id="form_issue_wh" style="overflow: hidden;">
            <BR><BR>
            <table>
                <tr>

                    <td><b>Truck :</b></td>
                    <td>
                        <!-- <select name="truck" id="truck" class="form-control" required></select> -->
                        <input type="text" name="truck" id="truck" style="width:150px;margin-bottom: 5px;" autofocus autocomplete="off">
                    </td>
                    <td>&nbsp;&nbsp;</td>
                    <td> <button type="submit" class="btn btn-primary pull-right" id="bt_submit">Save</button>
                    </td>
                    <!-- <td>Authorize</td>
                            <td>
                               
                                <select name="round" id="round" class="form-control"></select>

                            </td> -->

                </tr>

            </table>
            <input type="hidden" name="form_type">
            <input type="hidden" name="_id">

        </form>

    </div>
</center>

<div id="result" style="text-align: center; margin: 0 auto; width: 200px; padding: 10px; display: none; color: red;"></div>

<script>
    jQuery(document).ready(function($) {
        $('#bt_submit').hide();
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
                        //alert(data.message);
                        $('#show-ok').show();
                        $('#show-result-text').text(data.message);
                    } else {
                        // window.location = '?error='+data.message+'&barcode='+$('#barcode').val();
                        if (data.status == 405) {
                            alert('error');
                        } else {
                            $('#show-error').show();
                            // $('#show-ok').show();
                            $('#show-error-text').text(data.message);

                        }

                        // $('#showCureCode').hide();
                    }
                    // alert(data.message);
                    // console.log(data.message);
                    $('#barcode').prop('readonly', false);
                    $('#barcode').val('').focus();
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