<?php

header('Content-Type: text/html; charset=utf-8');
$PermissionService = new App\Services\PermissionService;

//$detect = new \Mobile_Detect;

// if (!$detect->isMobile()) {
//     exit("กรุณาเข้าผ่านอุปกรณ์ Handheld เท่านั้น");
// }

?>
<!DOCTYPE html>
<html lang="en">
<style>
    .hide {
        display: none;
    }

    #show-error {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        background: red;
        text-align: center;
        color: #ffffff;
        width: 100%;
        height: 90%;
        padding: 20px;
        font-size: 30px;
    }

    #show-error2 {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        background: red;
        text-align: center;
        color: #ffffff;
        width: 100%;
        height: 90%;
        padding: 20px;
        font-size: 30px;
    }


    .hide {
        display: none;
    }

    #show-ok {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        background: green;
        text-align: center;
        color: #ffffff;
        width: 100%;
        height: 90%;
        padding: 20px;
        font-size: 30px;


    }

    input[type=text],
    input[type=password],
    select {
        border: 1px solid;
        width: 400px;
        margin: 5px 0px;
        font-size: 2em;
    }

    input:focus {
        background: #388e3c;
        color: white;
        font-weight: bold;
    }

    input:blur {
        background: white;
        color: black;
    }

    label {
        font-size: 1.5em;
        font-weight: bold;
    }
</style>

<head>
    <!-- <meta http-equiv="x-ua-compatible" content="ie=edge"> -->
    <!-- <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no"> -->
    <!-- <meta charset="UTF-8"> -->
    <title>Barcode STR</title>
    <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->

    <script>
        var base_url = '';
        var api_url = '';
        var uuid = '<?php if (isset($_SESSION['user_login'])) echo $_SESSION['user_login']; ?>';
        var onFocus = '';
    </script>

    <script src="/assets/js/jquery-1.12.0.min.js"></script>
    <script src="/assets/js/bootstrap.min.js"></script>

    <script src="/assets/js/gojax.min.js"></script>
    <script src="/assets/js/qs.min.js"></script>

    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/jquery-ui.min.js"></script>

</head>

<body>



    <?php if (isset($_SESSION["user_login"])) : ?>

        <div class="panel panel-default" style="border-radius: 0px; margin-bottom: 0px;">
            <div class="panel-body" style="padding:0px;">
                <a id="link_logout" href="/user/logoutmobile">
                    <i class="glyphicon glyphicon-log-out"></i>
                    Log Out
                </a>

                </BR></BR>
                <select name="menu" id="menu" class="form-control input-lg inputs" onchange="location = this.value;" required></select>
                </BR>

            </div>
        </div>

        <div class="alert alert-success" id="top_alert" style="background: green;
   color:white;
   border: 0;
   font-weight: bold;
   font-size: 1.2em;
   text-align: center;
   display: none;" role="alert">
            <div id="top_alert_message"></div>
        </div>

    <?php endif; ?>


    <div class="container-fluid">
        <?php echo $this->section("content"); ?>
    </div>

    <script>
        jQuery(document).ready(function($) {

            gojax('get', base_url + '/api/menu/generateMobile').done(function(data) {
                $('select[name=menu]').html("<option value=''>SELECT MENU</option>");
                $.each(data, function(index, val) {
                    $('select[name=menu]').append('<option value="' + val.Link + '">' + val.Description + '</option>');
                });
            });

        });
    </script>

    <script>
        function close_window() {
            window.open('', '_self', '');