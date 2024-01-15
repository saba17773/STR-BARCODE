<?php

header('Content-Type: text/html; charset=utf-8');
$PermissionService = new App\Services\PermissionService;

$detect = new \Mobile_Detect;

if (!$detect->isMobile()) {
    exit("กรุณาเข้าผ่านอุปกรณ์ Handheld เท่านั้น");
}

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
</style>

<head>
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta charset="UTF-8">
    <title>Barcode STR</title>
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/assets/css/app.css" />
    <!-- JS -->
    <script>
        var base_url = '';
        var api_url = '';
        var uuid = '<?php if (isset($_SESSION['user_login'])) echo $_SESSION['user_login']; ?>';
        var onFocus = '';
    </script>
    <script src="/assets/js/jquery-1.12.0.min.js"></script>
    <script src="/assets/js/bootstrap.min.js"></script>
    <script src="/assets/select2/select2.min.js"></script>
    <script src="/assets/jquery-ui-1.12.1/jquery-ui.min.js"></script>
    <script src="/assets/js/jquery-ui-timepicker-addon.js"></script>
    <script src="/assets/jqwidgets/jqxcore.js"></script>
    <script src="/assets/jqwidgets/jqxinput.js"></script>
    <script src="/assets/jqwidgets/jqxdata.js"></script>
    <script src="/assets/jqwidgets/jqxbuttons.js"></script>
    <script src="/assets/jqwidgets/jqxbuttongroup.js"></script>
    <script src="/assets/jqwidgets/jqxscrollbar.js"></script>
    <script src="/assets/jqwidgets/jqxmenu.js"></script>
    <script src="/assets/jqwidgets/jqxlistbox.js"></script>
    <script src="/assets/jqwidgets/jqxdropdownlist.js"></script>
    <script src="/assets/jqwidgets/jqxgrid.js"></script>
    <script src="/assets/jqwidgets/jqxgrid.selection.js"></script>
    <script src="/assets/jqwidgets/jqxgrid.columnsresize.js"></script>
    <script src="/assets/jqwidgets/jqxgrid.filter.js"></script>
    <script src="/assets/jqwidgets/jqxgrid.sort.js"></script>
    <script src="/assets/jqwidgets/jqxgrid.pager.js"></script>
    <script src="/assets/jqwidgets/jqxgrid.edit.js"></script>
    <script src="/assets/jqwidgets/jqxdatetimeinput.js"></script>
    <script src="/assets/jqwidgets/jqxcalendar.js"></script>
    <script src="/assets/jqwidgets/jqxgrid.grouping.js"></script>
    <script src="/assets/jqwidgets/jqxwindow.js"></script>
    <script src="/assets/jqwidgets/jqxinput.js"></script>
    <script src="/assets/jqwidgets/jqxcheckbox.js"></script>
    <script src="/assets/jqwidgets/jqxpanel.js"></script>
    <script src="/assets/jqwidgets/jqxcombobox.js"></script>
    <script src="/assets/jqwidgets/jqxdropdownbutton.js"></script>
    <script src="/assets/jqwidgets/globalization/globalize.js"></script>
    <script src="/assets/js/fastclick.js"></script>
    <script src="/assets/js/jquery.maskMoney.min.js"></script>
    <script src="/assets/js/jquery.mask.min.js"></script>
    <script src="/assets/js/gojax.min.js"></script>
    <script src="/assets/js/qs.min.js"></script>
    <script src="/assets/js/multiple-select.js"></script>
    <script src="/assets/js/jquery.base64.min.js"></script>
    <script src="/assets/js/jquery.form-validator.min.js"></script>
    <script src="/assets/js/loadingoverlay.min.js"></script>
    <script src="/assets/js/moment.js"></script>
    <script src="/assets/js/xpire.js"></script>
    <script src="/assets/js/jqx_mod.js"></script>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/dayjs.min.js"></script>


</head>

<body>



    <?php if (isset($_SESSION["user_login"])) : ?>

        <div class="panel panel-default" style="border-radius: 0px; margin-bottom: 0px;">
            <div class="panel-body" style="padding:0px;">
                <ul class="nav nav-pills container-fluid" id="menu-loader" style="overflow-x: auto; white-space: nowrap;">
                    <li><a href="#">ไม่พบเมนูที่สามารถใช้งานได้</a></li>
                </ul>
            </div>
        </div>



    <?php endif; ?>

    <div id="show-error">
        <table border="0" width="100%">
            <tr>
                <td valign="top" align="center">
                    <!-- <img data-dismiss="modal" width="70" height="70" src="/assets/images/error01.png" alt=""> -->
                </td>
            </tr>
            <tr>
                <td valign="top" align="center">
                    <b id="show-error-text" style="color: white;"></b>
                </td>
            </tr>
        </table>
    </div>

    <div id="show-error2">
        <table border="0" width="100%">
            <tr>
                <td valign="top" align="center">
                    <!-- <img data-dismiss="modal" width="70" height="70" src="/assets/images/error01.png" alt=""> -->
                </td>
            </tr>
            <tr>
                <td valign="top" align="center">
                    <b id="show-error-text2" style="color: white;"></b>
                </td>
            </tr>
        </table>
    </div>

    <div id="show-ok">
        <table border="0" width="100%">
            <tr>
                <td valign="top" align="center">
                    <!-- <img data-dismiss="modal" width="70" height="70" src="/assets/images/error01.png" alt=""> -->
                </td>
            </tr>
            <tr>
                <td valign="top" align="center">
                    <b id="show-result-text" style="color: white;"></b>
                </td>
            </tr>
        </table>
    </div>


    <div class="container-fluid">
        <?php echo $this->section("content"); ?>
    </div>



    <!-- <script>
        jQuery(document).ready(function($) {

            $("#link_logout").on("click", function() {

                if ('<#?php echo $_SESSION["user_permission"]; ?>' === '1' ||
                    '<#?php echo $_SESSION['user_permission']; ?>' === '18') {
                    if (!confirm("คุณต้องการออกจากระบบหรือไม่ ?")) {
                        return false;
                    } else {
                        return true;
                    }

                } else {
                    return true;
                }
            });
        });
    </script> -->

    <script>
        function close_window() {
            window.open('', '_self', '');
            window.close();
        }
    </script>

</body>

</html>