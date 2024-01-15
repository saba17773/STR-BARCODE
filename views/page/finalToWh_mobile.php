<?php

$this->layout("layouts/mobile", ['title' => 'Handheld Login']);

// if (isset($_SESSION["user_login"])) {
//     header("Location: " . APP_ROOT . "/curing");
// }


?>
<style>
    #show_remain {
        position: absolute;
        top: 100px;
        right: 70px;
        font-size: 6em;
        font-weight: bold;
    }

    #show_result {

        font-size: 3em;
        font-weight: bold;
    }
</style>




<left>
    <div>
        <form id="form_issue_wh">
            <table>




                <tr>

                    <td>Truck</td>
                    <td>
                        <!-- <select name="truck" id="truck" class="form-control" required></select> -->

                        <input type="text" name="truckshow" id="truckshow" style="width:150px;margin-bottom: 5px;" autofocus autocomplete="off">
                        <input type="hidden" id="typecheck" name="typecheck" value="1">
                        <input type="hidden" id="form_type" name="form_type" />
                        <input type="hidden" id="truck" name="truck" />
                        <input type="hidden" id="_id" name="_id" />
                    </td>

                </tr>
                <tr>
                    <td>&nbsp;&nbsp;
                    </td>
                <tr>
                    <td></td>
                    <td>


                        <input type="text" name="truckdes" id="truckdes" style="width:150px;margin-bottom: 5px;" autofocus autocomplete="off" disabled>

                    </td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;
                    </td>
                </tr>
                <tr>
                    <BR>
                    <td>Barcode</td>
                    <td>

                        <input type="text" name="barcode" id="barcode" class="inputs" style="width:150px;margin-bottom: 5px;" autofocus autocomplete="off">
                        <!-- <button type="submit" id="bt_suubmit"></button> -->

                    </td>
                </tr>

            </table>
        </form>



        <BR>
        <div id="show_result"></div>
        <BR>

    </div>


</left>
<center> <button class="btn btn-warning" id="btn_update" style="height:50px;width:120px"><b>Complete</b> <span id="text_user"></span></button></center>

<div id="show_remain"></div>

<div id="result" style="text-align: center; margin: 0 auto; width: 200px; padding: 10px; display: none; color: red;"></div>

<script>
    jQuery(document).ready(function($) {

        // $('#bt_suubmit').hide();
        // $('#section').html("");


        // gojax('get', base_url + '/api/press/alltmobiletruck')
        //     .done(function(data) {
        //         $('select[name=truck]').html('<option value="">= เลือกข้อมูล =</option>');
        //         $.each(data, function(index, val) {

        //             $('select[name=truck]').append('<option value="' + val.Id + '">' + val.TruckID + " " + val.JournalDescription + '</option>');
        //             // $('#hh_username').val('').focus();
        //         });

        //     });

        // setInterval(function() {
        //     var truck2 = $('#truck').val();
        //     gojax('get', base_url + '/api/invent/warehouse/total_finaltowh/' + truck2, )
        //         .done(function(data) {
        //             $('#show_remain').text(data.count);
        //         })
        //         .fail(function() {
        //             $('#show_remain').text();
        //         });
        // }, 3000);

        $("#truckshow").keyup(function(e) {
            // console.log('keyup called');
            var truck = $('#truckshow').val();
            var code = e.keyCode || e.which;
            // if (code === 13) e.preventDefault();
            if (code == '13') {
                $.ajax({
                        url: base_url + '/api/truck/check',
                        type: 'post',
                        cache: false,
                        dataType: 'json',
                        data: $('form#form_issue_wh').serialize()
                    })
                    .done(function(data) {
                        if (data.status == 200) {
                            $('#truckdes').val(data.roundcar);
                            $('#truck').val(data.ID);
                            $('#_id').val(data.ID);
                            var truck2 = $('#truck').val();
                            $('#show_remain').show();
                            gojax('get', base_url + '/api/invent/warehouse/total_finaltowh/' + truck2, )
                                .done(function(data) {

                                    $('#show_remain').text(data.count);
                                })
                                .fail(function() {
                                    $('#show_remain').text();
                                });


                            $('#show_result').hide();
                            $('#barcode').val('').focus();

                        } else {




                            $('#show-error2').show();

                            $('#show-error-text2').text(data.message);
                            $('#truckdes').val('');
                            $('#truck').val('');
                            $('#truckshow').val('');
                            $('#show_remain').hide();
                            $('#show_result').hide();
                            $('#truckdes').hide();



                        }
                        // open_button();
                    })
                    .fail(function() {
                        alert('ไม่สามารถเชื่อมต่อเครือข่ายได้');
                        //open_button();
                    });
            }
        });

        $("#barcode").focus(function() {
            $.ajax({
                    url: base_url + '/api/truck/check',
                    type: 'post',
                    cache: false,
                    dataType: 'json',
                    data: $('form#form_issue_wh').serialize()
                })
                .done(function(data) {
                    if (data.status == 200) {
                        $('#truckdes').val(data.roundcar);
                        $('#truck').val(data.ID);
                        //    $('#barcode').val('').focus();

                    } else {




                        $('#show-error2').show();

                        $('#show-error-text2').text(data.message);
                        $('#truckdes').val('');
                        $('#truck').val('');
                        $('#truckshow').val('');
                        $('#show_remain').hide();
                        $('#show_result').hide();
                        $('#truckdes').hide();

                    }
                    // open_button();
                })
                .fail(function() {
                    alert('ไม่สามารถเชื่อมต่อเครือข่ายได้');
                    //open_button();
                });

        });

        $('input[name=barcode]').keydown(function(event) {
            if (event.which === 13) {
                $('form#form_issue_wh').submit();
            }
        });


        $('form#form_issue_wh').on('submit', function(e) {
            e.preventDefault();

            /* Act on the event */
            var barcode = $('#barcode').val();
            var truck = $('#truck').val();
            var typecheck = $('#typecheck').val();

            if (!!$.trim(barcode)) {

                $('#barcode').prop('readonly', true);

                gojax('post', base_url + '/api/xray/issue/wh', {
                        barcode: barcode,
                        truck: truck,
                        typecheck: typecheck
                    })
                    .done(function(data) {
                        if (data.status == 200) {
                            $('#show_result').show();
                            $('#show_result').text(barcode).css("color", "green");
                            var truck2 = $('#truck').val();
                            gojax('get', base_url + '/api/invent/warehouse/total_finaltowh/' + truck2, )
                                .done(function(data) {
                                    $('#show_remain').text(data.count);
                                })
                                .fail(function() {
                                    $('#show_remain').text();
                                });
                            //alert(data.message);
                            // $('#show-ok').show();
                            // $('#show-error-text').text(data.message);
                        } else {
                            // window.location = '?error='+data.message+'&barcode='+$('#barcode').val();
                            if (data.status == 405) {
                                $('#show_result').show();
                                $('#show_result').text("Barcode number already exist.").css("color", "red");
                                $('#show-error').show();
                                // $('#show-ok').show();
                                $('#show-error-text').text("Barcode number already exist.");
                                $('#truckdes').hide();
                            } else {
                                $('#show_result').show();
                                $('#show_result').text(data.message).css("color", "red");
                                $('#show-error').show();
                                // $('#show-ok').show();
                                $('#show-error-text').text(data.message);
                                $('#truckdes').hide();
                            }

                            // $('#showCureCode').hide();
                        }
                        // alert(data.message);
                        // console.log(data.message);
                        $('#barcode').prop('readonly', false);
                        $('#barcode').val('').focus();
                        // console.log(data);
                    });
            } else {
                // alert("Barcode ไม่ถูกต้อง");
                // window.location = '?error=Barcode ไม่ถูกต้อง';
                $('#modal_alert').modal({
                    backdrop: 'static'
                });
                $('#modal_alert_message').text('Barcode ไม่ถูกต้อง');
                $('#barcode').prop('readonly', false);
                // $('#showCureCode').hide();
            }
        });

        // $('#truck').on('click', function() {

        //     var truck2 = $('#truck').val();
        //     gojax('get', base_url + '/api/invent/warehouse/total_finaltowh/' + truck2, )
        //         .done(function(data) {
        //             $('#show_remain').text(data.count);
        //         })
        //         .fail(function() {
        //             $('#show_remain').text();
        //         });


        //     $('#show_result').hide();




        // });

        $('#btn_update').on('click', function(event) {
            event.preventDefault();

            $('input[name=form_type]').val('update');

            // alert(1234);
            // exit();

            if (confirm('Are you sure?')) {
                $.ajax({
                        url: base_url + '/api/warehousesendtable/create',
                        type: 'post',
                        cache: false,
                        dataType: 'json',
                        data: $('form#form_issue_wh').serialize()
                    })
                    .done(function(data) {
                        if (data.status == 200) {
                            $('#show-ok').show();
                            $('#show-error-text').text("Complete");

                            location.reload();
                        } else {



                            //alert(data.message);
                            $('#show-error').show();
                            // $('#show-ok').show();
                            $('#show-error-text').text(data.message);
                        }
                        open_button();
                    })
                    .fail(function() {
                        alert('ไม่สามารถเชื่อมต่อเครือข่ายได้');
                        open_button();
                    });

            }

        });

        $('#show-error').on('click', function() {
            $('#show-error').hide();
            $('#top_alert').hide();
            $('#barcode').val('').focus();
            $('#truckdes').show();
        });

        $('#show-error2').on('click', function() {
            $('#show-error2').hide();
            $('#top_alert').hide();
            $('#truckshow').val('').focus();
            $('#truckdes').show();
        });


        $('#show-ok').on('click', function() {
            $('#show-ok').hide();
            $('#top_alert').hide();
            $('#barcode').val('').focus();
        });





    });
</script>