<?php

$this->layout("layouts/handheldmobile", ['title' => 'Handheld Login']);

// if (isset($_SESSION["user_login"])) {
//     header("Location: " . APP_ROOT . "/greentire/incomingcheck_Mb");
// }

?>

<form id="form_hh_auth" style="padding: 10px; display: block; text-align: center;" onsubmit="return form_hh_submit()">

    <hr>
    <div style="text-align: center; font-weight: bold;">
        <select name="shift">
            <option value="">select shift</option>
            <option value="1">shift A</option>
            <option value="2">shift B</option>
            <option value="3">shift D</option>
        </select>
    </div>
    <hr>

    <label for="username_login">Username</label> <br />
    <input type="text" name="username_login" id="username_login" class="inputs">
    <br>
    <label for="password_login">Password</label> <br />
    <input type="password" name="password_login" id="password_login" class="inputs">

</form>

<div id="result" style="text-align: center; margin: 0 auto; width: 200px; padding: 10px; display: none; color: red;"></div>

<script>
    jQuery(document).ready(function($) {

        $('input[type=radio]').on('click', function(event) {
            $('#username_login').val('').focus();
        });
        $('select[name=shift]').on('change', function() {
            $('#username_login').val('').focus();
        });
        $('#password_login').keydown(function(event) {
            if (event.which === 13) {

                var u = $('#username_login').val();
                var p = $('#password_login').val();
                var s = $("input[name=shift]:checked");

                if (typeof u !== 'undefined' && !!s && !!p) {
                    gojax_f('post', base_url + '/api/user/desktop/auth', '#form_hh_auth')
                        .done(function(data) {
                            if (data.status == 200) {
                                // alert(1234);
                                window.location = base_url + '/page_mobile/handheld_login';
                                // location.reload();

                            } else {
                                $('#result').text(data.message).show();
                                $('#form_hh_auth').trigger('reset');
                                $('#hh_username').focus();
                            }
                        });
                } else {
                    $('#result').text('Please fill require data.').show();
                }

            }
        });
    });
</script>