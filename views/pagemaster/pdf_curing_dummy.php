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
    font-size:8px;
    font-family:"Angsana New";
    }

    td, tr, th {
        border: 1px solid #000000;
        text-align: center;
        padding: 5px;
    }

    .table {
    border-collapse: collapse;
    width: 100%;
    font-size: 8px;
    }

    .td, .tr, .th {
        border: 0px solid #000000;
        text-align: left;
        padding: 4px;
    }

    .double_td{
    border: 2px solid black;
    }
    .f12{
        font-size:14px;
        font-family:"Angsana New";
    }
    .f10{
        font-size:10px;
        font-family:"Angsana New";
    }

</style>
</head>
<body>
<div class="container">

<?php if(isset($press1)){ ?>
    <table>
        <tr>
            <td colspan="5">
                <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
                style="padding-left:10px;height:55px; width:auto;" /></a>
            </td>
            <td colspan="17" class="f12">
                <b><i>SIAMTRUCK RADIAL CO.LTD.</i></b> <br>
                <b><i>CURING REPORT LINE Dummy</i></b>
            </td>
        </tr>
        <tr>
            <td rowspan="2" text-rotate="90" class="f10"><b>รายชื่อ</b></td>
            <td colspan="21" class="f10"><br>
            <b>DATE : <?php echo $datecuring; ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>SHIFT : <?php if($shift=="day"){ echo "กลางวัน"; }else{ echo "กลางคืน";} ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>GROUP : Dummy</b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>REPORTED BY : ............................................</b>
            </td>
        </tr>
        <tr>
            <td><br><b>Press</b></td>
            <td width="3%"><br><b>Side</b></td>
            <td width="6%"><br><b>Cure code</b></td>
            <td><br><b>Top Turn</b></td>
            <?php if ($shift=="day") {?>
            <td><br><b>8.00-11.00</b></td>
            <td><br><b>11.00-14.00</b></td>
            <td><br><b>14.00-17.00</b></td>
            <td><br><b>17.00-20.00</b></td>
            <?php }else{?>
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
               
            </td>
        </tr>
        <tr>
            <td rowspan="2">A13</td>
            <td>
                L
            </td>
            <td>
                <?php
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'A13' && $value['PressSide'] === 'L') {
                                $codeA13L .= $value['CuringCode'].",";
                                $topA13L .= $value['rate12'].",";
                                $q1_textA13L .= $value['Q1'].",";
                                $q2_textA13L .= $value['Q2'].",";
                                $q3_textA13L .= $value['Q3'].",";
                                $q4_textA13L .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeA13L, ",");
                ?>
            </td>
            <td>
                <?php
                    $topA13L = trim($topA13L, ",");
                    $topA13L = explode(",", $topA13L);
                    echo $topA13L = $topA13L[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textA13L = trim($q1_textA13L, ",");
                    echo $q1_textA13L; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textA13L = trim($q2_textA13L, ",");
                    echo $q2_textA13L; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textA13L = trim($q3_textA13L, ",");
                    echo $q3_textA13L; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textA13L = trim($q4_textA13L, ",");
                    echo $q4_textA13L; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsA13L=array($q1_textA13L,$q2_textA13L,$q3_textA13L,$q4_textA13L);
                    if (array_sum($rowsA13L)!=0) {
                        echo $rowsA13L_new = array_sum($rowsA13L);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsA13L_new)) {
                        $newrate = ($rowsA13L_new/$topA13L)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'A13' && $value['PressSide'] === 'R') {
                                $codeA13R .= $value['CuringCode'].",";
                                $topA13R .= $value['rate12'].",";
                                $q1_textA13R .= $value['Q1'].",";
                                $q2_textA13R .= $value['Q2'].",";
                                $q3_textA13R .= $value['Q3'].",";
                                $q4_textA13R .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeA13R, ",");
                ?>
            </td>
            <td>
                <?php
                    $topA13R = trim($topA13R, ",");
                    $topA13R = explode(",", $topA13R);
                    echo $topA13R = $topA13R[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textA13R = trim($q1_textA13R, ",");
                    echo $q1_textA13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textA13R = trim($q2_textA13R, ",");
                    echo $q2_textA13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textA13R = trim($q3_textA13R, ",");
                    echo $q3_textA13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textA13R = trim($q4_textA13R, ",");
                    echo $q4_textA13R; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsA13R=array($q1_textA13R,$q2_textA13R,$q3_textA13R,$q4_textA13R);
                    if (array_sum($rowsA13R)!=0) {
                        echo $rowsA13R_new = array_sum($rowsA13R);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsA13R_new)) {
                        $newrate = ($rowsA13R_new/$topA13R)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2">A14</td>
            <td>
                L
            </td>
            <td>
                <?php
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'A14' && $value['PressSide'] === 'L') {
                                $codeA14L .= $value['CuringCode'].",";
                                $topA14L .= $value['rate12'].",";
                                $q1_textA14L .= $value['Q1'].",";
                                $q2_textA14L .= $value['Q2'].",";
                                $q3_textA14L .= $value['Q3'].",";
                                $q4_textA14L .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeA14L, ",");
                ?>
            </td>
            <td>
                <?php
                    $topA14L = trim($topA14L, ",");
                    $topA14L = explode(",", $topA14L);
                    echo $topA14L = $topA14L[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textA14L = trim($q1_textA14L, ",");
                    echo $q1_textA14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textA14L = trim($q2_textA14L, ",");
                    echo $q2_textA14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textA14L = trim($q3_textA14L, ",");
                    echo $q3_textA14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textA14L = trim($q4_textA14L, ",");
                    echo $q4_textA14L; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsA14L=array($q1_textA14L,$q2_textA14L,$q3_textA14L,$q4_textA14L);
                    if (array_sum($rowsA14L)!=0) {
                        echo $rowsA14L_new = array_sum($rowsA14L);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsA14L_new)) {
                        $newrate = ($rowsA14L_new/$topA14L)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'A14' && $value['PressSide'] === 'R') {
                                $codeA14R .= $value['CuringCode'].",";
                                $topA14R .= $value['rate12'].",";
                                $q1_textA14R .= $value['Q1'].",";
                                $q2_textA14R .= $value['Q2'].",";
                                $q3_textA14R .= $value['Q3'].",";
                                $q4_textA14R .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeA14R, ",");
                ?>
            </td>
            <td>
                <?php
                    $topA14R = trim($topA14R, ",");
                    $topA14R = explode(",", $topA14R);
                    echo $topA14R = $topA14R[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textA14R = trim($q1_textA14R, ",");
                    echo $q1_textA14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textA14R = trim($q2_textA14R, ",");
                    echo $q2_textA14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textA14R = trim($q3_textA14R, ",");
                    echo $q3_textA14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textA14R = trim($q4_textA14R, ",");
                    echo $q4_textA14R; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsA14R=array($q1_textA14R,$q2_textA14R,$q3_textA14R,$q4_textA14R);
                    if (array_sum($rowsA14R)!=0) {
                        echo $rowsA14R_new = array_sum($rowsA14R);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsA14R_new)) {
                        $newrate = ($rowsA14R_new/$topA14R)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2">B13</td>
            <td>
                L
            </td>
            <td>
                <?php
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'B13' && $value['PressSide'] === 'L') {
                                $codeB13L .= $value['CuringCode'].",";
                                $topB13L .= $value['rate12'].",";
                                $q1_textB13L .= $value['Q1'].",";
                                $q2_textB13L .= $value['Q2'].",";
                                $q3_textB13L .= $value['Q3'].",";
                                $q4_textB13L .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeB13L, ",");
                ?>
            </td>
            <td>
                <?php
                    $topB13L = trim($topB13L, ",");
                    $topB13L = explode(",", $topB13L);
                    echo $topB13L = $topB13L[0];
                ?>
            </td>
            <td>
                <?php
                    $q1_textB13L = trim($q1_textB13L, ","); 
                    echo $q1_textB13L; 
                ?>
            </td>
            <td>
                <?php
                    $q2_textB13L = trim($q2_textB13L, ","); 
                    echo $q2_textB13L; 
                ?>
            </td>
            <td>
                <?php
                    $q3_textB13L = trim($q3_textB13L, ","); 
                    echo $q3_textB13L; 
                ?>
            </td>
            <td>
                <?php
                    $q4_textB13L = trim($q4_textB13L, ","); 
                    echo $q4_textB13L; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsB13L=array($q1_textB13L,$q2_textB13L,$q3_textB13L,$q4_textB13L);
                    if (array_sum($rowsB13L)!=0) {
                        echo $rowsB13L_new = array_sum($rowsB13L);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsB13L_new)) {
                        $newrate = ($rowsB13L_new/$topB13L)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'B13' && $value['PressSide'] === 'R') {
                                $codeB13R .= $value['CuringCode'].",";
                                $topB13R .= $value['rate12'].",";
                                $q1_textB13R .= $value['Q1'].",";
                                $q2_textB13R .= $value['Q2'].",";
                                $q3_textB13R .= $value['Q3'].",";
                                $q4_textB13R .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeB13R, ",");
                ?>
            </td>
            <td>
                <?php
                    $topB13R = trim($topB13R, ",");
                    $topB13R = explode(",", $topB13R);
                    echo $topB13R = $topB13R[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textB13R = trim($q1_textB13R, ",");
                    echo $q1_textB13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textB13R = trim($q2_textB13R, ",");
                    echo $q2_textB13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textB13R = trim($q3_textB13R, ",");
                    echo $q3_textB13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textB13R = trim($q4_textB13R, ",");
                    echo $q4_textB13R; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsB13R=array($q1_textB13R,$q2_textB13R,$q3_textB13R,$q4_textB13R);
                    if (array_sum($rowsB13R)!=0) {
                        echo $rowsB13R_new = array_sum($rowsB13R);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsB13R_new)) {
                        $newrate = ($rowsB13R_new/$topB13R)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2">B14</td>
            <td>
                L
            </td>
            <td>
                <?php
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'B14' && $value['PressSide'] === 'L') {
                                $codeB14L .= $value['CuringCode'].",";
                                $topB14L .= $value['rate12'].",";
                                $q1_textB14L .= $value['Q1'].",";
                                $q2_textB14L .= $value['Q2'].",";
                                $q3_textB14L .= $value['Q3'].",";
                                $q4_textB14L .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeB14L, ",");
                ?>
            </td>
            <td>
                <?php
                    $topB14L = trim($topB14L, ",");
                    $topB14L = explode(",", $topB14L);
                    echo $topB14L = $topB14L[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textB14L = trim($q1_textB14L, ",");
                    echo $q1_textB14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textB14L = trim($q2_textB14L, ",");
                    echo $q2_textB14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textB14L = trim($q3_textB14L, ",");
                    echo $q3_textB14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textB14L = trim($q4_textB14L, ",");
                    echo $q4_textB14L; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsB14L=array($q1_textB14L,$q2_textB14L,$q3_textB14L,$q4_textB14L);
                    if (array_sum($rowsB14L)!=0) {
                        echo $rowsB14L_new = array_sum($rowsB14L);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsB14L_new)) {
                        $newrate = ($rowsB14L_new/$topB14L)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'B14' && $value['PressSide'] === 'R') {
                                $codeB14R .= $value['CuringCode'].",";
                                $topB14R .= $value['rate12'].",";
                                $q1_textB14R .= $value['Q1'].",";
                                $q2_textB14R .= $value['Q2'].",";
                                $q3_textB14R .= $value['Q3'].",";
                                $q4_textB14R .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeB14R, ",");
                ?>
            </td>
            <td>
                <?php
                    $topB14R = trim($topB14R, ",");
                    $topB14R = explode(",", $topB14R);
                    echo $topB14R = $topB14R[0];
                ?>
            </td>
            <td>
                <?php
                    $q1_textB14R = trim($q1_textB14R, ",");
                    echo $q1_textB14R; 
                ?>
            </td>
            <td>
                <?php
                    $q2_textB14R = trim($q2_textB14R, ",");
                    echo $q2_textB14R; 
                ?>
            </td>
            <td>
                <?php
                    $q3_textB14R = trim($q3_textB14R, ",");
                    echo $q3_textB14R; 
                ?>
            </td>
            <td>
                <?php
                    $q4_textB14R = trim($q4_textB14R, ",");
                    echo $q4_textB14R; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsB14R=array($q1_textB14R,$q2_textB14R,$q3_textB14R,$q4_textB14R);
                    if (array_sum($rowsB14R)!=0) {
                        echo $rowsB14R_new = array_sum($rowsB14R);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsB14R_new)) {
                        $newrate = ($rowsB14R_new/$topB14R)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>

        <tr text-rotate="90">
            <td rowspan="9" text-rotate="90" class="f10">
               
            </td>
        </tr>
        <tr>
            <td rowspan="2">C13</td>
            <td>
                L
            </td>
            <td>
                <?php
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'C13' && $value['PressSide'] === 'L') {
                                $codeC13L .= $value['CuringCode'].",";
                                $topC13L .= $value['rate12'].",";
                                $q1_textC13L .= $value['Q1'].",";
                                $q2_textC13L .= $value['Q2'].",";
                                $q3_textC13L .= $value['Q3'].",";
                                $q4_textC13L .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeC13L, ",");
                ?>
            </td>
            <td>
                <?php
                    $topC13L = trim($topC13L, ",");
                    $topC13L = explode(",", $topC13L);
                    echo $topC13L = $topC13L[0];
                ?>
            </td>
            <td>
                <?php
                    $q1_textC13L = trim($q1_textC13L, ",");
                    echo $q1_textC13L; 
                ?>
            </td>
            <td>
                <?php
                    $q2_textC13L = trim($q2_textC13L, ",");
                    echo $q2_textC13L; 
                ?>
            </td>
            <td>
                <?php
                    $q3_textC13L = trim($q3_textC13L, ",");
                    echo $q3_textC13L; 
                ?>
            </td>
            <td>
                <?php
                    $q4_textC13L = trim($q4_textC13L, ",");
                    echo $q4_textC13L; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsC13L=array($q1_textC13L,$q2_textC13L,$q3_textC13L,$q4_textC13L);
                    if (array_sum($rowsC13L)!=0) {
                        echo $rowsC13L_new = array_sum($rowsC13L);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsC13L_new)) {
                        $newrate = ($rowsC13L_new/$topC13L)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'C13' && $value['PressSide'] === 'R') {
                                $codeC13R .= $value['CuringCode'].",";
                                $topC13R .= $value['rate12'].",";
                                $q1_textC13R .= $value['Q1'].",";
                                $q2_textC13R .= $value['Q2'].",";
                                $q3_textC13R .= $value['Q3'].",";
                                $q4_textC13R .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeC13R, ",");
                ?>
            </td>
            <td>
                <?php
                    $topC13R = trim($topC13R, ",");
                    $topC13R = explode(",", $topC13R);
                    echo $topC13R = $topC13R[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textC13R = trim($q1_textC13R, ",");
                    echo $q1_textC13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textC13R = trim($q2_textC13R, ",");
                    echo $q2_textC13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textC13R = trim($q3_textC13R, ",");
                    echo $q3_textC13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textC13R = trim($q4_textC13R, ",");
                    echo $q4_textC13R; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsC13R=array($q1_textC13R,$q2_textC13R,$q3_textC13R,$q4_textC13R);
                    if (array_sum($rowsC13R)!=0) {
                        echo $rowsC13R_new = array_sum($rowsC13R);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsC13R_new)) {
                        $newrate = ($rowsC13R_new/$topC13R)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2">C14</td>
            <td>
                L
            </td>
            <td>
                <?php
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'C14' && $value['PressSide'] === 'L') {
                                $codeC14L .= $value['CuringCode'].",";
                                $topC14L .= $value['rate12'].",";
                                $q1_textC14L .= $value['Q1'].",";
                                $q2_textC14L .= $value['Q2'].",";
                                $q3_textC14L .= $value['Q3'].",";
                                $q4_textC14L .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeC14L, ",");
                ?>
            </td>
            <td>
                <?php
                    $topC14L = trim($topC14L, ",");
                    $topC14L = explode(",", $topC14L);
                    echo $topC14L = $topC14L[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textC14L = trim($q1_textC14L, ",");
                    echo $q1_textC14L;
                ?>
            </td>
            <td>
                <?php 
                    $q2_textC14L = trim($q2_textC14L, ",");
                    echo $q2_textC14L;
                ?>
            </td>
            <td>
                <?php 
                    $q3_textC14L = trim($q3_textC14L, ",");
                    echo $q3_textC14L;
                ?>
            </td>
            <td>
                <?php 
                    $q4_textC14L = trim($q4_textC14L, ",");
                    echo $q4_textC14L;
                ?>
            </td>
            <td>
                <?php 
                    $rowsC14L=array($q1_textC14L,$q2_textC14L,$q3_textC14L,$q4_textC14L);
                    if (array_sum($rowsC14L)!=0) {
                        echo $rowsC14L_new = array_sum($rowsC14L);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsC14L_new)) {
                        $newrate = ($rowsC14L_new/$topC14L)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'C14' && $value['PressSide'] === 'R') {
                                $codeC14R .= $value['CuringCode'].",";
                                $topC14R .= $value['rate12'].",";
                                $q1_textC14R .= $value['Q1'].",";
                                $q2_textC14R .= $value['Q2'].",";
                                $q3_textC14R .= $value['Q3'].",";
                                $q4_textC14R .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeC14R, ",");
                ?>
            </td>
            <td>
                <?php
                    $topC14R = trim($topC14R, ",");
                    $topC14R = explode(",", $topC14R);
                    echo $topC14R = $topC14R[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textC14R = trim($q1_textC14R, ",");
                    echo $q1_textC14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textC14R = trim($q2_textC14R, ",");
                    echo $q2_textC14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textC14R = trim($q3_textC14R, ",");
                    echo $q3_textC14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textC14R = trim($q4_textC14R, ",");
                    echo $q4_textC14R; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsC14R=array($q1_textC14R,$q2_textC14R,$q3_textC14R,$q4_textC14R);
                    if (array_sum($rowsC14R)!=0) {
                        echo $rowsC14R_new = array_sum($rowsC14R);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsC14R_new)) {
                        $newrate = ($rowsC14R_new/$topC14R)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2">D13</td>
            <td>
                L
            </td>
            <td>
                <?php
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'D13' && $value['PressSide'] === 'L') {
                                $codeD13L .= $value['CuringCode'].",";
                                $topD13L .= $value['rate12'].",";
                                $q1_textD13L .= $value['Q1'].",";
                                $q2_textD13L .= $value['Q2'].",";
                                $q3_textD13L .= $value['Q3'].",";
                                $q4_textD13L .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeD13L, ",");
                ?>
            </td>
            <td>
                <?php
                    $topD13L = trim($topD13L, ",");
                    $topD13L = explode(",", $topD13L);
                    echo $topD13L = $topD13L[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textD13L = trim($q1_textD13L, ",");
                    echo $q1_textD13L; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textD13L = trim($q2_textD13L, ",");
                    echo $q2_textD13L; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textD13L = trim($q3_textD13L, ",");
                    echo $q3_textD13L; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textD13L = trim($q4_textD13L, ",");
                    echo $q4_textD13L; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsD13L=array($q1_textD13L,$q2_textD13L,$q3_textD13L,$q4_textD13L);
                    if (array_sum($rowsD13L)!=0) {
                        echo $rowsD13L_new = array_sum($rowsD13L);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsD13L_new)) {
                        $newrate = ($rowsD13L_new/$topD13L)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'D13' && $value['PressSide'] === 'R') {
                                $codeD13R .= $value['CuringCode'].",";
                                $topD13R .= $value['rate12'].",";
                                $q1_textD13R .= $value['Q1'].",";
                                $q2_textD13R .= $value['Q2'].",";
                                $q3_textD13R .= $value['Q3'].",";
                                $q4_textD13R .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeD13R, ",");
                ?>
            </td>
            <td>
                <?php
                    $topD13R = trim($topD13R, ",");
                    $topD13R = explode(",", $topD13R);
                    echo $topD13R = $topD13R[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textD13R = trim($q1_textD13R, ",");
                    echo $q1_textD13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textD13R = trim($q2_textD13R, ",");
                    echo $q2_textD13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textD13R = trim($q3_textD13R, ",");
                    echo $q3_textD13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textD13R = trim($q4_textD13R, ",");
                    echo $q4_textD13R; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsD13R=array($q1_textD13R,$q2_textD13R,$q3_textD13R,$q4_textD13R);
                    if (array_sum($rowsD13R)!=0) {
                        echo $rowsD13R_new = array_sum($rowsD13R);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsD13R_new)) {
                        $newrate = ($rowsD13R_new/$topD13R)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2">D14</td>
            <td>
                L
            </td>
            <td>
                <?php
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'D14' && $value['PressSide'] === 'L') {
                                $codeD14L .= $value['CuringCode'].",";
                                $topD14L .= $value['rate12'].",";
                                $q1_textD14L .= $value['Q1'].",";
                                $q2_textD14L .= $value['Q2'].",";
                                $q3_textD14L .= $value['Q3'].",";
                                $q4_textD14L .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeD14L, ",");
                ?>
            </td>
            <td>
                <?php
                    $topD14L = trim($topD14L, ",");
                    $topD14L = explode(",", $topD14L);
                    echo $topD14L = $topD14L[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textD14L = trim($q1_textD14L, ",");
                    echo $q1_textD14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textD14L = trim($q2_textD14L, ",");
                    echo $q2_textD14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textD14L = trim($q3_textD14L, ",");
                    echo $q3_textD14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textD14L = trim($q4_textD14L, ",");
                    echo $q4_textD14L; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsD14L=array($q1_textD14L,$q2_textD14L,$q3_textD14L,$q4_textD14L);
                    if (array_sum($rowsD14L)!=0) {
                        echo $rowsD14L_new = array_sum($rowsD14L);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsD14L_new)) {
                        $newrate = ($rowsD14L_new/$topD14L)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'D14' && $value['PressSide'] === 'R') {
                                $codeD14R .= $value['CuringCode'].",";
                                $topD14R .= $value['rate12'].",";
                                $q1_textD14R .= $value['Q1'].",";
                                $q2_textD14R .= $value['Q2'].",";
                                $q3_textD14R .= $value['Q3'].",";
                                $q4_textD14R .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeD14R, ",");
                ?>
            </td>
            <td>
                <?php
                    $topD14R = trim($topD14R, ",");
                    $topD14R = explode(",", $topD14R);
                    echo $topD14R = $topD14R[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textD14R = trim($q1_textD14R, ",");
                    echo $q1_textD14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textD14R = trim($q2_textD14R, ",");
                    echo $q2_textD14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textD14R = trim($q3_textD14R, ",");
                    echo $q3_textD14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textD14R = trim($q4_textD14R, ",");
                    echo $q4_textD14R; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsD14R=array($q1_textD14R,$q2_textD14R,$q3_textD14R,$q4_textD14R);
                    if (array_sum($rowsD14R)!=0) {
                        echo $rowsD14R_new = array_sum($rowsD14R);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsD14R_new)) {
                        $newrate = ($rowsD14R_new/$topD14R)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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

                ?>
            </td>
        </tr>
        <tr>
            <td rowspan="2">E13</td>
            <td>
                L
            </td>
            <td>
                <?php
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'E13' && $value['PressSide'] === 'L') {
                                $codeE13L .= $value['CuringCode'].",";
                                $topE13L .= $value['rate12'].",";
                                $q1_textE13L .= $value['Q1'].",";
                                $q2_textE13L .= $value['Q2'].",";
                                $q3_textE13L .= $value['Q3'].",";
                                $q4_textE13L .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeE13L, ",");
                ?>
            </td>
            <td>
                <?php
                    $topE13L = trim($topE13L, ",");
                    $topE13L = explode(",", $topE13L);
                    echo $topE13L = $topE13L[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textE13L = trim($q1_textE13L, ",");
                    echo $q1_textE13L; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textE13L = trim($q2_textE13L, ",");
                    echo $q2_textE13L; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textE13L = trim($q3_textE13L, ",");
                    echo $q3_textE13L; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textE13L = trim($q4_textE13L, ",");
                    echo $q4_textE13L; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsE13L=array($q1_textE13L,$q2_textE13L,$q3_textE13L,$q4_textE13L);
                    if (array_sum($rowsE13L)!=0) {
                        echo $rowsE13L_new = array_sum($rowsE13L);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsE13L_new)) {
                        $newrate = ($rowsE13L_new/$topE13L)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'E13' && $value['PressSide'] === 'R') {
                                $codeE13R .= $value['CuringCode'].",";
                                $topE13R .= $value['rate12'].",";
                                $q1_textE13R .= $value['Q1'].",";
                                $q2_textE13R .= $value['Q2'].",";
                                $q3_textE13R .= $value['Q3'].",";
                                $q4_textE13R .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeE13R, ",");
                ?>
            </td>
            <td>
                <?php
                    $topE13R = trim($topE13R, ",");
                    $topE13R = explode(",", $topE13R);
                    echo $topE13R = $topE13R[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textE13R = trim($q1_textE13R, ",");
                    echo $q1_textE13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textE13R = trim($q2_textE13R, ",");
                    echo $q2_textE13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textE13R = trim($q3_textE13R, ",");
                    echo $q3_textE13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textE13R = trim($q4_textE13R, ",");
                    echo $q4_textE13R; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsE13R=array($q1_textE13R,$q2_textE13R,$q3_textE13R,$q4_textE13R);
                    if (array_sum($rowsE13R)!=0) {
                        echo $rowsE13R_new = array_sum($rowsE13R);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsE13R_new)) {
                        $newrate = ($rowsE13R_new/$topE13R)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2">E14</td>
            <td>
                L
            </td>
            <td>
                <?php
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'E14' && $value['PressSide'] === 'L') {
                                $codeE14L .= $value['CuringCode'].",";
                                $topE14L .= $value['rate12'].",";
                                $q1_textE14L .= $value['Q1'].",";
                                $q2_textE14L .= $value['Q2'].",";
                                $q3_textE14L .= $value['Q3'].",";
                                $q4_textE14L .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeE14L, ",");
                ?>
            </td>
            <td>
                <?php
                    $topE14L = trim($topE14L, ",");
                    $topE14L = explode(",", $topE14L);
                    echo $topE14L = $topE14L[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textE14L = trim($q1_textE14L, ",");
                    echo $q1_textE14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textE14L = trim($q2_textE14L, ",");
                    echo $q2_textE14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textE14L = trim($q3_textE14L, ",");
                    echo $q3_textE14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textE14L = trim($q4_textE14L, ",");
                    echo $q4_textE14L; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsE14L=array($q1_textE14L,$q2_textE14L,$q3_textE14L,$q4_textE14L);
                    if (array_sum($rowsE14L)!=0) {
                        echo $rowsE14L_new = array_sum($rowsE14L);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsE14L_new)) {
                        $newrate = ($rowsE14L_new/$topE14L)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'E14' && $value['PressSide'] === 'R') {
                                $codeE14R .= $value['CuringCode'].",";
                                $topE14R .= $value['rate12'].",";
                                $q1_textE14R .= $value['Q1'].",";
                                $q2_textE14R .= $value['Q2'].",";
                                $q3_textE14R .= $value['Q3'].",";
                                $q4_textE14R .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeE14R, ",");
                ?>
            </td>
            <td>
                <?php
                    $topE14R = trim($topE14R, ",");
                    $topE14R = explode(",", $topE14R);
                    echo $topE14R = $topE14R[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textE14R = trim($q1_textE14R, ",");
                    echo $q1_textE14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textE14R = trim($q2_textE14R, ",");
                    echo $q2_textE14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textE14R = trim($q3_textE14R, ",");
                    echo $q3_textE14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textE14R = trim($q4_textE14R, ",");
                    echo $q4_textE14R; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsE14R=array($q1_textE14R,$q2_textE14R,$q3_textE14R,$q4_textE14R);
                    if (array_sum($rowsE14R)!=0) {
                        echo $rowsE14R_new = array_sum($rowsE14R);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsE14R_new)) {
                        $newrate = ($rowsE14R_new/$topE14R)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
            </td>
            <td colspan="5"><b><center>จำนวนพนักงาน</center></b></td>
            <td colspan="4"><b><center>สรุปการผลิต</center></b></td>
        </tr>
        <tr>
            <td rowspan="2">F13</td>
            <td>
                L
            </td>
            <td>
                <?php
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'F13' && $value['PressSide'] === 'L') {
                                $codeF13L .= $value['CuringCode'].",";
                                $topF13L .= $value['rate12'].",";
                                $q1_textF13L .= $value['Q1'].",";
                                $q2_textF13L .= $value['Q2'].",";
                                $q3_textF13L .= $value['Q3'].",";
                                $q4_textF13L .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeF13L, ",");
                ?>
            </td>
            <td>
                <?php
                    $topF13L = trim($topF13L, ",");
                    $topF13L = explode(",", $topF13L);
                    echo $topF13L = $topF13L[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textF13L = trim($q1_textF13L, ",");
                    echo $q1_textF13L; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textF13L = trim($q2_textF13L, ",");
                    echo $q2_textF13L; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textF13L = trim($q3_textF13L, ",");
                    echo $q3_textF13L; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textF13L = trim($q4_textF13L, ",");
                    echo $q4_textF13L; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsF13L=array($q1_textF13L,$q2_textF13L,$q3_textF13L,$q4_textF13L);
                    if (array_sum($rowsF13L)!=0) {
                        echo $rowsF13L_new = array_sum($rowsF13L);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsF13L_new)) {
                        $newrate = ($rowsF13L_new/$topF13L)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'F13' && $value['PressSide'] === 'R') {
                                $codeF13R .= $value['CuringCode'].",";
                                $topF13R .= $value['rate12'].",";
                                $q1_textF13R .= $value['Q1'].",";
                                $q2_textF13R .= $value['Q2'].",";
                                $q3_textF13R .= $value['Q3'].",";
                                $q4_textF13R .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeF13R, ",");
                ?>
            </td>
            <td>
                <?php
                    $topF13R = trim($topF13R, ",");
                    $topF13R = explode(",", $topF13R);
                    echo $topF13R = $topF13R[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textF13R = trim($q1_textF13R, ",");
                    echo $q1_textF13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textF13R = trim($q2_textF13R, ",");
                    echo $q2_textF13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textF13R = trim($q3_textF13R, ",");
                    echo $q3_textF13R; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textF13R = trim($q4_textF13R, ",");
                    echo $q4_textF13R; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsF13R=array($q1_textF13R,$q2_textF13R,$q3_textF13R,$q4_textF13R);
                    if (array_sum($rowsF13R)!=0) {
                        echo $rowsF13R_new = array_sum($rowsF13R);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsF13R_new)) {
                        $newrate = ($rowsF13R_new/$topF13R)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td rowspan="2">F14</td>
            <td>
                L
            </td>
            <td>
                <?php
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'F14' && $value['PressSide'] === 'L') {
                                $codeF14L .= $value['CuringCode'].",";
                                $topF14L .= $value['rate12'].",";
                                $q1_textF14L .= $value['Q1'].",";
                                $q2_textF14L .= $value['Q2'].",";
                                $q3_textF14L .= $value['Q3'].",";
                                $q4_textF14L .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeF14L, ",");
                ?>
            </td>
            <td>
                <?php
                    $topF14L = trim($topF14L, ",");
                    $topF14L = explode(",", $topF14L);
                    echo $topF14L = $topF14L[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textF14L = trim($q1_textF14L, ",");
                    echo $q1_textF14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textF14L = trim($q2_textF14L, ",");
                    echo $q2_textF14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textF14L = trim($q3_textF14L, ",");
                    echo $q3_textF14L; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textF14L = trim($q4_textF14L, ",");
                    echo $q4_textF14L; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsF14L=array($q1_textF14L,$q2_textF14L,$q3_textF14L,$q4_textF14L);
                    if (array_sum($rowsF14L)!=0) {
                        echo $rowsF14L_new = array_sum($rowsF14L);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsF14L_new)) {
                        $newrate = ($rowsF14L_new/$topF14L)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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
                        foreach ($dataDummy as $key => $value) {
                            if ($value['PressNo'] === 'F14' && $value['PressSide'] === 'R') {
                                $codeF14R .= $value['CuringCode'].",";
                                $topF14R .= $value['rate12'].",";
                                $q1_textF14R .= $value['Q1'].",";
                                $q2_textF14R .= $value['Q2'].",";
                                $q3_textF14R .= $value['Q3'].",";
                                $q4_textF14R .= $value['Q4'].",";
                            }
                        }
                    echo trim($codeF14R, ",");
                ?>
            </td>
            <td>
                <?php
                    $topF14R = trim($topF14R, ",");
                    $topF14R = explode(",", $topF14R);
                    echo $topF14R = $topF14R[0];
                ?>
            </td>
            <td>
                <?php 
                    $q1_textF14R = trim($q1_textF14R, ",");
                    echo $q1_textF14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q2_textF14R = trim($q2_textF14R, ",");
                    echo $q2_textF14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q3_textF14R = trim($q3_textF14R, ",");
                    echo $q3_textF14R; 
                ?>
            </td>
            <td>
                <?php 
                    $q4_textF14R = trim($q4_textF14R, ",");
                    echo $q4_textF14R; 
                ?>
            </td>
            <td>
                <?php 
                    $rowsF14R=array($q1_textF14R,$q2_textF14R,$q3_textF14R,$q4_textF14R);
                    if (array_sum($rowsF14R)!=0) {
                        echo $rowsF14R_new = array_sum($rowsF14R);
                    }
                ?>
            </td>
            <td>
                <?php 
                    if (isset($rowsF14R_new)) {
                        $newrate = ($rowsF14R_new/$topF14R)*100;
                        if (!is_infinite($newrate)) 
                        echo $newrate_format_number = number_format($newrate, 2, '.', '');
                    }
                ?>
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
            <th rowspan="3" colspan="3"  class="f10">TOTAL</th>
            <td rowspan="3"></td>
            <td rowspan="3">
                <?php $sumtop=array($topA13L,$topA13R,$topA14L,$topA14R,$topB13L,$topB13R,$topB14L,$topB14R,$topC13L,$topC13R,$topC14L,$topC14R,$topD13L,$topD13R,$topD14L,$topD14R,$topE13L,$topE13R,$topE14L,$topE14R,$topF13L,$topF13R,$topF14L,$topF14R);
                    if (array_sum($sumtop)!=0) {
                        echo $sumtop = array_sum($sumtop);
                    }
                ?>
            </td>
            <td rowspan="3">
                <?php $sumq1=array($q1_textA13L,$q1_textA13R,$q1_textA14L,$q1_textA14R,$q1_textB13L,$q1_textB13R,$q1_textB14L,$q1_textB14R,$q1_textC13L,$q1_textC13R,$q1_textC14L,$q1_textC14R,$q1_textD13L,$q1_textD13R,$q1_textD14L,$q1_textD14R,$q1_textE13L,$q1_textE13R,$q1_textE14L,$q1_textE14R,$q1_textF13L,$q1_textF13R,$q1_textF14L,$q1_textF14R);
                    if (array_sum($sumq1)!=0) {
                        echo $sumq1 = array_sum($sumq1);
                    }
                ?>
            </td>
            <td rowspan="3">
                <?php $sumq2=array($q2_textA13L,$q2_textA13R,$q2_textA14L,$q2_textA14R,$q2_textB13L,$q2_textB13R,$q2_textB14L,$q2_textB14R,$q2_textC13L,$q2_textC13R,$q2_textC14L,$q2_textC14R,$q2_textD13L,$q2_textD13R,$q2_textD14L,$q2_textD14R,$q2_textE13L,$q2_textE13R,$q2_textE14L,$q2_textE14R,$q2_textF13L,$q2_textF13R,$q2_textF14L,$q2_textF14R);
                    if (array_sum($sumq2)!=0) {
                        echo $sumq2 = array_sum($sumq2);
                    }
                ?>
            </td>
            <td rowspan="3">
                <?php $sumq3=array($q3_textA13L,$q3_textA13R,$q3_textA14L,$q3_textA14R,$q3_textB13L,$q3_textB13R,$q3_textB14L,$q3_textB14R,$q3_textC13L,$q3_textC13R,$q3_textC14L,$q3_textC14R,$q3_textD13L,$q3_textD13R,$q3_textD14L,$q3_textD14R,$q3_textE13L,$q3_textE13R,$q3_textE14L,$q3_textE14R,$q3_textF13L,$q3_textF13R,$q3_textF14L,$q3_textF14R);
                    if (array_sum($sumq3)!=0) {
                        echo $sumq3 = array_sum($sumq3);
                    }
                ?>
            </td>
            <td rowspan="3">
                <?php $sumq4=array($q4_textA13L,$q4_textA13R,$q4_textA14L,$q4_textA14R,$q4_textB13L,$q4_textB13R,$q4_textB14L,$q4_textB14R,$q4_textC13L,$q4_textC13R,$q4_textC14L,$q4_textC14R,$q4_textD13L,$q4_textD13R,$q4_textD14L,$q4_textD14R,$q4_textE13L,$q4_textE13R,$q4_textE14L,$q4_textE14R,$q4_textF13L,$q4_textF13R,$q4_textF14L,$q4_textF14R);
                    if (array_sum($sumq4)!=0) {
                        echo $sumq4 = array_sum($sumq4);
                    }
                ?>
            </td>
            <td rowspan="3">
                <?php
                    // foreach ($datajson as $value) {
                    // $sum = 0;
                    // $rows = array($value->Q1,$value->Q2,$value->Q3,$value->Q4);
                    // $QQ = array_sum($rows);
                    // $sumrows += $QQ;
                    // }
                    // if ($sumrows!=0) {
                    //     echo $sumrows;
                    // }
                    $sum_all=array($rowsA13L_new,$rowsA13R_new,$rowsA14L_new,$rowsA14R_new,$rowsB13L_new,$rowsB13R_new,$rowsB14L_new,$rowsB14R_new,$rowsC13L_new,$rowsC13R_new,$rowsC14L_new,$rowsC14R_new,$rowsD13L_new,$rowsD13R_new,$rowsD14L_new,$rowsD14R_new,$rowsE13L_new,$rowsE13R_new,$rowsE14L_new,$rowsE14R_new,$rowsF13L_new,$rowsF13R_new,$rowsF14L_new,$rowsF14R_new);
                    // $sum_all=array($sumq1,$sumq2,$sumq3,$sumq4);
                    if (array_sum($sum_all)!=0) {
                        echo $sumall_total = array_sum($sum_all);
                    }
                ?>
            </td>
            <td rowspan="3">
                <?php $sumq_all=array($sumq1,$sumq2,$sumq3,$sumq4);
                    if (array_sum($sumq_all)!=0) {
                        $sumq_all = array_sum($sumq_all);
                        if(array_sum($sumtop) == '0' || $sumtop == 0){
                            echo "";
                        }else{
                            $sumper = ($sumq_all/$sumtop)*100;
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

<?php } ?>
</div>
</body>
</html>

<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4-L', 0, '', 3, 3, 3, 3);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
