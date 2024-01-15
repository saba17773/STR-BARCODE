<?php $PermissionService = new App\Services\PermissionService; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0">
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="image/png" href="/logo.png" />
    <title><?php echo $this->e($title); ?> - <?php echo APP_NAME; ?></title>
    <link rel="manifest" href="/manifest.json">
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/assets/jqwidgets/styles/jqx.base.css" />
    <link rel="stylesheet" href="/assets/jqwidgets/styles/theme.css" />
    <link rel="stylesheet" href="/assets/css/multiple-select.css" />
    <link rel="stylesheet" href="/assets/jquery-ui-1.12.1/jquery-ui.min.css" />
    <link rel="stylesheet" href="/assets/css/jquery-ui-timepicker-addon.css">
    <link rel="stylesheet" href="/assets/select2/select2.min.css">
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

    <!--[if lt IE 9]>
      <script src="/assets/js/html5shiv.js"></script>
      <script src="/assets/js/respond.js"></script>
    <![endif]-->
</head>

<body>
    <div id="http-loading"></div>
    <!-- <div id="http-loading"><img src="/assets/images/ajax-loader.gif" />	 กำลังประมวณผล... กรุณารอสักครู่...</div> -->

    <!-- Modal Alert-->
    <div class="modal" id="modal_alert" tabindex="-1" role="dialog" style="top: 80px; z-index: 9999999;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="background-color: red;">
                <div class="modal-header" style="border-bottom: 0px;">
                </div>
                <div class="modal-body">
                    <!-- Content -->
                    <table border="0" width="100%">
                        <tr>
                            <td valign="top" align="center">
                                <img data-dismiss="modal" width="200" height="200" src="/assets/images/error01.png"
                                    alt="">
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" align="center" style="color: white;">
                                <h1 id="modal_alert_message"></h1>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Warning-->
    <div class="modal" id="modal_warning" tabindex="-1" role="dialog" style="top: 80px; z-index: 9999999;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="background-color: yellow;">
                <div class="modal-header" style="border-bottom: 0px;">
                </div>
                <div class="modal-body">
                    <!-- Content -->
                    <table border="0" width="100%">
                        <tr>
                            <td valign="top" align="center">
                                <img data-dismiss="modal" width="200" height="200" src="/assets/images/error01.png"
                                    alt="">
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" align="center" style="color: black;">
                                <h1 id="modal_warning_message"></h1>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal pass-->
    <div class="modal" id="modal_alert_pass" tabindex="-1" role="dialog" style="top: 80px; z-index: 9999999;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="background-color: green;">
                <div class="modal-header" style="border-bottom: 0px;">
                </div>
                <div class="modal-body">
                    <!-- Content -->
                    <table border="0" width="100%">
                        <tr>
                            <td valign="top" align="center">
                                <img data-dismiss="modal" width="200" height="200" src="/assets/images/error01.png"
                                    alt="">
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" align="center" style="color: white;">
                                <h1 id="modal_alert_message_pass"></h1>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pass-->
    <div class="modal" id="modal_alert_pass_curring" tabindex="-1" role="dialog"
        style="top: 80px; z-index: 9999999;position:absolute;width: 100%;height: 100%;display: none;background: green;">

    </div>
    <!-- Modal Warning-->
    <div class="modal" id="modal_warning" tabindex="-1" role="dialog" style="top: 80px; z-index: 9999999;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="background-color: orange;">
                <div class="modal-header" style="border-bottom: 0px;">
                </div>
                <div class="modal-body">
                    <!-- Content -->
                    <table border="0" width="100%">
                        <tr>
                            <td valign="top" align="center">
                                <img data-dismiss="modal" width="200" height="200" src="/assets/images/error01.png"
                                    alt="">
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" align="center" style="color: black;">
                                <h1 id="modal_warning_message"></h1>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Warning_blue-->
    <div class="modal" id="modal_warning_blue" tabindex="-1" role="dialog" style="top: 80px; z-index: 9999999;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="background-color: #2E9AFE;">
                <div class="modal-header" style="border-bottom: 0px;">
                </div>
                <div class="modal-body">
                    <!-- Content -->
                    <table border="0" width="100%">
                        <tr>
                            <td valign="top" align="center">
                                <img data-dismiss="modal" width="200" height="200" src="/assets/images/error01.png"
                                    alt="">
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" align="center" style="color: black;">
                                <h1 id="modal_warning_message_blue"></h1>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Loading-->
    <div class="modal" id="modal_loading" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">กำลังประมวลผล</h4>
                </div>
                <div class="modal-body">
                    <!-- Content -->
                    <div class="text-center" style="margin-bottom: 20px;">กรุณารอสักครู่...</div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45"
                            aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" onClick="window.location.reload()">ปิดหน้าต่างนี้</button>
                </div>
            </div>
        </div>
    </div>

    <div id="dialog-confirm" title="Are you sure ?" style="display: none; font-size: 2em;">
        <span>คุณยืนยันจะดำเนินการต่อไปหรือไม่ ?</span>
    </div>

    <nav class="navbar navbar-default navbar-static-top">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <?php
				$detect = new \Mobile_Detect;
				if ($detect->isMobile()) {
					echo '<a class="navbar-brand" href="#">' . APP_NAME . '</a>';
				} else {
					echo '<a class="navbar-brand" href="/">' . APP_NAME . '</a>';
				}
				?>

            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                <!-- Menu -->
                <ul class="nav navbar-nav navbar-left">
                    <?php if (isset($_SESSION["user_login"])) : ?>
                    <?php if ($_SESSION["user_name"] == "admin") { ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                            aria-expanded="false"><i class="glyphicon glyphicon-wrench"></i> ตั้งค่าระบบ <span
                                class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown dropdown-submenu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="glyphicon glyphicon-cog"></i> Green Tire
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="/master/greentirecode">
                                            <i class="glyphicon glyphicon-circle-arrow-right"></i> Greentire Code
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/template/register">
                                            <i class="glyphicon glyphicon-circle-arrow-right"></i> Serial Register
                                        </a>
                                    </li>
                                    <li><a href="/master/building"><i
                                                class="glyphicon glyphicon-circle-arrow-right"></i> Building MC.</a>
                                    </li>
                                    <li><a href="/master/press"><i class="glyphicon glyphicon-circle-arrow-right"></i>
                                            Press</a></li>
                                    <li><a href="/master/mold"><i class="glyphicon glyphicon-circle-arrow-right"></i>
                                            Mold</a></li>
                                    <li><a href="/master/curetirecode"><i
                                                class="glyphicon glyphicon-circle-arrow-right"></i> Curetire Code</a>
                                    </li>
                                    <li><a href="/master/batchchangdate"><i
                                                class="glyphicon glyphicon-circle-arrow-right"></i> Set Start Batch</a>
                                    </li>
                                    <!-- <li><a href="/master/checkgreentire"><i class="glyphicon glyphicon-circle-arrow-right"></i> Set Check Greentire</a></li> -->
                                </ul>
                            </li>

                            <li class="dropdown dropdown-submenu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                        class="glyphicon glyphicon-cog"></i> Repair/Scrap</a>
                                <ul class="dropdown-menu">
                                    <li><a href="/master/defect"><i class="glyphicon glyphicon-circไle-arrow-right"></i>
                                            Defect</a></li>
                                </ul>
                            </li>
                            <li class="dropdown dropdown-submenu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                        class="glyphicon glyphicon-cog"></i> System Management</a>
                                <ul class="dropdown-menu">
                                    <li><a href="/master/warehouse"><i
                                                class="glyphicon glyphicon-circle-arrow-right"></i> Warehouse</a></li>
                                    <li><a href="/master/location"><i
                                                class="glyphicon glyphicon-circle-arrow-right"></i> Location</a></li>
                                    <li><a href="/master/disposal"><i
                                                class="glyphicon glyphicon-circle-arrow-right"></i> Disposal to Use
                                            In</a></li>
                                    <li><a href="/master/company"><i class="glyphicon glyphicon-circle-arrow-right"></i>
                                            Company</a></li>
                                    <li><a href="/master/gate"><i class="glyphicon glyphicon-circle-arrow-right"></i>
                                            Gate</a></li>
                                    <li><a href="/loading/deny"><i class="glyphicon glyphicon-circle-arrow-right"></i>
                                            Deny Loading</a></li>
                                </ul>
                            </li>
                            <li class="dropdown dropdown-submenu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                                        class="glyphicon glyphicon-cog"></i> User Management</a>
                                <ul class="dropdown-menu">
                                    <li><a href="/master/user"><i class="glyphicon glyphicon-circle-arrow-right"></i>
                                            User</a></li>
                                    <li><a href="/master/department"><i
                                                class="glyphicon glyphicon-circle-arrow-right"></i> Department</a></li>
                                    <li><a href="/master/menu"><i class="glyphicon glyphicon-circle-arrow-right"></i>
                                            Menu</a></li>
                                    <li><a href="/master/permission"><i
                                                class="glyphicon glyphicon-circle-arrow-right"></i> Permission</a></li>
                                    <li><a href="/master/authorize"><i
                                                class="glyphicon glyphicon-circle-arrow-right"></i> Authorize</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php endif ?>

                    <?php if (isset($_SESSION["user_login"])) : ?>
                    <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'menu_report') === true) : ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                            aria-expanded="false"><i class="glyphicon glyphicon-list"></i> รายงาน <span
                                class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'onhand_report') === true) : ?>
                            <li><a href="/report/onhand"><i class="glyphicon glyphicon-list-alt"></i> Onhand report</a>
                            </li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'greentire_hold_report') === true) : ?>
                            <li><a href="/report/greentire/hold"><i class="glyphicon glyphicon-list-alt"></i> Greentire
                                    Hold report</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'final_hold_report') === true) : ?>
                            <li><a href="/report/final/hold"><i class="glyphicon glyphicon-list-alt"></i> Final Hold
                                    report</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'daily_final_hold_report') === true) : ?>
                            <li><a href="/report/daily_final_hold"><i class="glyphicon glyphicon-list-alt"></i> Daily
                                    Final Hold report</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'building_report') === true) : ?>
                            <li><a href="/report/building"><i class="glyphicon glyphicon-list-alt"></i> Building
                                    report</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'building_reportt3') === true) : ?>
                            <li><a href="/report/buildingt3"><i class="glyphicon glyphicon-list-alt"></i> Builing New
                                    Report</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'wms_ship_detail_report') === true) : ?>
                            <li><a href="/report/shipdetail"><i class="glyphicon glyphicon-list-alt"></i> WMS Ship Detail Report</a>
                            </li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'internal_withdrawal_report') === true) : ?>
                            <li><a href="/report/internal"><i class="glyphicon glyphicon-list-alt"></i> Final
                                    Withdraw</a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'final_sendreport_to_warehouse_report') === true) : ?>
                            <li><a href="/report/final/sent"><i class="glyphicon glyphicon-list-alt"></i>
                                    รายงานรับยางเข้า Final</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'finish_good_withdraw_report') === true) : ?>
                            <li><a href="/report/finish_good/withdraw"><i class="glyphicon glyphicon-list-alt"></i>
                                    Finish Good Withdraw</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'curing_report') === true) : ?>
                            <li><a href="/report/curing"><i class="glyphicon glyphicon-list-alt"></i> Curing report</a>
                            </li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'greentire_inventory_report') === true) : ?>
                            <li><a href="/report/greentire/inventory"><i class="glyphicon glyphicon-list-alt"></i>
                                    Greentire Inventory</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'greentire_inspection_report') === true) : ?>
                            <li><a href="/report/greentire/inspection"><i class="glyphicon glyphicon-list-alt"></i>
                                    Greentire Inspection</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'final_send_to_warehouse_report') === true) : ?>
                            <li><a href="/report/warehouse/sent"><i class="glyphicon glyphicon-list-alt"></i>
                                    รายงานส่งยางเข้าคลังสินค้า</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'warehouse_receive_from_final_report') === true) : ?>
                            <li><a href="/report/warehouse/recive"><i class="glyphicon glyphicon-list-alt"></i> ใบรายงาน
                                    รับสินค้าเข้าคลังสินค้า</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'cured_report') === true) : ?>
                            <li><a href="/report/curingpress"><i class="glyphicon glyphicon-list-alt"></i>
                                    ใบรายงานจำนวนยางที่อบ</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'greentire_scrap_report') === true) : ?>
                            <li><a href="/report/greentire/scrap"><i class="glyphicon glyphicon-list-alt"></i> Greentire
                                    Scrap</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'curetire_scrap_report') === true) : ?>
                            <li><a href="/report/curetire/scrap"><i class="glyphicon glyphicon-list-alt"></i> Curetire
                                    Scrap</a></li>
                            <?php endif ?>
                            <!-- AX REPORT -->
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'building_ax_report') === true) : ?>
                            <li><a href="/report/building_ax"><i class="glyphicon glyphicon-list-alt"></i> Building
                                    AX</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'curing_ax_report') === true) : ?>
                            <li><a href="/report/curing_ax"><i class="glyphicon glyphicon-list-alt"></i> Curing AX</a>
                            </li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'curing_ax__finalsend') === true) : ?>
                            <li><a href="/report/curing_ax_send"><i class="glyphicon glyphicon-list-alt"></i> Curing AX
                                    (Final Send)</a></li>
                            <?php endif ?>
                            <!-- J Report -->
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'cure_inventory_report') === true) : ?>
                            <li><a href="/report/cure/inventory"><i class="glyphicon glyphicon-list-alt"></i> Cured
                                    Inventory</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'wip_final_fg_report') === true) : ?>
                            <li><a href="/report/wipfinalfg"><i class="glyphicon glyphicon-list-alt"></i> WIP Final
                                    FG.</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'cure_tire_code_master_report') === true) : ?>
                            <li><a href="/report/curetire/master"><i class="glyphicon glyphicon-list-alt"></i> Cure Tire
                                    Code Master</a></li>
                            <?php endif ?>

                            <!-- 20/4/2560 -->
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'greentire_hold_unhold_repair_report') === true) : ?>
                            <li><a href="/report/greentire/hold_unhold_repair"><i
                                        class="glyphicon glyphicon-list-alt"></i> Greentire Unhold/Unrepair</a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'final_hold_unhold_repair_report') === true) : ?>
                            <li><a href="/report/final/hold_unhold_repair"><i class="glyphicon glyphicon-list-alt"></i>
                                    Final Unhold/Unrepair</a></li>
                            <?php endif ?>

                            <!-- 2016.06.15 -->
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_bom') === true) : ?>
                            <li><a href="/report_bom"><i class="glyphicon glyphicon-list-alt"></i> Bom</a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_foil') === true) : ?>
                            <li><a href="/report_foil"><i class="glyphicon glyphicon-list-alt"></i> Foil</a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_building_machine') === true) : ?>
                            <li><a href="/report/building_machine"><i class="glyphicon glyphicon-list-alt"></i> Building
                                    Machine</a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'pcr_to_svo') === true) : ?>
                            <li><a href="/transfer/pcr_to_svo"><i class="glyphicon glyphicon-list-alt"></i> Transfer to
                                    SVO</a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_ratebuilding') === true) : ?>
                            <li><a href="/report/ratebuilding"><i class="glyphicon glyphicon-list-alt"></i> Rate Build
                                    Daily</a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_actbuilding') === true) : ?>
                            <li><a href="/report/actbuilding"><i class="glyphicon glyphicon-list-alt"></i> Actual Build
                                    Daily</a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_ratemonthly') === true) : ?>
                            <li><a href="/report/rate_monthly"><i class="glyphicon glyphicon-list-alt"></i> Rate Monthly
                                    Build</a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_deduct') === true) : ?>
                            <li><a href="/report/deduct"><i class="glyphicon glyphicon-list-alt"></i> Deduct Report</a>
                            </li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'greentire_cure_final_report') === true) : ?>
                            <li><a href="/report/gtcurefinal"><i class="glyphicon glyphicon-list-alt"></i> Greentire
                                    Curing Final Report</a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_logbuild') === true) : ?>
                            <li><a href="/report/logbuilding"><i class="glyphicon glyphicon-list-alt"></i>
                                    ประวัติการBuildรายเครื่อง/บุคคล </a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_ratecuring') === true) : ?>
                            <li><a href="/report/ratecuring"><i class="glyphicon glyphicon-list-alt"></i> Rate Cure
                                    Daily </a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_scrapchecking') === true) : ?>
                            <li><a href="/report/scrapchecking"><i class="glyphicon glyphicon-list-alt"></i> Scrap
                                    Checking Report </a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_loadtirecount') === true) : ?>
                            <li><a href="/report/loading/Containertire"><i class="glyphicon glyphicon-list-alt"></i>
                                    รายงานจำนวนการโหลดยางขึ้นตู้ </a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_warehouseonhand') === true) : ?>
                            <li><a href="/report/warehouse/Warehouseonhand"><i class="glyphicon glyphicon-list-alt"></i>
                                    Warehouse Inventory Onhand</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_greentirecode') === true) : ?>
                            <li><a href="/report/grentrecode"><i class="glyphicon glyphicon-list-alt"></i> Greentire
                                    Inventory By Code</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'report_buidingcode') === true) : ?>
                            <li><a href="/report/buildingbycode"><i class="glyphicon glyphicon-list-alt"></i> Building
                                    Report By Code</a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'building_ax_acc') === true) : ?>
                            <li><a href="/report/building_acc"><i class="glyphicon glyphicon-list-alt"></i> Building
                                    Report For Account </a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'greentire_scap_acc') === true) : ?>
                            <li><a href="/report/greentire/scrapacc"><i class="glyphicon glyphicon-list-alt"></i>
                                    Greentire Scrap For Account </a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'greentire_repairwarhouse_report') === true) : ?>
                            <li><a href="/report/greentire/repairgreentirereport"><i
                                        class="glyphicon glyphicon-list-alt"></i> Greentire Repair Report </a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'greentire_repair_report') === true) : ?>
                            <li><a href="/report/greentire/repairfinalreport"><i
                                        class="glyphicon glyphicon-list-alt"></i> Final Repair Report </a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'stock_counting_report') === true) : ?>
                            <li><a href="/warehouse_counting/report_counting"><i
                                        class="glyphicon glyphicon-list-alt"></i> Stock Counting Report </a></li>
                            <?php endif ?>

                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'stock_counting_onhand_diff_report') === true) : ?>
                            <li><a href="/warehouse_counting/report_onhand_diff"><i
                                        class="glyphicon glyphicon-list-alt"></i> Stock Counting Onhand Diff Report </a>
                            </li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'final_to_wh_report') === true) : ?>
                            <li><a href="/report/finaltowh/report"> <span class="glyphicon glyphicon-list-alt"
                                        aria-hidden="true"></span> Final Send To Warehouse</a></li>
                            <?php endif ?>
                            <!-- Report Quality -->
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'quality_checking') === true) : ?>
                            <li><a href="/report/quality/report"> <span class="glyphicon glyphicon-list-alt"
                                        aria-hidden="true"></span> Report Quality Checking</a></li>
                            <?php endif ?>
                            <!-- Report Final INS -->
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'final_finishing') === true) : ?>
                            <li><a href="/report/Finalfinishing/report"> <span class="glyphicon glyphicon-list-alt"
                                        aria-hidden="true"></span> Final Finishing - INS Report </a></li>
                            <?php endif ?>
                            <!-- Report Repair Inventory -->
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'repair_inventory') === true) : ?>
                            <li><a href="/report/repair/inventory"> <span class="glyphicon glyphicon-list-alt"
                                        aria-hidden="true"></span> Report Repair Inventory </a></li>
                            <?php endif ?>
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'daily_repair') === true) : ?>
                            <li><a href="/report/dailyrepair/report"> <span class="glyphicon glyphicon-list-alt"
                                        aria-hidden="true"></span>Daily Repair Report </a></li>
                            <?php endif ?>
                            <!-- Report Light Buff Inventory -->
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'lightbuff_inventory') === true) : ?>
                            <li><a href="/report/lightbuff/inventory"> <span class="glyphicon glyphicon-list-alt"
                                        aria-hidden="true"></span> Light Buff Inventory </a></li>
                            <?php endif ?>
                            <!-- Report Light Buff  -->
                            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'lightbuff_report') === true) : ?>
                            <li><a href="/report/lightbuff/report"> <span class="glyphicon glyphicon-list-alt"
                                        aria-hidden="true"></span> Mode Light Buff Report </a></li>
                            <?php endif ?>
                        </ul>
                    </li>
                    <?php endif ?>

                    <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'tracking_so') === true) : ?>
                    <li><a href="/tracking_v3"> <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                            Tracking</a></li>
                    <?php endif ?>
                    <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'svo_barcode_pcr_tracking') === true) : ?>
                    <li><a href="/svo_pcr_tracking"> <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                            WMS Barcode Tracking</a></li>
                    <?php endif ?>


                    <!-- DEV shortcut -->
                    <!-- <li><a href="/warehouse_counting"> <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Stock Counting</a></li>
						<li><a href="/warehouse_counting/setup"> <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Stock Counting Setup</a></li> -->
                    <!-- # DEV shortcut -->
                    <?php endif; ?>
                </ul>

                <?php if (isset($_SESSION["user_login"])) : ?>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="#">
                            <i class="glyphicon glyphicon-user"></i>
                            <?php echo $_SESSION["user_name"]; ?>
                        </a>
                    </li>
                    <li>
                        <?php
							$detect = new \Mobile_Detect;
							if (!$detect->isMobile()) { ?>
                        <a href="/change_password">
                            <span class="glyphicon glyphicon-refresh"></span>
                            เปลี่ยนรหัสผ่าน
                        </a>
                        <?php } ?>
                    </li>
                    <li>
                        <a id="link_logout" href="/user/logout">
                            <i class="glyphicon glyphicon-log-out"></i>
                            ออกจากระบบ
                        </a>
                    </li>
                </ul>
                <?php endif; ?>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>

    <?php if (isset($_SESSION["user_login"])) : ?>

    <div class="panel panel-default" style="border-radius: 0px; margin-bottom: 0px;">
        <div class="panel-body" style="padding:0px;">
            <ul class="nav nav-pills container-fluid" id="menu-loader" style="overflow-x: auto; white-space: nowrap;">
                <li><a href="#">ไม่พบเมนูที่สามารถใช้งานได้</a></li>
            </ul>
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

    <div
        style="padding: 10px; background: #eeeeee; color: #000000; border-bottom: 1px #cccccc solid; margin-bottom: 10px; font-weight: bold; display: none;">
        <?php echo $this->e($title); ?>
    </div>
    <div class="container-fluid">
        <?php echo $this->section("content"); ?>
    </div>

    <?php echo $this->section('scripts') ?>

    <script>
    jQuery(document).ready(function($) {

        $("#link_logout").on("click", function() {

            if ('<?php echo $_SESSION["user_permission"]; ?>' === '1' ||
                '<?php echo $_SESSION['user_permission']; ?>' === '18') {
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
    </script>

</body>

</html>