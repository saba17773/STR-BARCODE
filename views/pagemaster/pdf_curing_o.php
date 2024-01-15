<?php ob_start();
error_reporting(0);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Curing ReportA</title>
    <!-- <link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" /> -->

    <style type="text/css">
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 8px;
            font-family: "Angsana New";
        }

        td,
        tr,
        th {
            border: 1px solid #000000;
            text-align: center;
            padding: 5px;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
            font-size: 8px;
        }

        .td,
        .tr,
        .th {
            border: 0px solid #000000;
            text-align: left;
            padding: 4px;
        }

        .double_td {
            border: 2px solid black;
        }

        .f12 {
            font-size: 14px;
            font-family: "Angsana New";
        }

        .f10 {
            font-size: 10px;
            font-family: "Angsana New";
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (isset($press1)) { ?>
            <table>
                <tr>
                    <td colspan="5">
                        <a class="navbar-brand"><img src="./assets/images/STR.jpg" style="padding-left:10px;height:55px; width:auto;" /></a>
                    </td>
                    <td colspan="17" class="f12">
                        <b><i>SIAMTRUCK RADIAL CO.LTD.</i></b> <br>
                        <b><i>CURING REPORT LINE <?php echo $pressNo; ?></i></b>
                    </td>
                </tr>
                <tr>
                    <td rowspan="2" text-rotate="90" class="f10"><b>รายชื่อ</b></td>
                    <td colspan="21" class="f10"><br>
                        <b>DATE : <?php echo $datecuring; ?></b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>SHIFT : <?php if ($shift == "day") {
                                        echo "กลางวัน";
                                    } else {
                                        echo "กลางคืน";
                                    } ?></b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>GROUP :
                            <?php $ids = array();
                            foreach ($group_decode as $value) {
                                $ids[] = $value->Description;
                            }
                            echo implode(",", $ids);
                            ?>
                        </b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>REPORTED BY : ............................................</b>
                    </td>
                </tr>
                <tr>
                    <td><br><b>Press</b></td>
                    <td width="3%"><br><b>Side</b></td>
                    <td width="6%"><br><b>Cure code</b></td>
                    <td><br><b>Top Turn</b></td>
                    <?php if ($shift == "day") { ?>
                        <td><br><b>8.00-11.00</b></td>
                        <td><br><b>11.00-14.00</b></td>
                        <td><br><b>14.00-17.00</b></td>
                        <td><br><b>17.00-20.00</b></td>
                    <?php } else { ?>
                        <td><br><b>20.00-23.00</b></td>
                        <td><br><b>23.00-02.00</b></td>
                        <td><br><b>02.00-05.00</b></td>
                        <td><br><b>05.00-08.00</b></td>
                    <?php } ?>
                    <td width="6%"><br><b>Total</b></td>
                    <td width="4%"><br><b>%</b></td>
                    <td width="5%"><br><b>Press</b></td>
                    <td width="5%"><br><b>TimeOn</b></td>
                    <td width="5%"><br><b>TimeOff</b></td>
                    <td width="5%"><br><b>TotalTime</b></td>
                    <td width="20%" colspan="5"><br><b>Causes of down time</b></td>
                </tr>

                <tr text-rotate="90">
                    <td rowspan="9" text-rotate="90" class="f10">
                        <?php
                        // $nx = "";
                        // foreach ($dataname1 as $value) {
                        // $nx .= $value['Name'].",";
                        // }
                        // echo trim($nx,",");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b01; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b01 && $value['PressSide'] === 'L') {
                                $code01L .= $value['CuringCode'] . ",";
                                $top01L .= $value['rate12'] . ",";
                                $q1_text01L .= $value['Q1'] . ",";
                                $q2_text01L .= $value['Q2'] . ",";
                                $q3_text01L .= $value['Q3'] . ",";
                                $q4_text01L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code01L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top01L = trim($top01L, ",");
                        $top01L = explode(",", $top01L);
                        echo $top01L = $top01L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text01L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text01L, ","); ?></td>
                    <td><?php echo trim($q3_text01L, ","); ?></td>
                    <td><?php echo trim($q4_text01L, ","); ?></td>
                    <td><?php $rows1 = array($qty11, $qty21, $qty31, $qty41);
                        if (array_sum($rows1) != 0) {
                            echo $rows1_new = array_sum($rows1);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows1_new)) {

                            $newrate = ($rows1_new / $top01L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b01 && $value['PressSide'] === 'R') {
                                $code01R .= $value['CuringCode'] . ",";
                                $top01R .= $value['rate12'] . ",";
                                $q1_text01R .= $value['Q1'] . ",";
                                $q2_text01R .= $value['Q2'] . ",";
                                $q3_text01R .= $value['Q3'] . ",";
                                $q4_text01R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code01R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top01R = trim($top01R, ",");
                        $top01R = explode(",", $top01R);
                        echo $top01R = $top01R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text01R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text01R, ","); ?></td>
                    <td><?php echo trim($q3_text01R, ","); ?></td>
                    <td><?php echo trim($q4_text01R, ","); ?></td>
                    <td><?php $rows2 = array($qty12, $qty22, $qty32, $qty42);
                        if (array_sum($rows2) != 0) {
                            echo $rows2_new = array_sum($rows2);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows2_new)) {

                            $newrate = ($rows2_new / $top01R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b02; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b02 && $value['PressSide'] === 'L') {
                                $code02L .= $value['CuringCode'] . ",";
                                $top02L .= $value['rate12'] . ",";
                                $q1_text02L .= $value['Q1'] . ",";
                                $q2_text02L .= $value['Q2'] . ",";
                                $q3_text02L .= $value['Q3'] . ",";
                                $q4_text02L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code02L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top02L = trim($top02L, ",");
                        $top02L = explode(",", $top02L);
                        echo $top02L = $top02L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text02L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text02L, ","); ?></td>
                    <td><?php echo trim($q3_text02L, ","); ?></td>
                    <td><?php echo trim($q4_text02L, ","); ?></td>
                    <td><?php $rows3 = array($qty13, $qty23, $qty33, $qty43);
                        if (array_sum($rows3) != 0) {
                            echo $rows3_new = array_sum($rows3);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows3_new)) {

                            $newrate = ($rows3_new / $top02L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b02 && $value['PressSide'] === 'R') {
                                $code02R .= $value['CuringCode'] . ",";
                                $top02R .= $value['rate12'] . ",";
                                $q1_text02R .= $value['Q1'] . ",";
                                $q2_text02R .= $value['Q2'] . ",";
                                $q3_text02R .= $value['Q3'] . ",";
                                $q4_text02R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code02R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top02R = trim($top02R, ",");
                        $top02R = explode(",", $top02R);
                        echo $top02R = $top02R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text02R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text02R, ","); ?></td>
                    <td><?php echo trim($q3_text02R, ","); ?></td>
                    <td><?php echo trim($q4_text02R, ","); ?></td>
                    <td><?php $rows4 = array($qty14, $qty24, $qty34, $qty44);
                        if (array_sum($rows4) != 0) {
                            echo $rows4_new = array_sum($rows4);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows4_new)) {

                            $newrate = ($rows4_new / $top02R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b03; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b03 && $value['PressSide'] === 'L') {
                                $code03L .= $value['CuringCode'] . ",";
                                $top03L .= $value['rate12'] . ",";
                                $q1_text03L .= $value['Q1'] . ",";
                                $q2_text03L .= $value['Q2'] . ",";
                                $q3_text03L .= $value['Q3'] . ",";
                                $q4_text03L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code03L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top03L = trim($top03L, ",");
                        $top03L = explode(",", $top03L);
                        echo $top03L = $top03L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text03L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text03L, ","); ?></td>
                    <td><?php echo trim($q3_text03L, ","); ?></td>
                    <td><?php echo trim($q4_text03L, ","); ?></td>
                    <td><?php $rows5 = array($qty15, $qty25, $qty35, $qty45);
                        if (array_sum($rows5) != 0) {
                            echo $rows5_new = array_sum($rows5);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows5_new)) {

                            $newrate = ($rows5_new / $top03L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b03 && $value['PressSide'] === 'R') {
                                $code03R .= $value['CuringCode'] . ",";
                                $top03R .= $value['rate12'] . ",";
                                $q1_text03R .= $value['Q1'] . ",";
                                $q2_text03R .= $value['Q2'] . ",";
                                $q3_text03R .= $value['Q3'] . ",";
                                $q4_text03R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code03R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top03R = trim($top03R, ",");
                        $top03R = explode(",", $top03R);
                        echo $top03R = $top03R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text03R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text03R, ","); ?></td>
                    <td><?php echo trim($q3_text03R, ","); ?></td>
                    <td><?php echo trim($q4_text03R, ","); ?></td>
                    <td><?php $rows6 = array($qty16, $qty26, $qty36, $qty46);
                        if (array_sum($rows6) != 0) {
                            echo $rows6_new = array_sum($rows6);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows6_new)) {

                            $newrate = ($rows6_new / $top03R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b04; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b04 && $value['PressSide'] === 'L') {
                                $code04L .= $value['CuringCode'] . ",";
                                $top04L .= $value['rate12'] . ",";
                                $q1_text04L .= $value['Q1'] . ",";
                                $q2_text04L .= $value['Q2'] . ",";
                                $q3_text04L .= $value['Q3'] . ",";
                                $q4_text04L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code04L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top04L = trim($top04L, ",");
                        $top04L = explode(",", $top04L);
                        echo $top04L = $top04L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text04L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text04L, ","); ?></td>
                    <td><?php echo trim($q3_text04L, ","); ?></td>
                    <td><?php echo trim($q4_text04L, ","); ?></td>
                    <td><?php $rows7 = array($qty17, $qty27, $qty37, $qty47);
                        if (array_sum($rows7) != 0) {
                            echo $rows7_new = array_sum($rows7);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows7_new)) {

                            $newrate = ($rows7_new / $top04L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b04 && $value['PressSide'] === 'R') {
                                $code04R .= $value['CuringCode'] . ",";
                                $top04R .= $value['rate12'] . ",";
                                $q1_text04R .= $value['Q1'] . ",";
                                $q2_text04R .= $value['Q2'] . ",";
                                $q3_text04R .= $value['Q3'] . ",";
                                $q4_text04R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code04R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top04R = trim($top04R, ",");
                        $top04R = explode(",", $top04R);
                        echo $top04R = $top04R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text04R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text04R, ","); ?></td>
                    <td><?php echo trim($q3_text04R, ","); ?></td>
                    <td><?php echo trim($q4_text04R, ","); ?></td>
                    <td><?php $rows8 = array($qty18, $qty28, $qty38, $qty48);
                        if (array_sum($rows8) != 0) {
                            echo $rows8_new = array_sum($rows8);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows8_new)) {

                            $newrate = ($rows8_new / $top04R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr text-rotate="90">
                    <td rowspan="9" text-rotate="90" class="f10">
                        <?php
                        // $nx = "";
                        // foreach ($dataname2 as $value) {
                        // $nx .= $value['Name'].",";
                        // }
                        // echo trim($nx,",");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b05; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b05 && $value['PressSide'] === 'L') {
                                $code05L .= $value['CuringCode'] . ",";
                                $top05L .= $value['rate12'] . ",";
                                $q1_text05L .= $value['Q1'] . ",";
                                $q2_text05L .= $value['Q2'] . ",";
                                $q3_text05L .= $value['Q3'] . ",";
                                $q4_text05L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code05L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top05L = trim($top05L, ",");
                        $top05L = explode(",", $top05L);
                        echo $top05L = $top05L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text05L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text05L, ","); ?></td>
                    <td><?php echo trim($q3_text05L, ","); ?></td>
                    <td><?php echo trim($q4_text05L, ","); ?></td>
                    <td><?php $rows9 = array($qty19, $qty29, $qty39, $qty49);
                        if (array_sum($rows9) != 0) {
                            echo $rows9_new = array_sum($rows9);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows9_new)) {

                            $newrate = ($rows9_new / $top05L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b05 && $value['PressSide'] === 'R') {
                                $code05R .= $value['CuringCode'] . ",";
                                $top05R .= $value['rate12'] . ",";
                                $q1_text05R .= $value['Q1'] . ",";
                                $q2_text05R .= $value['Q2'] . ",";
                                $q3_text05R .= $value['Q3'] . ",";
                                $q4_text05R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code05R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top05R = trim($top05R, ",");
                        $top05R = explode(",", $top05R);
                        echo $top05R = $top05R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text05R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text05R, ","); ?></td>
                    <td><?php echo trim($q3_text05R, ","); ?></td>
                    <td><?php echo trim($q4_text05R, ","); ?></td>
                    <td><?php $rows10 = array($qty110, $qty210, $qty310, $qty410);
                        if (array_sum($rows10) != 0) {
                            echo $rows10_new = array_sum($rows10);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows10_new)) {

                            $newrate = ($rows10_new / $top05R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b06; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b06 && $value['PressSide'] === 'L') {
                                $code06L .= $value['CuringCode'] . ",";
                                $top06L .= $value['rate12'] . ",";
                                $q1_text06L .= $value['Q1'] . ",";
                                $q2_text06L .= $value['Q2'] . ",";
                                $q3_text06L .= $value['Q3'] . ",";
                                $q4_text06L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code06L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top06L = trim($top06L, ",");
                        $top06L = explode(",", $top06L);
                        echo $top06L = $top06L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text06L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text06L, ","); ?></td>
                    <td><?php echo trim($q3_text06L, ","); ?></td>
                    <td><?php echo trim($q4_text06L, ","); ?></td>
                    <td><?php $rows11 = array($qty111, $qty211, $qty311, $qty411);
                        if (array_sum($rows11) != 0) {
                            echo $rows11_new = array_sum($rows11);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows11_new)) {

                            $newrate = ($rows11_new / $top06L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b06 && $value['PressSide'] === 'R') {
                                $code06R .= $value['CuringCode'] . ",";
                                $top06R .= $value['rate12'] . ",";
                                $q1_text06R .= $value['Q1'] . ",";
                                $q2_text06R .= $value['Q2'] . ",";
                                $q3_text06R .= $value['Q3'] . ",";
                                $q4_text06R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code06R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top06R = trim($top06R, ",");
                        $top06R = explode(",", $top06R);
                        echo $top06R = $top06R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text06R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text06R, ","); ?></td>
                    <td><?php echo trim($q3_text06R, ","); ?></td>
                    <td><?php echo trim($q4_text06R, ","); ?></td>
                    <td><?php $rows12 = array($qty112, $qty212, $qty312, $qty412);
                        if (array_sum($rows12) != 0) {
                            echo $rows12_new = array_sum($rows12);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows12_new)) {

                            $newrate = ($rows12_new / $top06R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b07; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b07 && $value['PressSide'] === 'L') {
                                $code07L .= $value['CuringCode'] . ",";
                                $top07L .= $value['rate12'] . ",";
                                $q1_text07L .= $value['Q1'] . ",";
                                $q2_text07L .= $value['Q2'] . ",";
                                $q3_text07L .= $value['Q3'] . ",";
                                $q4_text07L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code07L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top07L = trim($top07L, ",");
                        $top07L = explode(",", $top07L);
                        echo $top07L = $top07L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text07L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text07L, ","); ?></td>
                    <td><?php echo trim($q3_text07L, ","); ?></td>
                    <td><?php echo trim($q4_text07L, ","); ?></td>
                    <td><?php $rows13 = array($qty113, $qty213, $qty313, $qty413);
                        if (array_sum($rows13) != 0) {
                            echo $rows13_new = array_sum($rows13);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows13_new)) {

                            $newrate = ($rows13_new / $top07L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b07 && $value['PressSide'] === 'R') {
                                $code07R .= $value['CuringCode'] . ",";
                                $top07R .= $value['rate12'] . ",";
                                $q1_text07R .= $value['Q1'] . ",";
                                $q2_text07R .= $value['Q2'] . ",";
                                $q3_text07R .= $value['Q3'] . ",";
                                $q4_text07R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code07R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top07R = trim($top07R, ",");
                        $top07R = explode(",", $top07R);
                        echo $top07R = $top07R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text07R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text07R, ","); ?></td>
                    <td><?php echo trim($q3_text07R, ","); ?></td>
                    <td><?php echo trim($q4_text07R, ","); ?></td>
                    <td><?php $rows14 = array($qty114, $qty214, $qty314, $qty414);
                        if (array_sum($rows14) != 0) {
                            echo $rows14_new = array_sum($rows14);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows14_new)) {

                            $newrate = ($rows14_new / $top07R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b08; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b08 && $value['PressSide'] === 'L') {
                                $code08L .= $value['CuringCode'] . ",";
                                $top08L .= $value['rate12'] . ",";
                                $q1_text08L .= $value['Q1'] . ",";
                                $q2_text08L .= $value['Q2'] . ",";
                                $q3_text08L .= $value['Q3'] . ",";
                                $q4_text08L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code08L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top08L = trim($top08L, ",");
                        $top08L = explode(",", $top08L);
                        echo $top08L = $top08L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text08L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text08L, ","); ?></td>
                    <td><?php echo trim($q3_text08L, ","); ?></td>
                    <td><?php echo trim($q4_text08L, ","); ?></td>
                    <td><?php $rows15 = array($qty115, $qty215, $qty315, $qty415);
                        if (array_sum($rows15) != 0) {
                            echo $rows15_new = array_sum($rows15);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows15_new)) {

                            $newrate = ($rows15_new / $top08L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b08 && $value['PressSide'] === 'R') {
                                $code08R .= $value['CuringCode'] . ",";
                                $top08R .= $value['rate12'] . ",";
                                $q1_text08R .= $value['Q1'] . ",";
                                $q2_text08R .= $value['Q2'] . ",";
                                $q3_text08R .= $value['Q3'] . ",";
                                $q4_text08R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code08R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top08R = trim($top08R, ",");
                        $top08R = explode(",", $top08R);
                        echo $top08R = $top08R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text08R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text08R, ","); ?></td>
                    <td><?php echo trim($q3_text08R, ","); ?></td>
                    <td><?php echo trim($q4_text08R, ","); ?></td>
                    <<td><?php $rows16 = array($qty116, $qty216, $qty316, $qty416);
                            if (array_sum($rows16) != 0) {
                                echo $rows16_new = array_sum($rows16);
                            } ?>
                        </td>
                        <td>
                            <?php if (isset($rows16_new)) {

                                $newrate = ($rows16_new / $top08R) * 100;
                                if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                            } ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="5"></td>
                </tr>
                <tr text-rotate="90">
                    <td rowspan="9" text-rotate="90" class="f10">
                        <?php
                        // $nx = "";
                        // foreach ($dataname3 as $value) {
                        // $nx .= $value['Name'].",";
                        // }
                        // echo trim($nx,",");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b09; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b09 && $value['PressSide'] === 'L') {
                                $code09L .= $value['CuringCode'] . ",";
                                $top09L .= $value['rate12'] . ",";
                                $q1_text09L .= $value['Q1'] . ",";
                                $q2_text09L .= $value['Q2'] . ",";
                                $q3_text09L .= $value['Q3'] . ",";
                                $q4_text09L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code09L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top09L = trim($top09L, ",");
                        $top09L = explode(",", $top09L);
                        echo $top09L = $top09L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text09L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text09L, ","); ?></td>
                    <td><?php echo trim($q3_text09L, ","); ?></td>
                    <td><?php echo trim($q4_text09L, ","); ?></td>
                    <td><?php $rows17 = array($qty117, $qty217, $qty317, $qty417);
                        if (array_sum($rows17) != 0) {
                            echo $rows17_new = array_sum($rows17);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows17_new)) {

                            $newrate = ($rows17_new / $top09L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b09 && $value['PressSide'] === 'R') {
                                $code09R .= $value['CuringCode'] . ",";
                                $top09R .= $value['rate12'] . ",";
                                $q1_text09R .= $value['Q1'] . ",";
                                $q2_text09R .= $value['Q2'] . ",";
                                $q3_text09R .= $value['Q3'] . ",";
                                $q4_text09R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code09R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top09R = trim($top09R, ",");
                        $top09R = explode(",", $top09R);
                        echo $top09R = $top09R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text09R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text09R, ","); ?></td>
                    <td><?php echo trim($q3_text09R, ","); ?></td>
                    <td><?php echo trim($q4_text09R, ","); ?></td>
                    <td><?php $rows18 = array($qty118, $qty218, $qty318, $qty418);
                        if (array_sum($rows18) != 0) {
                            echo $rows18_new = array_sum($rows18);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows18_new)) {

                            $newrate = ($rows18_new / $top09R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b10; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        $temp_curing_code = '';
                        $temp_q1 = '';
                        $temp_q2 = '';
                        $temp_q3 = '';
                        $temp_q4 = '';

                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b10 && $value['PressSide'] === 'L') {

                                if ($temp_curing_code !== $value['CuringCode']) {
                                    $code10L .= $value['CuringCode'] . ",";
                                    $temp_curing_code = $value['CuringCode'];
                                }

                                $top10L .= $value['rate12'] . ",";

                                if ($temp_q1 !== $value['Q1']) {
                                    $q1_text10L .= $value['Q1'] . ",";
                                    $temp_q1 = $value['Q1'];
                                }

                                if ($temp_q2 !== $value['Q2']) {
                                    $q2_text10L .= $value['Q2'] . ",";
                                    $temp_q2 = $value['Q2'];
                                }

                                if ($temp_q3 !== $value['Q3']) {
                                    $q3_text10L .= $value['Q3'] . ",";
                                    $temp_q3 = $value['Q3'];
                                }

                                if ($temp_q4 !== $value['Q4']) {
                                    $q4_text10L .= $value['Q4'] . ",";
                                    $temp_q4 = $value['Q4'];
                                }
                            }
                        }
                        echo trim($code10L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top10L = trim($top10L, ",");
                        $top10L = explode(",", $top10L);
                        echo $top10L = $top10L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text10L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text10L, ","); ?></td>
                    <td><?php echo trim($q3_text10L, ","); ?></td>
                    <td><?php echo trim($q4_text10L, ","); ?></td>
                    <td><?php $rows19 = array($qty119, $qty219, $qty319, $qty419);
                        if (array_sum($rows19) != 0) {
                            echo $rows19_new = array_sum($rows19);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows19_new)) {

                            $newrate = ($rows19_new / $top10L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php

                        $temp_curing_code = '';
                        $temp_q1 = '';
                        $temp_q2 = '';
                        $temp_q3 = '';
                        $temp_q4 = '';
                        $code10R = '';
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b10 && $value['PressSide'] === 'R') {

                                if ($temp_curing_code !== $value['CuringCode']) {
                                    $code10R .= $value['CuringCode'] . ",";
                                    $temp_curing_code = $value['CuringCode'];
                                }

                                $top10R .= $value['rate12'] . ",";

                                if ($temp_q1 !== $value['Q1']) {
                                    $q1_text10R .= $value['Q1'] . ",";
                                    $temp_q1 = $value['Q1'];
                                }

                                if ($temp_q2 !== $value['Q2']) {
                                    $q2_text10R .= $value['Q2'] . ",";
                                    $temp_q2 = $value['Q2'];
                                }

                                if ($temp_q3 !== $value['Q3']) {
                                    $q3_text10R .= $value['Q3'] . ",";
                                    $temp_q3 = $value['Q3'];
                                }

                                if ($temp_q4 !== $value['Q4']) {
                                    $q4_text10R .= $value['Q4'] . ",";
                                    $temp_q4 = $value['Q4'];
                                }
                            }
                        }
                        echo trim($code10R, ",");
                        // echo $value['CuringCode'] . ' / ' . $value['CuringCode'];
                        ?>
                    </td>
                    <td>
                        <?php
                        $top10R = trim($top10R, ",");
                        $top10R = explode(",", $top10R);
                        echo $top10R = $top10R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text10R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text10R, ","); ?></td>
                    <td><?php echo trim($q3_text10R, ","); ?></td>
                    <td><?php echo trim($q4_text10R, ","); ?></td>
                    <td><?php $rows20 = array($qty1110, $qty2110, $qty3110, $qty4110);
                        if (array_sum($rows20) != 0) {
                            echo $rows20_new = array_sum($rows20);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows20_new)) {

                            $newrate = ($rows20_new / $top10R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td colspan="5"><b>
                            <center>จำนวนพนักงาน</center>
                        </b></td>
                    <td colspan="4"><b>
                            <center>สรุปการผลิต</center>
                        </b></td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b11; ?></td>
                    <td>L</td>
                    <td>
                        <?php

                        $temp_curing_code = '';
                        $temp_q1 = '';
                        $temp_q2 = '';
                        $temp_q3 = '';
                        $temp_q4 = '';

                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b11 && $value['PressSide'] === 'L') {

                                if ($temp_curing_code !== $value['CuringCode']) {
                                    $code11L .= $value['CuringCode'] . ",";
                                    $temp_curing_code = $value['CuringCode'];
                                }

                                $top11L .= $value['rate12'] . ",";

                                if ($temp_q1 !== $value['Q1']) {
                                    $q1_text11L .= $value['Q1'] . ",";
                                    $temp_q1 = $value['Q1'];
                                }

                                if ($temp_q2 !== $value['Q2']) {
                                    $q2_text11L .= $value['Q2'] . ",";
                                    $temp_q2 = $value['Q2'];
                                }

                                if ($temp_q3 !== $value['Q3']) {
                                    $q3_text11L .= $value['Q3'] . ",";
                                    $temp_q3 = $value['Q3'];
                                }

                                if ($temp_q4 !== $value['Q4']) {
                                    $q4_text11L .= $value['Q4'] . ",";
                                    $temp_q4 = $value['Q4'];
                                }
                            }
                        }
                        echo trim($code11L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top11L = trim($top11L, ",");
                        $top11L = explode(",", $top11L);
                        echo $top11L = $top11L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text11L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text11L, ","); ?></td>
                    <td><?php echo trim($q3_text11L, ","); ?></td>
                    <td><?php echo trim($q4_text11L, ","); ?></td>
                    <td><?php $rows21 = array($qty1111, $qty2111, $qty3111, $qty4111);
                        if (array_sum($rows21) != 0) {
                            echo $rows21_new = array_sum($rows21);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows21_new)) {

                            $newrate = ($rows21_new / $top11L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <th rowspan="2" width="7%">แผนก</th>
                    <th rowspan="2" width="5%">เต็ม</th>
                    <th rowspan="2" width="5%">มา</th>
                    <th rowspan="2" width="5%">ลา</th>
                    <th rowspan="2" width="5%">ขาด</th>
                    <th rowspan="2" width="5%">รายการ</th>
                    <th rowspan="2" width="6%">จำนวน<br>(เส้น)</th>
                    <th rowspan="2" width="5%" width="5%">รายการ</th>
                    <th rowspan="2" width="6%" width="6%">จำนวน<br>(เส้น)</th>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        $temp_curing_code = '';
                        $temp_q1 = '';
                        $temp_q2 = '';
                        $temp_q3 = '';
                        $temp_q4 = '';

                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b11 && $value['PressSide'] === 'R') {

                                if ($temp_curing_code !== $value['CuringCode']) {
                                    $code11R .= $value['CuringCode'] . ",";
                                    $temp_curing_code = $value['CuringCode'];
                                }

                                $top11R .= $value['rate12'] . ",";

                                if ($temp_q1 !== $value['Q1']) {
                                    $q1_text11R .= $value['Q1'] . ",";
                                    $temp_q1 = $value['Q1'];
                                }

                                if ($temp_q2 !== $value['Q2']) {
                                    $q2_text11R .= $value['Q2'] . ",";
                                    $temp_q2 = $value['Q2'];
                                }

                                if ($temp_q3 !== $value['Q3']) {
                                    $q3_text11R .= $value['Q3'] . ",";
                                    $temp_q3 = $value['Q3'];
                                }

                                if ($temp_q4 !== $value['Q4']) {
                                    $q4_text11R .= $value['Q4'] . ",";
                                    $temp_q4 = $value['Q4'];
                                }
                            }
                        }
                        echo trim($code11R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top11R = trim($top11R, ",");
                        $top11R = explode(",", $top11R);
                        echo $top11R = $top11R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text11R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text11R, ","); ?></td>
                    <td><?php echo trim($q3_text11R, ","); ?></td>
                    <td><?php echo trim($q4_text11R, ","); ?></td>
                    <td><?php $rows22 = array($qty1112, $qty2112, $qty3112, $qty4112);
                        if (array_sum($rows22) != 0) {
                            echo $rows22_new = array_sum($rows22);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows22_new)) {

                            $newrate = ($rows22_new / $top11R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b12; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b12 && $value['PressSide'] === 'L') {
                                $code12L .= $value['CuringCode'] . ",";
                                $top12L .= $value['rate12'] . ",";
                                $q1_text12L .= $value['Q1'] . ",";
                                $q2_text12L .= $value['Q2'] . ",";
                                $q3_text12L .= $value['Q3'] . ",";
                                $q4_text12L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code12L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top12L = trim($top12L, ",");
                        $top12L = explode(",", $top12L);
                        echo $top12L = $top12L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text12L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text12L, ","); ?></td>
                    <td><?php echo trim($q3_text12L, ","); ?></td>
                    <td><?php echo trim($q4_text12L, ","); ?></td>
                    <td><?php $rows23 = array($qty1113, $qty2113, $qty3113, $qty4113);
                        if (array_sum($rows23) != 0) {
                            echo $rows23_new = array_sum($rows23);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows23_new)) {

                            $newrate = ($rows23_new / $top12L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b12 && $value['PressSide'] === 'R') {
                                $code12R .= $value['CuringCode'] . ",";
                                $top12R .= $value['rate12'] . ",";
                                $q1_text12R .= $value['Q1'] . ",";
                                $q2_text12R .= $value['Q2'] . ",";
                                $q3_text12R .= $value['Q3'] . ",";
                                $q4_text12R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code12R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top12R = trim($top12R, ",");
                        $top12R = explode(",", $top12R);
                        echo $top12R = $top12R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text12R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text12R, ","); ?></td>
                    <td><?php echo trim($q3_text12R, ","); ?></td>
                    <td><?php echo trim($q4_text12R, ","); ?></td>
                    <td><?php $rows24 = array($qty1114, $qty2114, $qty3114, $qty4114);
                        if (array_sum($rows24) != 0) {
                            echo $rows24_new = array_sum($rows24);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows24_new)) {

                            $newrate = ($rows24_new / $top12R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th rowspan="3" colspan="3" class="f10">TOTAL</th>
                    <td rowspan="3"></td>
                    <td rowspan="3">
                        <?php $sumtop = array($top01L, $top01R, $top02L, $top02R, $top03L, $top03R, $top04L, $top04R, $top05L, $top05R, $top06L, $top06R, $top07L, $top07R, $top08L, $top08R, $top09L, $top09R, $top10L, $top10R, $top11L, $top11R, $top12L, $top12R);
                        if (array_sum($sumtop) != 0) {
                            echo $sumtop = array_sum($sumtop);
                        }
                        ?>
                    </td>
                    <td rowspan="3">
                        <?php $sumq1 = array($qty11, $qty12, $qty13, $qty14, $qty15, $qty16, $qty17, $qty18, $qty19, $qty110, $qty111, $qty112, $qty113, $qty114, $qty115, $qty116, $qty117, $qty118, $qty119, $qty1110, $qty1111, $qty1112, $qty1113, $qty1114);
                        if (array_sum($sumq1) != 0) {
                            echo $sumq1 = array_sum($sumq1);
                        }
                        ?>
                    </td>
                    <td rowspan="3">
                        <?php $sumq2 = array($qty21, $qty22, $qty23, $qty24, $qty25, $qty26, $qty27, $qty28, $qty29, $qty210, $qty211, $qty212, $qty213, $qty214, $qty215, $qty216, $qty217, $qty218, $qty219, $qty2110, $qty2111, $qty2112, $qty2113, $qty2114);
                        if (array_sum($sumq2) != 0) {
                            echo $sumq2 = array_sum($sumq2);
                        }
                        ?>
                    </td>
                    <td rowspan="3">
                        <?php $sumq3 = array($qty31, $qty32, $qty33, $qty34, $qty35, $qty36, $qty37, $qty38, $qty39, $qty310, $qty311, $qty312, $qty313, $qty314, $qty315, $qty316, $qty317, $qty318, $qty319, $qty3110, $qty3111, $qty3112, $qty3113, $qty3114);
                        if (array_sum($sumq3) != 0) {
                            echo $sumq3 = array_sum($sumq3);
                        }
                        ?>
                    </td>
                    <td rowspan="3">
                        <?php $sumq4 = array($qty41, $qty42, $qty43, $qty44, $qty45, $qty46, $qty47, $qty48, $qty49, $qty410, $qty411, $qty412, $qty413, $qty414, $qty415, $qty416, $qty417, $qty418, $qty419, $qty4110, $qty4111, $qty4112, $qty4113, $qty4114);
                        if (array_sum($sumq4) != 0) {
                            echo $sumq4 = array_sum($sumq4);
                        }
                        ?>
                    </td>
                    <td rowspan="3">
                        <?php
                        // foreach ($datajson as $value) {
                        //     if (
                        //         $value->PressNo !== "P13" && $value->PressNo !== "P14" && $value->PressNo !== "P15" && $value->PressNo !== "P16"
                        //         && $value->PressNo !== "P17" && $value->PressNo !== "P18" && $value->PressNo !== "P19" && $value->PressNo !== "P20"
                        //     ) {
                        //         $sum = 0;
                        //         $rows = array($value->Q1, $value->Q2, $value->Q3, $value->Q4);
                        //         $QQ = array_sum($rows);
                        //         $sumrows += $QQ;
                        //     }
                        // }
                        // if ($sumrows != 0) {
                        //     echo $sumrows;
                        // }
                        $sumq_all = array($sumq1, $sumq2, $sumq3, $sumq4);
                        if (array_sum($sumq_all) != 0) {
                            $sumq_all = array_sum($sumq_all);
                            echo $sumq_all;
                        }
                        ?>
                    </td>
                    <td rowspan="3">
                        <?php $sumq_all = array($sumq1, $sumq2, $sumq3, $sumq4);
                        if (array_sum($sumq_all) != 0) {
                            $sumq_all = array_sum($sumq_all);
                            if (array_sum($sumtop) == '0' || $sumtop == 0) {
                                echo "";
                            } else {
                                $sumper = ($sumq_all / $sumtop) * 100;
                                echo $sumper_format_number = number_format($sumper, 2, '.', '');
                            }
                        }
                        ?>
                    </td>
                    <td><br></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>TOTAL</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>TOTAL</td>
                    <td></td>
                    <td>TOTAL</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="10"><b>รายงานอุบัติเหตุ</b></td>
                </tr>
                <tr>
                    <th rowspan="3" colspan="11" valign="bottom"><b>ผู้ตรวจสอบ : ......................................................................
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            ผู้อนุมัติ : ......................................................................</b></th>
                    <th colspan="3"><b>จำนวน</b></th>
                    <th colspan="7"><b>สาเหตุ</b></th>
                </tr>
                <tr>
                    <th colspan="3"><br></th>
                    <th colspan="7"><br></th>
                </tr>
                <tr>
                    <th colspan="3"><br></th>
                    <th colspan="7"><br></th>
                </tr>
            </table>
            <table class="table">
                <tr class="tr">
                    <td class="td" align="left">
                        Ref.WI-PP-2.12
                    </td>
                    <td class="td" align="right">
                        <!-- FM-PP-2.12.3,Issue #1 -->
                        FM-PP-2.12.2,Issued #1
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td colspan="5">
                        <a class="navbar-brand"><img src="./assets/images/STR.jpg" style="padding-left:10px;height:55px; width:auto;" /></a>
                    </td>
                    <td colspan="17" class="f12">
                        <b><i>SIAMTRUCK RADIAL CO.LTD.</i></b> <br>
                        <b><i>CURING REPORT LINE <?php echo $pressNo; ?></i></b>
                    </td>
                </tr>
                <tr>
                    <td rowspan="2" text-rotate="90" class="f10"><b>รายชื่อ</b></td>
                    <td colspan="21" class="f10"><br>
                        <b>DATE : <?php echo $datecuring; ?></b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>SHIFT : <?php if ($shift == "day") {
                                        echo "กลางวัน";
                                    } else {
                                        echo "กลางคืน";
                                    } ?></b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>GROUP :
                            <?php $ids = array();
                            foreach ($group_decode as $value) {
                                $ids[] = $value->Description;
                            }
                            echo implode(",", $ids);
                            ?>
                        </b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>REPORTED BY : ............................................</b>
                    </td>
                </tr>
                <tr>
                    <td><br><b>Press</b></td>
                    <td width="3%"><br><b>Side</b></td>
                    <td width="6%"><br><b>Cure code</b></td>
                    <td><br><b>Top Turn</b></td>
                    <?php if ($shift == "day") { ?>
                        <td><br><b>8.00-11.00</b></td>
                        <td><br><b>11.00-14.00</b></td>
                        <td><br><b>14.00-17.00</b></td>
                        <td><br><b>17.00-20.00</b></td>
                    <?php } else { ?>
                        <td><br><b>20.00-23.00</b></td>
                        <td><br><b>23.00-02.00</b></td>
                        <td><br><b>02.00-05.00</b></td>
                        <td><br><b>05.00-08.00</b></td>
                    <?php } ?>
                    <td width="6%"><br><b>Total</b></td>
                    <td width="4%"><br><b>%</b></td>
                    <td width="5%"><br><b>Press</b></td>
                    <td width="5%"><br><b>TimeOn</b></td>
                    <td width="5%"><br><b>TimeOff</b></td>
                    <td width="5%"><br><b>TotalTime</b></td>
                    <td width="20%" colspan="5"><br><b>Causes of down time</b></td>
                </tr>

                <tr text-rotate="90">
                    <td rowspan="9" text-rotate="90" class="f10">
                        <?php
                        // $nx = "";
                        // foreach ($dataname1 as $value) {
                        // $nx .= $value['Name'].",";
                        // }
                        // echo trim($nx,",");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b13; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b13 && $value['PressSide'] === 'L') {
                                $code13L .= $value['CuringCode'] . ",";
                                $top13L .= $value['rate12'] . ",";
                                $q1_text13L .= $value['Q1'] . ",";
                                $q2_text13L .= $value['Q2'] . ",";
                                $q3_text13L .= $value['Q3'] . ",";
                                $q4_text13L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code13L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top13L = trim($top13L, ",");
                        $top13L = explode(",", $top13L);
                        echo $top13L = $top13L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text13L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text13L, ","); ?></td>
                    <td><?php echo trim($q3_text13L, ","); ?></td>
                    <td><?php echo trim($q4_text13L, ","); ?></td>
                    <td><?php $rows25 = array($qty1115, $qty2115, $qty3115, $qty4115);
                        if (array_sum($rows25) != 0) {
                            echo $rows25_new = array_sum($rows25);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows25_new)) {

                            $newrate = ($rows25_new / $top13L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b13 && $value['PressSide'] === 'R') {
                                $code13R .= $value['CuringCode'] . ",";
                                $top13R .= $value['rate12'] . ",";
                                $q1_text13R .= $value['Q1'] . ",";
                                $q2_text13R .= $value['Q2'] . ",";
                                $q3_text13R .= $value['Q3'] . ",";
                                $q4_text13R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code13R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top13R = trim($top13R, ",");
                        $top13R = explode(",", $top13R);
                        echo $top13R = $top13R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text13R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text13R, ","); ?></td>
                    <td><?php echo trim($q3_text13R, ","); ?></td>
                    <td><?php echo trim($q4_text13R, ","); ?></td>
                    <td><?php $rows26 = array($qty1116, $qty2116, $qty3116, $qty4116);
                        if (array_sum($rows26) != 0) {
                            echo $rows26_new = array_sum($rows26);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows26_new)) {

                            $newrate = ($rows26_new / $top13R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b14; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b14 && $value['PressSide'] === 'L') {
                                $code14L .= $value['CuringCode'] . ",";
                                $top14L .= $value['rate12'] . ",";
                                $q1_text14L .= $value['Q1'] . ",";
                                $q2_text14L .= $value['Q2'] . ",";
                                $q3_text14L .= $value['Q3'] . ",";
                                $q4_text14L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code14L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top14L = trim($top14L, ",");
                        $top14L = explode(",", $top14L);
                        echo $top14L = $top14L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text14L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text14L, ","); ?></td>
                    <td><?php echo trim($q3_text14L, ","); ?></td>
                    <td><?php echo trim($q4_text14L, ","); ?></td>
                    <td><?php $rows27 = array($qty1117, $qty2117, $qty3117, $qty4117);
                        if (array_sum($rows27) != 0) {
                            echo $rows27_new = array_sum($rows27);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows27_new)) {

                            $newrate = ($rows27_new / $top14L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b14 && $value['PressSide'] === 'R') {
                                $code14R .= $value['CuringCode'] . ",";
                                $top14R .= $value['rate12'] . ",";
                                $q1_text14R .= $value['Q1'] . ",";
                                $q2_text14R .= $value['Q2'] . ",";
                                $q3_text14R .= $value['Q3'] . ",";
                                $q4_text14R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code14R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top14R = trim($top14R, ",");
                        $top14R = explode(",", $top14R);
                        echo $top14R = $top14R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text14R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text14R, ","); ?></td>
                    <td><?php echo trim($q3_text14R, ","); ?></td>
                    <td><?php echo trim($q4_text14R, ","); ?></td>
                    <td><?php $rows28 = array($qty1118, $qty2118, $qty3118, $qty4118);
                        if (array_sum($rows28) != 0) {
                            echo $rows28_new = array_sum($rows28);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows28_new)) {

                            $newrate = ($rows28_new / $top14R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b15; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b15 && $value['PressSide'] === 'L') {
                                $code15L .= $value['CuringCode'] . ",";
                                $top15L .= $value['rate12'] . ",";
                                $q1_text15L .= $value['Q1'] . ",";
                                $q2_text15L .= $value['Q2'] . ",";
                                $q3_text15L .= $value['Q3'] . ",";
                                $q4_text15L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code15L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top15L = trim($top15L, ",");
                        $top15L = explode(",", $top15L);
                        echo $top15L = $top15L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text15L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text15L, ","); ?></td>
                    <td><?php echo trim($q3_text15L, ","); ?></td>
                    <td><?php echo trim($q4_text15L, ","); ?></td>
                    <td><?php $rows29 = array($qty1119, $qty2119, $qty3119, $qty4119);
                        if (array_sum($rows29) != 0) {
                            echo $rows29_new = array_sum($rows29);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows29_new)) {

                            $newrate = ($rows29_new / $top15L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b15 && $value['PressSide'] === 'R') {
                                $code15R .= $value['CuringCode'] . ",";
                                $top15R .= $value['rate12'] . ",";
                                $q1_text15R .= $value['Q1'] . ",";
                                $q2_text15R .= $value['Q2'] . ",";
                                $q3_text15R .= $value['Q3'] . ",";
                                $q4_text15R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code15R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top15R = trim($top15R, ",");
                        $top15R = explode(",", $top15R);
                        echo $top15R = $top15R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text15R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text15R, ","); ?></td>
                    <td><?php echo trim($q3_text15R, ","); ?></td>
                    <td><?php echo trim($q4_text15R, ","); ?></td>
                    <td><?php $rows30 = array($qty1120, $qty2120, $qty3120, $qty4120);
                        if (array_sum($rows30) != 0) {
                            echo $rows30_new = array_sum($rows30);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows30_new)) {

                            $newrate = ($rows30_new / $top15R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b16; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b16 && $value['PressSide'] === 'L') {
                                $code16L .= $value['CuringCode'] . ",";
                                $top16L .= $value['rate12'] . ",";
                                $q1_text16L .= $value['Q1'] . ",";
                                $q2_text16L .= $value['Q2'] . ",";
                                $q3_text16L .= $value['Q3'] . ",";
                                $q4_text16L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code16L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top16L = trim($top16L, ",");
                        $top16L = explode(",", $top16L);
                        echo $top16L = $top16L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text16L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text16L, ","); ?></td>
                    <td><?php echo trim($q3_text16L, ","); ?></td>
                    <td><?php echo trim($q4_text16L, ","); ?></td>
                    <td><?php $rows31 = array($qty1121, $qty2121, $qty3121, $qty4121);
                        if (array_sum($rows31) != 0) {
                            echo $rows31_new = array_sum($rows31);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows31_new)) {

                            $newrate = ($rows31_new / $top16L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b16 && $value['PressSide'] === 'R') {
                                $code16R .= $value['CuringCode'] . ",";
                                $top16R .= $value['rate12'] . ",";
                                $q1_text16R .= $value['Q1'] . ",";
                                $q2_text16R .= $value['Q2'] . ",";
                                $q3_text16R .= $value['Q3'] . ",";
                                $q4_text16R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code16R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top16R = trim($top16R, ",");
                        $top16R = explode(",", $top16R);
                        echo $top16R = $top16R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text16R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text16R, ","); ?></td>
                    <td><?php echo trim($q3_text16R, ","); ?></td>
                    <td><?php echo trim($q4_text16R, ","); ?></td>
                    <td><?php $rows32 = array($qty1122, $qty2122, $qty3122, $qty4122);
                        if (array_sum($rows32) != 0) {
                            echo $rows32_new = array_sum($rows32);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows32_new)) {

                            $newrate = ($rows32_new / $top16R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>

                <tr text-rotate="90">
                    <td rowspan="9" text-rotate="90" class="f10">
                        <?php
                        // $nx = "";
                        // foreach ($dataname3 as $value) {
                        // $nx .= $value['Name'].",";
                        // }
                        // echo trim($nx,",");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b17; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b17 && $value['PressSide'] === 'L') {
                                $code17L .= $value['CuringCode'] . ",";
                                $top17L .= $value['rate12'] . ",";
                                $q1_text17L .= $value['Q1'] . ",";
                                $q2_text17L .= $value['Q2'] . ",";
                                $q3_text17L .= $value['Q3'] . ",";
                                $q4_text17L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code17L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top17L = trim($top17L, ",");
                        $top17L = explode(",", $top17L);
                        echo $top17L = $top17L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text17L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text17L, ","); ?></td>
                    <td><?php echo trim($q3_text17L, ","); ?></td>
                    <td><?php echo trim($q4_text17L, ","); ?></td>
                    <td><?php $rows33 = array($qty1123, $qty2123, $qty3123, $qty4123);
                        if (array_sum($rows33) != 0) {
                            echo $rows33_new = array_sum($rows33);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows33_new)) {

                            $newrate = ($rows33_new / $top17L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b17 && $value['PressSide'] === 'R') {
                                $code17R .= $value['CuringCode'] . ",";
                                $top17R .= $value['rate12'] . ",";
                                $q1_text17R .= $value['Q1'] . ",";
                                $q2_text17R .= $value['Q2'] . ",";
                                $q3_text17R .= $value['Q3'] . ",";
                                $q4_text17R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code17R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top17R = trim($top17R, ",");
                        $top17R = explode(",", $top17R);
                        echo $top17R = $top17R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text17R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text17R, ","); ?></td>
                    <td><?php echo trim($q3_text17R, ","); ?></td>
                    <td><?php echo trim($q4_text17R, ","); ?></td>
                    <td><?php $rows34 = array($qty1124, $qty2124, $qty3124, $qty4124);
                        if (array_sum($rows34) != 0) {
                            echo $rows34_new = array_sum($rows34);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows34_new)) {

                            $newrate = ($rows34_new / $top17R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b18; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        $temp_curing_code = '';
                        $temp_q1 = '';
                        $temp_q2 = '';
                        $temp_q3 = '';
                        $temp_q4 = '';

                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b18 && $value['PressSide'] === 'L') {

                                if ($temp_curing_code !== $value['CuringCode']) {
                                    $code18L .= $value['CuringCode'] . ",";
                                    $temp_curing_code = $value['CuringCode'];
                                }

                                $top18L .= $value['rate12'] . ",";

                                if ($temp_q1 !== $value['Q1']) {
                                    $q1_text18L .= $value['Q1'] . ",";
                                    $temp_q1 = $value['Q1'];
                                }

                                if ($temp_q2 !== $value['Q2']) {
                                    $q2_text18L .= $value['Q2'] . ",";
                                    $temp_q2 = $value['Q2'];
                                }

                                if ($temp_q3 !== $value['Q3']) {
                                    $q3_text18L .= $value['Q3'] . ",";
                                    $temp_q3 = $value['Q3'];
                                }

                                if ($temp_q4 !== $value['Q4']) {
                                    $q4_text18L .= $value['Q4'] . ",";
                                    $temp_q4 = $value['Q4'];
                                }
                            }
                        }
                        echo trim($code18L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top18L = trim($top18L, ",");
                        $top18L = explode(",", $top18L);
                        echo $top18L = $top18L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text18L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text18L, ","); ?></td>
                    <td><?php echo trim($q3_text18L, ","); ?></td>
                    <td><?php echo trim($q4_text18L, ","); ?></td>
                    <td><?php $rows35 = array($qty1125, $qty2125, $qty3125, $qty4125);
                        if (array_sum($rows35) != 0) {
                            echo $rows35_new = array_sum($rows35);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows35_new)) {

                            $newrate = ($rows35_new / $top18L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php

                        $temp_curing_code = '';
                        $temp_q1 = '';
                        $temp_q2 = '';
                        $temp_q3 = '';
                        $temp_q4 = '';
                        $code10R = '';
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b18 && $value['PressSide'] === 'R') {

                                if ($temp_curing_code !== $value['CuringCode']) {
                                    $code18R .= $value['CuringCode'] . ",";
                                    $temp_curing_code = $value['CuringCode'];
                                }

                                $top18R .= $value['rate12'] . ",";

                                if ($temp_q1 !== $value['Q1']) {
                                    $q1_text18R .= $value['Q1'] . ",";
                                    $temp_q1 = $value['Q1'];
                                }

                                if ($temp_q2 !== $value['Q2']) {
                                    $q2_text18R .= $value['Q2'] . ",";
                                    $temp_q2 = $value['Q2'];
                                }

                                if ($temp_q3 !== $value['Q3']) {
                                    $q3_text18R .= $value['Q3'] . ",";
                                    $temp_q3 = $value['Q3'];
                                }

                                if ($temp_q4 !== $value['Q4']) {
                                    $q4_text18R .= $value['Q4'] . ",";
                                    $temp_q4 = $value['Q4'];
                                }
                            }
                        }
                        echo trim($code18R, ",");
                        // echo $value['CuringCode'] . ' / ' . $value['CuringCode'];
                        ?>
                    </td>
                    <td>
                        <?php
                        $top18R = trim($top18R, ",");
                        $top18R = explode(",", $top18R);
                        echo $top18R = $top18R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text18R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text18R, ","); ?></td>
                    <td><?php echo trim($q3_text18R, ","); ?></td>
                    <td><?php echo trim($q4_text18R, ","); ?></td>
                    <td><?php $rows36 = array($qty1126, $qty2126, $qty3126, $qty4126);
                        if (array_sum($rows36) != 0) {
                            echo $rows36_new = array_sum($rows36);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows36_new)) {

                            $newrate = ($rows36_new / $top18R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td colspan="5"><b>
                            <center>จำนวนพนักงาน</center>
                        </b></td>
                    <td colspan="4"><b>
                            <center>สรุปการผลิต</center>
                        </b></td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b19; ?></td>
                    <td>L</td>
                    <td>
                        <?php

                        $temp_curing_code = '';
                        $temp_q1 = '';
                        $temp_q2 = '';
                        $temp_q3 = '';
                        $temp_q4 = '';

                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b19 && $value['PressSide'] === 'L') {

                                if ($temp_curing_code !== $value['CuringCode']) {
                                    $code19L .= $value['CuringCode'] . ",";
                                    $temp_curing_code = $value['CuringCode'];
                                }

                                $top19L .= $value['rate12'] . ",";

                                if ($temp_q1 !== $value['Q1']) {
                                    $q1_text19L .= $value['Q1'] . ",";
                                    $temp_q1 = $value['Q1'];
                                }

                                if ($temp_q2 !== $value['Q2']) {
                                    $q2_text19L .= $value['Q2'] . ",";
                                    $temp_q2 = $value['Q2'];
                                }

                                if ($temp_q3 !== $value['Q3']) {
                                    $q3_text19L .= $value['Q3'] . ",";
                                    $temp_q3 = $value['Q3'];
                                }

                                if ($temp_q4 !== $value['Q4']) {
                                    $q4_text19L .= $value['Q4'] . ",";
                                    $temp_q4 = $value['Q4'];
                                }
                            }
                        }
                        echo trim($code19L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top19L = trim($top19L, ",");
                        $top19L = explode(",", $top19L);
                        echo $top19L = $top19L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text19L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text19L, ","); ?></td>
                    <td><?php echo trim($q3_text19L, ","); ?></td>
                    <td><?php echo trim($q4_text19L, ","); ?></td>
                    <td><?php $rows37 = array($qty1127, $qty2127, $qty3127, $qty4127);
                        if (array_sum($rows37) != 0) {
                            echo $rows37_new = array_sum($rows37);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows37_new)) {

                            $newrate = ($rows37_new / $top19L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <th rowspan="2" width="7%">แผนก</th>
                    <th rowspan="2" width="5%">เต็ม</th>
                    <th rowspan="2" width="5%">มา</th>
                    <th rowspan="2" width="5%">ลา</th>
                    <th rowspan="2" width="5%">ขาด</th>
                    <th rowspan="2" width="5%">รายการ</th>
                    <th rowspan="2" width="6%">จำนวน<br>(เส้น)</th>
                    <th rowspan="2" width="5%" width="5%">รายการ</th>
                    <th rowspan="2" width="6%" width="6%">จำนวน<br>(เส้น)</th>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        $temp_curing_code = '';
                        $temp_q1 = '';
                        $temp_q2 = '';
                        $temp_q3 = '';
                        $temp_q4 = '';

                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b19 && $value['PressSide'] === 'R') {

                                if ($temp_curing_code !== $value['CuringCode']) {
                                    $code19R .= $value['CuringCode'] . ",";
                                    $temp_curing_code = $value['CuringCode'];
                                }

                                $top19R .= $value['rate12'] . ",";

                                if ($temp_q1 !== $value['Q1']) {
                                    $q1_text19R .= $value['Q1'] . ",";
                                    $temp_q1 = $value['Q1'];
                                }

                                if ($temp_q2 !== $value['Q2']) {
                                    $q2_text19R .= $value['Q2'] . ",";
                                    $temp_q2 = $value['Q2'];
                                }

                                if ($temp_q3 !== $value['Q3']) {
                                    $q3_text19R .= $value['Q3'] . ",";
                                    $temp_q3 = $value['Q3'];
                                }

                                if ($temp_q4 !== $value['Q4']) {
                                    $q4_text19R .= $value['Q4'] . ",";
                                    $temp_q4 = $value['Q4'];
                                }
                            }
                        }
                        echo trim($code19R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top19R = trim($top19R, ",");
                        $top19R = explode(",", $top19R);
                        echo $top19R = $top19R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text19R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text19R, ","); ?></td>
                    <td><?php echo trim($q3_text19R, ","); ?></td>
                    <td><?php echo trim($q4_text19R, ","); ?></td>
                    <td><?php $rows38 = array($qty1128, $qty2128, $qty3128, $qty4128);
                        if (array_sum($rows38) != 0) {
                            echo $rows38_new = array_sum($rows38);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows38_new)) {

                            $newrate = ($rows38_new / $top19R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                </tr>
                <tr>
                    <td rowspan="2"><?php echo $b20; ?></td>
                    <td>L</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b20 && $value['PressSide'] === 'L') {
                                $code20L .= $value['CuringCode'] . ",";
                                $top20L .= $value['rate12'] . ",";
                                $q1_text20L .= $value['Q1'] . ",";
                                $q2_text20L .= $value['Q2'] . ",";
                                $q3_text20L .= $value['Q3'] . ",";
                                $q4_text20L .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code20L, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top20L = trim($top20L, ",");
                        $top20L = explode(",", $top20L);
                        echo $top20L = $top20L[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text20L, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text20L, ","); ?></td>
                    <td><?php echo trim($q3_text20L, ","); ?></td>
                    <td><?php echo trim($q4_text20L, ","); ?></td>
                    <td><?php $rows39 = array($qty1129, $qty2129, $qty3129, $qty4129);
                        if (array_sum($rows39) != 0) {
                            echo $rows39_new = array_sum($rows39);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows39_new)) {

                            $newrate = ($rows39_new / $top20L) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>R</td>
                    <td>
                        <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b20 && $value['PressSide'] === 'R') {
                                $code20R .= $value['CuringCode'] . ",";
                                $top20R .= $value['rate12'] . ",";
                                $q1_text20R .= $value['Q1'] . ",";
                                $q2_text20R .= $value['Q2'] . ",";
                                $q3_text20R .= $value['Q3'] . ",";
                                $q4_text20R .= $value['Q4'] . ",";
                            }
                        }
                        echo trim($code20R, ",");
                        ?>
                    </td>
                    <td>
                        <?php
                        $top20R = trim($top20R, ",");
                        $top20R = explode(",", $top20R);
                        echo $top20R = $top20R[0];
                        ?>
                    </td>
                    <td>
                        <?php echo trim($q1_text20R, ","); ?>
                    </td>
                    <td><?php echo trim($q2_text20R, ","); ?></td>
                    <td><?php echo trim($q3_text20R, ","); ?></td>
                    <td><?php echo trim($q4_text20R, ","); ?></td>
                    <td><?php $rows40 = array($qty1130, $qty2130, $qty3130, $qty4130);
                        if (array_sum($rows40) != 0) {
                            echo $rows40_new = array_sum($rows40);
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($rows40_new)) {

                            $newrate = ($rows40_new / $top20R) * 100;
                            if (!is_infinite($newrate)) echo $newrate_format_number = number_format($newrate, 2, '.', '');
                        } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th rowspan="3" colspan="3" class="f10">TOTAL</th>
                    <td rowspan="3"></td>
                    <td rowspan="3">
                        <?php $sumtop = array($top13L, $top13R, $top14L, $top14R, $top15L, $top15R, $top16L, $top16R, $top17L, $top17R, $top18L, $top18R, $top19L, $top19R, $top20L, $top20R);
                        if (array_sum($sumtop) != 0) {
                            echo $sumtop = array_sum($sumtop);
                        }
                        ?>
                    </td>
                    <td rowspan="3">
                        <?php $sumq1 = array($qty1115, $qty1116, $qty1117, $qty1118, $qty1119, $qty1120, $qty1121, $qty1122, $qty1123, $qty1124, $qty1125, $qty1126, $qty1127, $qty1128, $qty1129, $qty1130);
                        if (array_sum($sumq1) != 0) {
                            echo $sumq1 = array_sum($sumq1);
                        }
                        ?>
                    </td>
                    <td rowspan="3">
                        <?php $sumq2 = array($qty2115, $qty2116, $qty2117, $qty2118, $qty2119, $qty2120, $qty2121, $qty2122, $qty2123, $qty2124, $qty2125, $qty2126, $qty2127, $qty2128, $qty2129, $qty2130);
                        if (array_sum($sumq2) != 0) {
                            echo $sumq2 = array_sum($sumq2);
                        }
                        ?>
                    </td>
                    <td rowspan="3">
                        <?php $sumq3 = array($qty3115, $qty3116, $qty3117, $qty3118, $qty3119, $qty3120, $qty3121, $qty3122, $qty3123, $qty3124, $qty3125, $qty3126, $qty3127, $qty3128, $qty3129, $qty3130);
                        if (array_sum($sumq3) != 0) {
                            echo $sumq3 = array_sum($sumq3);
                        }
                        ?>
                    </td>
                    <td rowspan="3">
                        <?php $sumq4 = array($qty4115, $qty4116, $qty4117, $qty4118, $qty4119, $qty4120, $qty4121, $qty4122, $qty4123, $qty4124, $qty4125, $qty4126, $qty4127, $qty4128, $qty4129, $qty4130);
                        if (array_sum($sumq4) != 0) {
                            echo $sumq4 = array_sum($sumq4);
                        }
                        ?>
                    </td>
                    <td rowspan="3">
                        <?php
                        // foreach ($datajson as $value) {


                        //     if (
                        //         $value->PressNo == "P13" || $value->PressNo == "P14" || $value->PressNo == "P15" || $value->PressNo == "P16"
                        //         || $value->PressNo == "P17" || $value->PressNo == "P18" || $value->PressNo == "P19" || $value->PressNo == "P20"
                        //     ) {
                        //         $sum2 = 0;
                        //         $rows2 = array($value->$sumq1, $value->$sumq2, $value->$sumq3, $value->$sumq4);
                        //         $QQ2 = array_sum($rows2);
                        //         $sumrows2 += $QQ2;
                        //     }
                        // }
                        // if ($sumrows2 != 0) {
                        //     echo $sumrows2;
                        // }
                        $sumq_all = array($sumq1, $sumq2, $sumq3, $sumq4);
                        if (array_sum($sumq_all) != 0) {
                            $sumq_all = array_sum($sumq_all);
                            echo $sumq_all;
                        }
                        ?>
                    </td>
                    <td rowspan="3">
                        <?php $sumq_all = array($sumq1, $sumq2, $sumq3, $sumq4);
                        if (array_sum($sumq_all) != 0) {
                            $sumq_all = array_sum($sumq_all);
                            if (array_sum($sumtop) == '0' || $sumtop == 0) {
                                echo "";
                            } else {
                                $sumper = ($sumq_all / $sumtop) * 100;
                                echo $sumper_format_number = number_format($sumper, 2, '.', '');
                            }
                        }
                        ?>
                    </td>
                    <td><br></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>TOTAL</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>TOTAL</td>
                    <td></td>
                    <td>TOTAL</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="10"><b>รายงานอุบัติเหตุ</b></td>
                </tr>
                <tr>
                    <th rowspan="3" colspan="11" valign="bottom"><b>ผู้ตรวจสอบ : ......................................................................
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            ผู้อนุมัติ : ......................................................................</b></th>
                    <th colspan="3"><b>จำนวน</b></th>
                    <th colspan="7"><b>สาเหตุ</b></th>
                </tr>
                <tr>
                    <th colspan="3"><br></th>
                    <th colspan="7"><br></th>
                </tr>
                <tr>
                    <th colspan="3"><br></th>
                    <th colspan="7"><br></th>
                </tr>
            </table>
            <table class="table">
                <tr class="tr">
                    <td class="td" align="left">
                        Ref.WI-PP-2.12
                    </td>
                    <td class="td" align="right">
                        <!-- FM-PP-2.12.3,Issue #1 -->
                        FM-PP-2.12.2,Issued #1
                    </td>
                </tr>
            </table>




        <?php

        } ?>
    </div>
</body>

</html>

<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th', 'A4-L', 0, '', 3, 3, 3, 3);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
