<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Curing Report</title>
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
        padding: 5.5px;
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
<?php  if (isset($press1) && isset($press2) && isset($press3)){ ?>
    <table >
        <tr>
            <td colspan="5">
                <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
                style="padding-left:10px;height:55px; width:auto;" /></a>
            </td>
            <td align="center" colspan="12" class="f12">
                <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>CURING REPORT</b>
            </td>
        </tr>
        <tr>
            <td rowspan="2" text-rotate="90" class="f10"><b>รายชื่อ</b></td>
            <td colspan="16" class="f10"><br>
            <b>DATE : <?php echo $datecuring; ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>SHIFT : <?php if($shift=="day"){ echo "กลางวัน"; }else{ echo "กลางคืน";} ?></b>
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
            <td width="5%"><br><b>Cure code</b></td>
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
            <td width="7%"><br><b>Press</b></td>
            <td width="5%"><br><b>TimeOn</b></td>
            <td width="5%"><br><b>TimeOff</b></td>
            <td width="5%"><br><b>TotalTime</b></td>
            <td><br><b>Causes of down time</b></td>
        </tr>

        <tr text-rotate="90">
            <td rowspan="9" text-rotate="90" class="f10">
                <?php
                // $ids=array();
                // foreach ($dataname1 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
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
                                $code01L .= $value['CuringCode'].",";
                                $top01L .= $value['rate12'].",";
                                $q1_text01L .= $value['Q1'].",";
                                $q2_text01L .= $value['Q2'].",";
                                $q3_text01L .= $value['Q3'].",";
                                $q4_text01L .= $value['Q4'].",";
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
            <td><?php $rows1=array($qty11,$qty21,$qty31,$qty41);
                if (array_sum($rows1)!=0) {
                    echo $rows1_new = array_sum($rows1);
                }?>
            </td>
            <td>
            <?php if (isset($rows1_new)) {
            $newrate = ($rows1_new/$top01L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code01R .= $value['CuringCode'].",";
                                $top01R .= $value['rate12'].",";
                                $q1_text01R .= $value['Q1'].",";
                                $q2_text01R .= $value['Q2'].",";
                                $q3_text01R .= $value['Q3'].",";
                                $q4_text01R .= $value['Q4'].",";
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
            <td><?php $rows2=array($qty12,$qty22,$qty32,$qty42);
                if (array_sum($rows2)!=0) {
                    echo $rows2_new = array_sum($rows2);
                }?>
            </td>
            <td>
            <?php if (isset($rows2_new)) {
            $newrate = ($rows2_new/$top01R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code02L .= $value['CuringCode'].",";
                                $top02L .= $value['rate12'].",";
                                $q1_text02L .= $value['Q1'].",";
                                $q2_text02L .= $value['Q2'].",";
                                $q3_text02L .= $value['Q3'].",";
                                $q4_text02L .= $value['Q4'].",";
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
            <td><?php $rows3=array($qty13,$qty23,$qty33,$qty43);
                if (array_sum($rows3)!=0) {
                    echo $rows3_new = array_sum($rows3);
                }?>
            </td>
            <td>
            <?php if (isset($rows3_new)) {
            $newrate = ($rows3_new/$top02L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code02R .= $value['CuringCode'].",";
                                $top02R .= $value['rate12'].",";
                                $q1_text02R .= $value['Q1'].",";
                                $q2_text02R .= $value['Q2'].",";
                                $q3_text02R .= $value['Q3'].",";
                                $q4_text02R .= $value['Q4'].",";
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
            <td><?php $rows4=array($qty14,$qty24,$qty34,$qty44);
                if (array_sum($rows4)!=0) {
                    echo $rows4_new = array_sum($rows4);
                }?>
            </td>
            <td>
            <?php if (isset($rows4_new)) {
            $newrate = ($rows4_new/$top02R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code03L .= $value['CuringCode'].",";
                                $top03L .= $value['rate12'].",";
                                $q1_text03L .= $value['Q1'].",";
                                $q2_text03L .= $value['Q2'].",";
                                $q3_text03L .= $value['Q3'].",";
                                $q4_text03L .= $value['Q4'].",";
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
            <td><?php $rows5=array($qty15,$qty25,$qty35,$qty45);
                if (array_sum($rows5)!=0) {
                    echo $rows5_new = array_sum($rows5);
                }?>
            </td>
            <td>
            <?php if (isset($rows5_new)) {
            $newrate = ($rows5_new/$top03L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code03R .= $value['CuringCode'].",";
                                $top03R .= $value['rate12'].",";
                                $q1_text03R .= $value['Q1'].",";
                                $q2_text03R .= $value['Q2'].",";
                                $q3_text03R .= $value['Q3'].",";
                                $q4_text03R .= $value['Q4'].",";
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
            <td><?php $rows6=array($qty16,$qty26,$qty36,$qty46);
                if (array_sum($rows6)!=0) {
                    echo $rows6_new = array_sum($rows6);
                }?>
            </td>
            <td>
            <?php if (isset($rows6_new)) {
            $newrate = ($rows6_new/$top03R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code04L .= $value['CuringCode'].",";
                                $top04L .= $value['rate12'].",";
                                $q1_text04L .= $value['Q1'].",";
                                $q2_text04L .= $value['Q2'].",";
                                $q3_text04L .= $value['Q3'].",";
                                $q4_text04L .= $value['Q4'].",";
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
            <td><?php $rows7=array($qty17,$qty27,$qty37,$qty47);
                if (array_sum($rows7)!=0) {
                    echo $rows7_new = array_sum($rows7);
                }?>
            </td>
            <td>
            <?php if (isset($rows7_new)) {
            $newrate = ($rows7_new/$top04L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code04R .= $value['CuringCode'].",";
                                $top04R .= $value['rate12'].",";
                                $q1_text04R .= $value['Q1'].",";
                                $q2_text04R .= $value['Q2'].",";
                                $q3_text04R .= $value['Q3'].",";
                                $q4_text04R .= $value['Q4'].",";
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
            <td><?php $rows8=array($qty18,$qty28,$qty38,$qty48);
                if (array_sum($rows8)!=0) {
                    echo $rows8_new = array_sum($rows8);
                }?>
            </td>
            <td>
            <?php if (isset($rows8_new)) {
            $newrate = ($rows8_new/$top04R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                // $ids=array();
                // foreach ($dataname2 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
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
                                $code05L .= $value['CuringCode'].",";
                                $top05L .= $value['rate12'].",";
                                $q1_text05L .= $value['Q1'].",";
                                $q2_text05L .= $value['Q2'].",";
                                $q3_text05L .= $value['Q3'].",";
                                $q4_text05L .= $value['Q4'].",";
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
            <td><?php $rows9=array($qty19,$qty29,$qty39,$qty49);
                if (array_sum($rows9)!=0) {
                    echo $rows9_new = array_sum($rows9);
                }?>
            </td>
            <td>
            <?php if (isset($rows9_new)) {
            $newrate = ($rows9_new/$top05L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code05R .= $value['CuringCode'].",";
                                $top05R .= $value['rate12'].",";
                                $q1_text05R .= $value['Q1'].",";
                                $q2_text05R .= $value['Q2'].",";
                                $q3_text05R .= $value['Q3'].",";
                                $q4_text05R .= $value['Q4'].",";
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
            <td><?php $rows10=array($qty110,$qty210,$qty310,$qty410);
                if (array_sum($rows10)!=0) {
                    echo $rows10_new = array_sum($rows10);
                }?>
            </td>
            <td>
            <?php if (isset($rows10_new)) {
            $newrate = ($rows10_new/$top05R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code06L .= $value['CuringCode'].",";
                                $top06L .= $value['rate12'].",";
                                $q1_text06L .= $value['Q1'].",";
                                $q2_text06L .= $value['Q2'].",";
                                $q3_text06L .= $value['Q3'].",";
                                $q4_text06L .= $value['Q4'].",";
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
            <td><?php $rows11=array($qty111,$qty211,$qty311,$qty411);
                if (array_sum($rows11)!=0) {
                    echo $rows11_new = array_sum($rows11);
                }?>
            </td>
            <td>
            <?php if (isset($rows11_new)) {
            $newrate = ($rows11_new/$top06L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code06R .= $value['CuringCode'].",";
                                $top06R .= $value['rate12'].",";
                                $q1_text06R .= $value['Q1'].",";
                                $q2_text06R .= $value['Q2'].",";
                                $q3_text06R .= $value['Q3'].",";
                                $q4_text06R .= $value['Q4'].",";
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
            <td><?php $rows12=array($qty112,$qty212,$qty312,$qty412);
                if (array_sum($rows12)!=0) {
                    echo $rows12_new = array_sum($rows12);
                }?>
            </td>
            <td>
            <?php if (isset($rows12_new)) {
            $newrate = ($rows12_new/$top06R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code07L .= $value['CuringCode'].",";
                                $top07L .= $value['rate12'].",";
                                $q1_text07L .= $value['Q1'].",";
                                $q2_text07L .= $value['Q2'].",";
                                $q3_text07L .= $value['Q3'].",";
                                $q4_text07L .= $value['Q4'].",";
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
            <td><?php $rows13=array($qty113,$qty213,$qty313,$qty413);
                if (array_sum($rows13)!=0) {
                    echo $rows13_new = array_sum($rows13);
                }?>
            </td>
            <td>
            <?php if (isset($rows13_new)) {
            $newrate = ($rows13_new/$top07L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code07R .= $value['CuringCode'].",";
                                $top07R .= $value['rate12'].",";
                                $q1_text07R .= $value['Q1'].",";
                                $q2_text07R .= $value['Q2'].",";
                                $q3_text07R .= $value['Q3'].",";
                                $q4_text07R .= $value['Q4'].",";
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
            <td><?php $rows14=array($qty114,$qty214,$qty314,$qty414);
                if (array_sum($rows14)!=0) {
                    echo $rows14_new = array_sum($rows14);
                }?>
            </td>
            <td>
            <?php if (isset($rows14_new)) {
            $newrate = ($rows14_new/$top07R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code08L .= $value['CuringCode'].",";
                                $top08L .= $value['rate12'].",";
                                $q1_text08L .= $value['Q1'].",";
                                $q2_text08L .= $value['Q2'].",";
                                $q3_text08L .= $value['Q3'].",";
                                $q4_text08L .= $value['Q4'].",";
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
            <td><?php $rows15=array($qty115,$qty215,$qty315,$qty415);
                if (array_sum($rows15)!=0) {
                    echo $rows15_new = array_sum($rows15);
                }?>
            </td>
            <td>
            <?php if (isset($rows15_new)) {
            $newrate = ($rows15_new/$top08L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code08R .= $value['CuringCode'].",";
                                $top08R .= $value['rate12'].",";
                                $q1_text08R .= $value['Q1'].",";
                                $q2_text08R .= $value['Q2'].",";
                                $q3_text08R .= $value['Q3'].",";
                                $q4_text08R .= $value['Q4'].",";
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
            <<td><?php $rows16=array($qty116,$qty216,$qty316,$qty416);
                if (array_sum($rows16)!=0) {
                    echo $rows16_new = array_sum($rows16);
                }?>
            </td>
            <td>
            <?php if (isset($rows16_new)) {
            $newrate = ($rows16_new/$top08R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                // $ids=array();
                // foreach ($dataname3 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
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
                                $code09L .= $value['CuringCode'].",";
                                $top09L .= $value['rate12'].",";
                                $q1_text09L .= $value['Q1'].",";
                                $q2_text09L .= $value['Q2'].",";
                                $q3_text09L .= $value['Q3'].",";
                                $q4_text09L .= $value['Q4'].",";
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
            <td><?php $rows17=array($qty117,$qty217,$qty317,$qty417);
                if (array_sum($rows17)!=0) {
                    echo $rows17_new = array_sum($rows17);
                }?>
            </td>
            <td>
            <?php if (isset($rows17_new)) {
            $newrate = ($rows17_new/$top09L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code09R .= $value['CuringCode'].",";
                                $top09R .= $value['rate12'].",";
                                $q1_text09R .= $value['Q1'].",";
                                $q2_text09R .= $value['Q2'].",";
                                $q3_text09R .= $value['Q3'].",";
                                $q4_text09R .= $value['Q4'].",";
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
            <td><?php $rows18=array($qty118,$qty218,$qty318,$qty418);
                if (array_sum($rows18)!=0) {
                    echo $rows18_new = array_sum($rows18);
                }?>
            </td>
            <td>
            <?php if (isset($rows18_new)) {
            $newrate = ($rows18_new/$top09R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        $arr_10_l = [];
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b10 && $value['PressSide'] === 'L') {
                                $arr_10_l = array_unique(array_merge($arr_10_l, $value['CuringCode']));
                                $code10L .= $value['CuringCode'].",";
                                $top10L .= $value['rate12'].",";
                                $q1_text10L .= $value['Q1'].",";
                                $q2_text10L .= $value['Q2'].",";
                                $q3_text10L .= $value['Q3'].",";
                                $q4_text10L .= $value['Q4'].",";
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
            <td><?php $rows19=array($qty119,$qty219,$qty319,$qty419);
                if (array_sum($rows19)!=0) {
                    echo $rows19_new = array_sum($rows19);
                }?>
            </td>
            <td>
            <?php if (isset($rows19_new)) {
            $newrate = ($rows19_new/$top10L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                            if ($value['PressNo'] === $b10 && $value['PressSide'] === 'R') {
                                $code10R .= $value['CuringCode'].",";
                                $top10R .= $value['rate12'].",";
                                $q1_text10R .= $value['Q1'].",";
                                $q2_text10R .= $value['Q2'].",";
                                $q3_text10R .= $value['Q3'].",";
                                $q4_text10R .= $value['Q4'].",";
                            }
                        }
                    echo trim($code10R, ",");
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
            <td><?php $rows20=array($qty1110,$qty2110,$qty3110,$qty4110);
                if (array_sum($rows20)!=0) {
                    echo $rows20_new = array_sum($rows20);
                }?>
            </td>
            <td>
            <?php if (isset($rows20_new)) {
            $newrate = ($rows20_new/$top10R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b11; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b11 && $value['PressSide'] === 'L') {
                                $code11L .= $value['CuringCode'].",";
                                $top11L .= $value['rate12'].",";
                                $q1_text11L .= $value['Q1'].",";
                                $q2_text11L .= $value['Q2'].",";
                                $q3_text11L .= $value['Q3'].",";
                                $q4_text11L .= $value['Q4'].",";
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
            <td><?php $rows21=array($qty1111,$qty2111,$qty3111,$qty4111);
                if (array_sum($rows21)!=0) {
                    echo $rows21_new = array_sum($rows21);
                }?>
            </td>
            <td>
            <?php if (isset($rows21_new)) {
            $newrate = ($rows21_new/$top11L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                            if ($value['PressNo'] === $b11 && $value['PressSide'] === 'R') {
                                $code11R .= $value['CuringCode'].",";
                                $top11R .= $value['rate12'].",";
                                $q1_text11R .= $value['Q1'].",";
                                $q2_text11R .= $value['Q2'].",";
                                $q3_text11R .= $value['Q3'].",";
                                $q4_text11R .= $value['Q4'].",";
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
            <td><?php $rows22=array($qty1112,$qty2112,$qty3112,$qty4112);
                if (array_sum($rows22)!=0) {
                    echo $rows22_new = array_sum($rows22);
                }?>
            </td>
            <td>
            <?php if (isset($rows22_new)) {
            $newrate = ($rows22_new/$top11R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b12; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b12 && $value['PressSide'] === 'L') {
                                $code12L .= $value['CuringCode'].",";
                                $top12L .= $value['rate12'].",";
                                $q1_text12L .= $value['Q1'].",";
                                $q2_text12L .= $value['Q2'].",";
                                $q3_text12L .= $value['Q3'].",";
                                $q4_text12L .= $value['Q4'].",";
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
            <td><?php $rows23=array($qty1113,$qty2113,$qty3113,$qty4113);
                if (array_sum($rows23)!=0) {
                    echo $rows23_new = array_sum($rows23);
                }?>
            </td>
            <td>
            <?php if (isset($rows23_new)) {
            $newrate = ($rows23_new/$top12L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                                $code12R .= $value['CuringCode'].",";
                                $top12R .= $value['rate12'].",";
                                $q1_text12R .= $value['Q1'].",";
                                $q2_text12R .= $value['Q2'].",";
                                $q3_text12R .= $value['Q3'].",";
                                $q4_text12R .= $value['Q4'].",";
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
            <td><?php $rows24=array($qty1114,$qty2114,$qty3114,$qty4114);
                if (array_sum($rows24)!=0) {
                    echo $rows24_new = array_sum($rows24);
                }?>
            </td>
            <td>
            <?php if (isset($rows24_new)) {
            $newrate = ($rows24_new/$top12R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td colspan="2" class="f10"><br>
                <b>Total</b>
            </td>
            <td></td>
            <td></td>
            <td>
            <?php $sumtop=array($top01L,$top01R,$top02L,$top02R,$top03L,$top03R,$top04L,$top04R,$top05L,$top05R,$top06L,$top06R,$top07L,$top07R,$top08L,$top08R,$top09L,$top09R,$top10L,$top10R,$top11L,$top11R,$top12L,$top12R);
                if (array_sum($sumtop)!=0) {
                    echo $sumtop = array_sum($sumtop);
                }
            ?>
            </td>
            <td>
            <?php $sumq1=array($qty11,$qty12,$qty13,$qty14,$qty15,$qty16,$qty17,$qty18,$qty19,$qty110,$qty111,$qty112,$qty113,$qty114,$qty115,$qty116,$qty117,$qty118,$qty119,$qty1110,$qty1111,$qty1112,$qty1113,$qty1114);
                if (array_sum($sumq1)!=0) {
                    echo $sumq1 = array_sum($sumq1);
                }
            ?>
            </td>
            <td>
            <?php $sumq2=array($qty21,$qty22,$qty23,$qty24,$qty25,$qty26,$qty27,$qty28,$qty29,$qty210,$qty211,$qty212,$qty213,$qty214,$qty215,$qty216,$qty217,$qty218,$qty219,$qty2110,$qty2111,$qty2112,$qty2113,$qty2114);
                if (array_sum($sumq2)!=0) {
                    echo $sumq2 = array_sum($sumq2);
                }
            ?>
            </td>
            <td>
            <?php $sumq3=array($qty31,$qty32,$qty33,$qty34,$qty35,$qty36,$qty37,$qty38,$qty39,$qty310,$qty311,$qty312,$qty313,$qty314,$qty315,$qty316,$qty317,$qty318,$qty319,$qty3110,$qty3111,$qty3112,$qty3113,$qty3114);
                if (array_sum($sumq3)!=0) {
                    echo $sumq3 = array_sum($sumq3);
                }
            ?>
            </td>
            <td>
            <?php $sumq4=array($qty41,$qty42,$qty43,$qty44,$qty45,$qty46,$qty47,$qty48,$qty49,$qty410,$qty411,$qty412,$qty413,$qty414,$qty415,$qty416,$qty417,$qty418,$qty419,$qty4110,$qty4111,$qty4112,$qty4113,$qty4114);
                if (array_sum($sumq4)!=0) {
                    echo $sumq4 = array_sum($sumq4);
                }
            ?>
            </td>
            <td>
            <?php
                foreach ($datajson as $value) {
                $rows1 = array($value->Q1,$value->Q2,$value->Q3,$value->Q4);
                $QQ1 = array_sum($rows1);
                $sumrows1 += $QQ1;
                }
                if ($sumrows1!=0) {
                    echo $sumrows1;
                }
            ?>
            </td>
            <td>
            <?php $sumq_all=array($sumq1,$sumq2,$sumq3,$sumq4);
                if (array_sum($sumq_all)!=0) {
                    $sumq_all = array_sum($sumq_all);
                    if($sumtop == "" || $sumtop == 0)
                    {
                      $sumper = 0;
                    }
                    else {
                    $sumper = ($sumq_all/$sumtop)*100;

                    }
                    echo $sumper_format_number = number_format($sumper, 2, '.', '');
                }
            ?>
            </td>
            <td class="f10"><br><b>สรุปการผลิต</b></td>
            <td colspan="4" align="left" class="f10">
                <br><b>Cure : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            if ($sumrows1!=0) {
                    echo $sumrows1;
                }
            ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;เส้น</b>
            </td>
        </tr>
        <tr>
            <td colspan="17" class="f10" valign="bottom"><br><br><b>ผู้ตรวจสอบ : ......................................................................
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            ผู้อนุมัติ : ......................................................................</b></td>
        </tr>
    </table>
    <table class="table">
        <tr class="tr">
            <td class="td" align="left">
                Ref.WI-PP-2.12
            </td>
            <td class="td" align="right">
                FM-PP-2.12.3,Issue #1
            </td>
        </tr>
    </table>
    <table >
        <tr>
            <td colspan="5">
                <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
                style="padding-left:10px;height:55px; width:auto;" /></a>
            </td>
            <td align="center" colspan="12" class="f12">
                <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>CURING REPORT</b>
            </td>
        </tr>
        <tr>
            <td rowspan="2" text-rotate="90" class="f10"><b>รายชื่อ</b></td>
            <td colspan="16" class="f10"><br>
            <b>DATE : <?php echo $datecuring; ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>SHIFT : <?php if($shift=="day"){ echo "กลางวัน"; }else{ echo "กลางคืน";} ?></b>
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
            <td  width="5%"><br><b>Cure code</b></td>
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
            <td width="3%"><br><b>%</b></td>
            <td width="7%"><br><b>Press</b></td>
            <td width="5%"><br><b>TimeOn</b></td>
            <td width="5%"><br><b>TimeOff</b></td>
            <td width="5%"><br><b>TotalTime</b></td>
            <td><br><b>Causes of down time</b></td>
        </tr>

        <tr text-rotate="90">
            <td rowspan="9" text-rotate="90" class="f10">
                <?php
                // $ids=array();
                // foreach ($dataname_sec1 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
                ?>
            </td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b01_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b01_sec && $value['PressSide'] === 'L') {
                                $code01L_sec .= $value['CuringCode'].",";
                                $top01L_sec .= $value['rate12'].",";
                                $q1_text01L_sec .= $value['Q1'].",";
                                $q2_text01L_sec .= $value['Q2'].",";
                                $q3_text01L_sec .= $value['Q3'].",";
                                $q4_text01L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code01L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top01L_sec = trim($top01L_sec, ",");
                $top01L_sec = explode(",", $top01L_sec);
                echo $top01L_sec = $top01L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text01L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text01L_sec, ","); ?></td>
            <td><?php echo trim($q3_text01L_sec, ","); ?></td>
            <td><?php echo trim($q4_text01L_sec, ","); ?></td>
            <td><?php $rows1_sec=array($qty11_sec,$qty21_sec,$qty31_sec,$qty41_sec);
                if (array_sum($rows1_sec)!=0) {
                    echo $rows1_sec_new = array_sum($rows1_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows1_sec_new)) {
                $newrate = ($rows1_sec_new/$top01L_sec)*100;
                echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b01_sec && $value['PressSide'] === 'R') {
                                $code01R_sec .= $value['CuringCode'].",";
                                $top01R_sec .= $value['rate12'].",";
                                $q1_text01R_sec .= $value['Q1'].",";
                                $q2_text01R_sec .= $value['Q2'].",";
                                $q3_text01R_sec .= $value['Q3'].",";
                                $q4_text01R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code01R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top01R_sec = trim($top01R_sec, ",");
                $top01R_sec = explode(",", $top01R_sec);
                echo $top01R_sec = $top01R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text01R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text01R_sec, ","); ?></td>
            <td><?php echo trim($q3_text01R_sec, ","); ?></td>
            <td><?php echo trim($q4_text01R_sec, ","); ?></td>
            <td><?php $rows2_sec=array($qty12_sec,$qty22_sec,$qty32_sec,$qty42_sec);
                if (array_sum($rows2_sec)!=0) {
                    echo $rows2_sec_new = array_sum($rows2_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows2_sec_new)) {
                $newrate = ($rows2_sec_new/$top01R_sec)*100;
                echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b02_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b02_sec && $value['PressSide'] === 'L') {
                                $code02L_sec .= $value['CuringCode'].",";
                                $top02L_sec .= $value['rate12'].",";
                                $q1_text02L_sec .= $value['Q1'].",";
                                $q2_text02L_sec .= $value['Q2'].",";
                                $q3_text02L_sec .= $value['Q3'].",";
                                $q4_text02L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code02L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top02L_sec = trim($top02L_sec, ",");
                $top02L_sec = explode(",", $top02L_sec);
                echo $top02L_sec = $top02L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text02L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text02L_sec, ","); ?></td>
            <td><?php echo trim($q3_text02L_sec, ","); ?></td>
            <td><?php echo trim($q4_text02L_sec, ","); ?></td>
            <td><?php $rows3_sec=array($qty13_sec,$qty23_sec,$qty33_sec,$qty43_sec);
                if (array_sum($rows3_sec)!=0) {
                    echo $rows3_sec_new = array_sum($rows3_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows3_sec_new)) {
            $newrate = ($rows3_sec_new/$top02L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b02_sec && $value['PressSide'] === 'R') {
                                $code02R_sec .= $value['CuringCode'].",";
                                $top02R_sec .= $value['rate12'].",";
                                $q1_text02R_sec .= $value['Q1'].",";
                                $q2_text02R_sec .= $value['Q2'].",";
                                $q3_text02R_sec .= $value['Q3'].",";
                                $q4_text02R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code02R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top02R_sec = trim($top02R_sec, ",");
                $top02R_sec = explode(",", $top02R_sec);
                echo $top02R_sec = $top02R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text02R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text02R_sec, ","); ?></td>
            <td><?php echo trim($q3_text02R_sec, ","); ?></td>
            <td><?php echo trim($q4_text02R_sec, ","); ?></td>
            <td><?php $rows4_sec=array($qty14_sec,$qty24_sec,$qty34_sec,$qty44_sec);
                if (array_sum($rows4_sec)!=0) {
                    echo $rows4_sec_new =  array_sum($rows4_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows4_sec_new)) {
            $newrate = ($rows4_sec_new/$top02R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b03_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b03_sec && $value['PressSide'] === 'L') {
                                $code03L_sec .= $value['CuringCode'].",";
                                $top03L_sec .= $value['rate12'].",";
                                $q1_text03L_sec .= $value['Q1'].",";
                                $q2_text03L_sec .= $value['Q2'].",";
                                $q3_text03L_sec .= $value['Q3'].",";
                                $q4_text03L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code03L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top03L_sec = trim($top03L_sec, ",");
                $top03L_sec = explode(",", $top03L_sec);
                echo $top03L_sec = $top03L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text03L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text03L_sec, ","); ?></td>
            <td><?php echo trim($q3_text03L_sec, ","); ?></td>
            <td><?php echo trim($q4_text03L_sec, ","); ?></td>
           <td><?php $rows5_sec=array($qty15_sec,$qty25_sec,$qty35_sec,$qty45_sec);
                if (array_sum($rows5_sec)!=0) {
                    echo $rows5_sec_new =  array_sum($rows5_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows5_sec_new)) {
            $newrate = ($rows5_sec_new/$top03L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b03_sec && $value['PressSide'] === 'R') {
                                $code03R_sec .= $value['CuringCode'].",";
                                $top03R_sec .= $value['rate12'].",";
                                $q1_text03R_sec .= $value['Q1'].",";
                                $q2_text03R_sec .= $value['Q2'].",";
                                $q3_text03R_sec .= $value['Q3'].",";
                                $q4_text03R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code03R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top03R_sec = trim($top03R_sec, ",");
                $top03R_sec = explode(",", $top03R_sec);
                echo $top03R_sec = $top03R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text03R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text03R_sec, ","); ?></td>
            <td><?php echo trim($q3_text03R_sec, ","); ?></td>
            <td><?php echo trim($q4_text03R_sec, ","); ?></td>
            <td><?php $rows6_sec=array($qty16_sec,$qty26_sec,$qty36_sec,$qty46_sec);
                if (array_sum($rows6_sec)!=0) {
                    echo $rows6_sec_new =  array_sum($rows6_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows6_sec_new)) {
            $newrate = ($rows6_sec_new/$top03R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b04_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b04_sec && $value['PressSide'] === 'L') {
                                $code04L_sec .= $value['CuringCode'].",";
                                $top04L_sec .= $value['rate12'].",";
                                $q1_text04L_sec .= $value['Q1'].",";
                                $q2_text04L_sec .= $value['Q2'].",";
                                $q3_text04L_sec .= $value['Q3'].",";
                                $q4_text04L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code04L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top04L_sec = trim($top04L_sec, ",");
                $top04L_sec = explode(",", $top04L_sec);
                echo $top04L_sec = $top04L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text04L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text04L_sec, ","); ?></td>
            <td><?php echo trim($q3_text04L_sec, ","); ?></td>
            <td><?php echo trim($q4_text04L_sec, ","); ?></td>
            <td><?php $rows7_sec=array($qty17_sec,$qty27_sec,$qty37_sec,$qty47_sec);
                if (array_sum($rows7_sec)!=0) {
                    echo $rows7_sec_new =  array_sum($rows7_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows7_sec_new)) {
            $newrate = ($rows7_sec_new/$top04L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b04_sec && $value['PressSide'] === 'R') {
                                $code04R_sec .= $value['CuringCode'].",";
                                $top04R_sec .= $value['rate12'].",";
                                $q1_text04R_sec .= $value['Q1'].",";
                                $q2_text04R_sec .= $value['Q2'].",";
                                $q3_text04R_sec .= $value['Q3'].",";
                                $q4_text04R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code04R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top04R_sec = trim($top04R_sec, ",");
                $top04R_sec = explode(",", $top04R_sec);
                echo $top04R_sec = $top04R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text04R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text04R_sec, ","); ?></td>
            <td><?php echo trim($q3_text04R_sec, ","); ?></td>
            <td><?php echo trim($q4_text04R_sec, ","); ?></td>
            <td><?php $rows8_sec=array($qty18_sec,$qty28_sec,$qty38_sec,$qty48_sec);
                if (array_sum($rows8_sec)!=0) {
                    echo $rows8_sec_new =  array_sum($rows8_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows8_sec_new)) {
            $newrate = ($rows8_sec_new/$top04R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                // $ids=array();
                // foreach ($dataname_sec2 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
                ?>
            </td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b05_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b05_sec && $value['PressSide'] === 'L') {
                                $code05L_sec .= $value['CuringCode'].",";
                                $top05L_sec .= $value['rate12'].",";
                                $q1_text05L_sec .= $value['Q1'].",";
                                $q2_text05L_sec .= $value['Q2'].",";
                                $q3_text05L_sec .= $value['Q3'].",";
                                $q4_text05L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code05L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top05L_sec = trim($top05L_sec, ",");
                $top05L_sec = explode(",", $top05L_sec);
                echo $top05L_sec = $top05L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text05L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text05L_sec, ","); ?></td>
            <td><?php echo trim($q3_text05L_sec, ","); ?></td>
            <td><?php echo trim($q4_text05L_sec, ","); ?></td>
            <td><?php $rows9_sec=array($qty19_sec,$qty29_sec,$qty39_sec,$qty49_sec);
                if (array_sum($rows9_sec)!=0) {
                    echo $rows9_sec_new =  array_sum($rows9_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows9_sec_new)) {
            $newrate = ($rows9_sec_new/$top05L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b05_sec && $value['PressSide'] === 'R') {
                                $code05R_sec .= $value['CuringCode'].",";
                                $top05R_sec .= $value['rate12'].",";
                                $q1_text05R_sec .= $value['Q1'].",";
                                $q2_text05R_sec .= $value['Q2'].",";
                                $q3_text05R_sec .= $value['Q3'].",";
                                $q4_text05R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code05R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top05R_sec = trim($top05R_sec, ",");
                $top05R_sec = explode(",", $top05R_sec);
                echo $top05R_sec = $top05R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text05R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text05R_sec, ","); ?></td>
            <td><?php echo trim($q3_text05R_sec, ","); ?></td>
            <td><?php echo trim($q4_text05R_sec, ","); ?></td>
            <td><?php $rows10_sec=array($qty110_sec,$qty210_sec,$qty310_sec,$qty410_sec);
                if (array_sum($rows10_sec)!=0) {
                    echo $rows10_sec_new =  array_sum($rows10_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows10_sec_new)) {
            $newrate = ($rows10_sec_new/$top05R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b06_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b06_sec && $value['PressSide'] === 'L') {
                                $code06L_sec .= $value['CuringCode'].",";
                                $top06L_sec .= $value['rate12'].",";
                                $q1_text06L_sec .= $value['Q1'].",";
                                $q2_text06L_sec .= $value['Q2'].",";
                                $q3_text06L_sec .= $value['Q3'].",";
                                $q4_text06L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code06L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top06L_sec = trim($top06L_sec, ",");
                $top06L_sec = explode(",", $top06L_sec);
                echo $top06L_sec = $top06L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text06L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text06L_sec, ","); ?></td>
            <td><?php echo trim($q3_text06L_sec, ","); ?></td>
            <td><?php echo trim($q4_text06L_sec, ","); ?></td>
            <td><?php $rows11_sec=array($qty111_sec,$qty211_sec,$qty311_sec,$qty411_sec);
                if (array_sum($rows11_sec)!=0) {
                    echo $rows11_sec_new =  array_sum($rows11_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows11_sec_new)) {
            $newrate = ($rows11_sec_new/$top06L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b06_sec && $value['PressSide'] === 'R') {
                                $code06R_sec .= $value['CuringCode'].",";
                                $top06R_sec .= $value['rate12'].",";
                                $q1_text06R_sec .= $value['Q1'].",";
                                $q2_text06R_sec .= $value['Q2'].",";
                                $q3_text06R_sec .= $value['Q3'].",";
                                $q4_text06R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code06R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top06R_sec = trim($top06R_sec, ",");
                $top06R_sec = explode(",", $top06R_sec);
                echo $top06R_sec = $top06R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text06R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text06R_sec, ","); ?></td>
            <td><?php echo trim($q3_text06R_sec, ","); ?></td>
            <td><?php echo trim($q4_text06R_sec, ","); ?></td>
            <td><?php $rows12_sec=array($qty112_sec,$qty212_sec,$qty312_sec,$qty412_sec);
                if (array_sum($rows12_sec)!=0) {
                    echo $rows12_sec_new =  array_sum($rows12_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows12_sec_new)) {
            $newrate = ($rows12_sec_new/$top06R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b07_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b07_sec && $value['PressSide'] === 'L') {
                                $code07L_sec .= $value['CuringCode'].",";
                                $top07L_sec .= $value['rate12'].",";
                                $q1_text07L_sec .= $value['Q1'].",";
                                $q2_text07L_sec .= $value['Q2'].",";
                                $q3_text07L_sec .= $value['Q3'].",";
                                $q4_text07L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code07L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top07L_sec = trim($top07L_sec, ",");
                $top07L_sec = explode(",", $top07L_sec);
                echo $top07L_sec = $top07L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text07L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text07L_sec, ","); ?></td>
            <td><?php echo trim($q3_text07L_sec, ","); ?></td>
            <td><?php echo trim($q4_text07L_sec, ","); ?></td>
            <td><?php $rows13_sec=array($qty113_sec,$qty213_sec,$qty313_sec,$qty413_sec);
                if (array_sum($rows13_sec)!=0) {
                    echo $rows13_sec_new =  array_sum($rows13_sec);
                }?>
            <td>
            <?php if (isset($rows13_sec_new)) {
            $newrate = ($rows13_sec_new/$top07L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b07_sec && $value['PressSide'] === 'R') {
                                $code07R_sec .= $value['CuringCode'].",";
                                $top07R_sec .= $value['rate12'].",";
                                $q1_text07R_sec .= $value['Q1'].",";
                                $q2_text07R_sec .= $value['Q2'].",";
                                $q3_text07R_sec .= $value['Q3'].",";
                                $q4_text07R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code07R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top07R_sec = trim($top07R_sec, ",");
                $top07R_sec = explode(",", $top07R_sec);
                echo $top07R_sec = $top07R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text07R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text07R_sec, ","); ?></td>
            <td><?php echo trim($q3_text07R_sec, ","); ?></td>
            <td><?php echo trim($q4_text07R_sec, ","); ?></td>
            <td><?php $rows14_sec=array($qty114_sec,$qty214_sec,$qty314_sec,$qty414_sec);
                if (array_sum($rows14_sec)!=0) {
                    echo $rows14_sec_new =  array_sum($rows14_sec);
                }?>
            <td>
            <?php if (isset($rows14_sec_new)) {
            $newrate = ($rows14_sec_new/$top07R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b08_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b08_sec && $value['PressSide'] === 'L') {
                                $code08L_sec .= $value['CuringCode'].",";
                                $top08L_sec .= $value['rate12'].",";
                                $q1_text08L_sec .= $value['Q1'].",";
                                $q2_text08L_sec .= $value['Q2'].",";
                                $q3_text08L_sec .= $value['Q3'].",";
                                $q4_text08L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code08L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top08L_sec = trim($top08L_sec, ",");
                $top08L_sec = explode(",", $top08L_sec);
                echo $top08L_sec = $top08L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text08L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text08L_sec, ","); ?></td>
            <td><?php echo trim($q3_text08L_sec, ","); ?></td>
            <td><?php echo trim($q4_text08L_sec, ","); ?></td>
            <td><?php $rows15_sec=array($qty115_sec,$qty215_sec,$qty315_sec,$qty415_sec);
                if (array_sum($rows15_sec)!=0) {
                    echo $rows15_sec_new =  array_sum($rows15_sec);
                }?>
            <td>
            <?php if (isset($rows15_sec_new)) {
            $newrate = ($rows15_sec_new/$top08L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b08_sec && $value['PressSide'] === 'R') {
                                $code08R_sec .= $value['CuringCode'].",";
                                $top08R_sec .= $value['rate12'].",";
                                $q1_text08R_sec .= $value['Q1'].",";
                                $q2_text08R_sec .= $value['Q2'].",";
                                $q3_text08R_sec .= $value['Q3'].",";
                                $q4_text08R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code08R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top08R_sec = trim($top08R_sec, ",");
                $top08R_sec = explode(",", $top08R_sec);
                echo $top08R_sec = $top08R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text08R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text08R_sec, ","); ?></td>
            <td><?php echo trim($q3_text08R_sec, ","); ?></td>
            <td><?php echo trim($q4_text08R_sec, ","); ?></td>
            <<td><?php $rows16_sec=array($qty116_sec,$qty216_sec,$qty316_sec,$qty416_sec);
                if (array_sum($rows16_sec)!=0) {
                    echo $rows16_sec_new =  array_sum($rows16_sec);
                }?>
            <td>
            <?php if (isset($rows16_sec_new)) {
            $newrate = ($rows16_sec_new/$top08R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                // $ids=array();
                // foreach ($dataname_sec3 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
                ?>
            </td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b09_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b09_sec && $value['PressSide'] === 'L') {
                                $code09L_sec .= $value['CuringCode'].",";
                                $top09L_sec .= $value['rate12'].",";
                                $q1_text09L_sec .= $value['Q1'].",";
                                $q2_text09L_sec .= $value['Q2'].",";
                                $q3_text09L_sec .= $value['Q3'].",";
                                $q4_text09L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code09L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top09L_sec = trim($top09L_sec, ",");
                $top09L_sec = explode(",", $top09L_sec);
                echo $top09L_sec = $top09L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text09L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text09L_sec, ","); ?></td>
            <td><?php echo trim($q3_text09L_sec, ","); ?></td>
            <td><?php echo trim($q4_text09L_sec, ","); ?></td>
            <td><?php $rows17_sec=array($qty117_sec,$qty217_sec,$qty317_sec,$qty417_sec);
                if (array_sum($rows17_sec)!=0) {
                    echo $rows17_sec_new =  array_sum($rows17_sec);
                }?>
            <td>
            <?php if (isset($rows17_sec_new)) {
            $newrate = ($rows17_sec_new/$top09L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b09_sec && $value['PressSide'] === 'R') {
                                $code09R_sec .= $value['CuringCode'].",";
                                $top09R_sec .= $value['rate12'].",";
                                $q1_text09R_sec .= $value['Q1'].",";
                                $q2_text09R_sec .= $value['Q2'].",";
                                $q3_text09R_sec .= $value['Q3'].",";
                                $q4_text09R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code09R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top09R_sec = trim($top09R_sec, ",");
                $top09R_sec = explode(",", $top09R_sec);
                echo $top09R_sec = $top09R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text09R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text09R_sec, ","); ?></td>
            <td><?php echo trim($q3_text09R_sec, ","); ?></td>
            <td><?php echo trim($q4_text09R_sec, ","); ?></td>
            <td><?php $rows18_sec=array($qty118_sec,$qty218_sec,$qty318_sec,$qty418_sec);
                if (array_sum($rows18_sec)!=0) {
                    echo $rows18_sec_new =  array_sum($rows18_sec);
                }?>
            <td>
            <?php if (isset($rows18_sec_new)) {
            $newrate = ($rows18_sec_new/$top09R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b10_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b10_sec && $value['PressSide'] === 'L') {
                                $code10L_sec .= $value['CuringCode'].",";
                                $top10L_sec .= $value['rate12'].",";
                                $q1_text10L_sec .= $value['Q1'].",";
                                $q2_text10L_sec .= $value['Q2'].",";
                                $q3_text10L_sec .= $value['Q3'].",";
                                $q4_text10L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code10L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top10L_sec = trim($top10L_sec, ",");
                $top10L_sec = explode(",", $top10L_sec);
                echo $top10L_sec = $top10L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text10L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text10L_sec, ","); ?></td>
            <td><?php echo trim($q3_text10L_sec, ","); ?></td>
            <td><?php echo trim($q4_text10L_sec, ","); ?></td>
            <td><?php $rows19_sec=array($qty119_sec,$qty219_sec,$qty319_sec,$qty419_sec);
                if (array_sum($rows19_sec)!=0) {
                    echo $rows19_sec_new =  array_sum($rows19_sec);
                }?>
            <td>
            <?php if (isset($rows19_sec_new)) {
            $newrate = ($rows19_sec_new/$top10L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b10_sec && $value['PressSide'] === 'R') {
                                $code10R_sec .= $value['CuringCode'].",";
                                $top10R_sec .= $value['rate12'].",";
                                $q1_text10R_sec .= $value['Q1'].",";
                                $q2_text10R_sec .= $value['Q2'].",";
                                $q3_text10R_sec .= $value['Q3'].",";
                                $q4_text10R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code10R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top10R_sec = trim($top10R_sec, ",");
                $top10R_sec = explode(",", $top10R_sec);
                echo $top10R_sec = $top10R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text10R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text10R_sec, ","); ?></td>
            <td><?php echo trim($q3_text10R_sec, ","); ?></td>
            <td><?php echo trim($q4_text10R_sec, ","); ?></td>
            <td><?php $rows20_sec=array($qty1110_sec,$qty2110_sec,$qty3110_sec,$qty4110_sec);
                if (array_sum($rows20_sec)!=0) {
                    echo $rows20_sec_new =  array_sum($rows20_sec);
                }?>
            <td>
            <?php if (isset($rows20_sec_new)) {
            $newrate = ($rows20_sec_new/$top10R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b11_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b11_sec && $value['PressSide'] === 'L') {
                                $code11L_sec .= $value['CuringCode'].",";
                                $top11L_sec .= $value['rate12'].",";
                                $q1_text11L_sec .= $value['Q1'].",";
                                $q2_text11L_sec .= $value['Q2'].",";
                                $q3_text11L_sec .= $value['Q3'].",";
                                $q4_text11L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code11L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top11L_sec = trim($top11L_sec, ",");
                $top11L_sec = explode(",", $top11L_sec);
                echo $top11L_sec = $top11L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text11L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text11L_sec, ","); ?></td>
            <td><?php echo trim($q3_text11L_sec, ","); ?></td>
            <td><?php echo trim($q4_text11L_sec, ","); ?></td>
            <td><?php $rows21_sec=array($qty1111_sec,$qty2111_sec,$qty3111_sec,$qty4111_sec);
                if (array_sum($rows21_sec)!=0) {
                    echo $rows21_sec_new =  array_sum($rows21_sec);
                }?>
            <td>
            <?php if (isset($rows21_sec_new)) {
            $newrate = ($rows21_sec_new/$top11L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b11_sec && $value['PressSide'] === 'R') {
                                $code11R_sec .= $value['CuringCode'].",";
                                $top11R_sec .= $value['rate12'].",";
                                $q1_text11R_sec .= $value['Q1'].",";
                                $q2_text11R_sec .= $value['Q2'].",";
                                $q3_text11R_sec .= $value['Q3'].",";
                                $q4_text11R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code11R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top11R_sec = trim($top11R_sec, ",");
                $top11R_sec = explode(",", $top11R_sec);
                echo $top11R_sec = $top11R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text11R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text11R_sec, ","); ?></td>
            <td><?php echo trim($q3_text11R_sec, ","); ?></td>
            <td><?php echo trim($q4_text11R_sec, ","); ?></td>
            <td><?php $rows22_sec=array($qty1112_sec,$qty2112_sec,$qty3112_sec,$qty4112_sec);
                if (array_sum($rows22_sec)!=0) {
                    echo $rows22_sec_new =  array_sum($rows22_sec);
                }?>
            <td>
            <?php if (isset($rows22_sec_new)) {
            $newrate = ($rows22_sec_new/$top11R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b12_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b12_sec && $value['PressSide'] === 'L') {
                                $code12L_sec .= $value['CuringCode'].",";
                                $top12L_sec .= $value['rate12'].",";
                                $q1_text12L_sec .= $value['Q1'].",";
                                $q2_text12L_sec .= $value['Q2'].",";
                                $q3_text12L_sec .= $value['Q3'].",";
                                $q4_text12L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code12L_sec, ",");
                ?>
            </td>
            <td>
               <?php
                $top12L_sec = trim($top12L_sec, ",");
                $top12L_sec = explode(",", $top12L_sec);
                echo $top12L_sec = $top12L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text12L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text12L_sec, ","); ?></td>
            <td><?php echo trim($q3_text12L_sec, ","); ?></td>
            <td><?php echo trim($q4_text12L_sec, ","); ?></td>
            <td><?php $rows23_sec=array($qty1113_sec,$qty2113_sec,$qty3113_sec,$qty4113_sec);
                if (array_sum($rows23_sec)!=0) {
                    echo $rows23_sec_new =  array_sum($rows23_sec);
                }?>
            <td>
            <?php if (isset($rows23_sec_new)) {
            $newrate = ($rows23_sec_new/$top12L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b12_sec && $value['PressSide'] === 'R') {
                                $code12R_sec .= $value['CuringCode'].",";
                                $top12R_sec .= $value['rate12'].",";
                                $q1_text12R_sec .= $value['Q1'].",";
                                $q2_text12R_sec .= $value['Q2'].",";
                                $q3_text12R_sec .= $value['Q3'].",";
                                $q4_text12R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code12R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top12R_sec = trim($top12R_sec, ",");
                $top12R_sec = explode(",", $top12R_sec);
                echo $top12R_sec = $top12R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text12R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text12R_sec, ","); ?></td>
            <td><?php echo trim($q3_text12R_sec, ","); ?></td>
            <td><?php echo trim($q4_text12R_sec, ","); ?></td>
            <td><?php $rows24_sec=array($qty1114_sec,$qty2114_sec,$qty3114_sec,$qty4114_sec);
                if (array_sum($rows24_sec)!=0) {
                    echo $rows24_sec_new =  array_sum($rows24_sec);
                }?>
            <td>
            <?php if (isset($rows24_sec_new)) {
            $newrate = ($rows24_sec_new/$top12R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td colspan="2" class="f10"><br>
                <b>Total</b>
            </td>
            <td></td>
            <td></td>
            <td>
            <?php $sumtop_sec=array($top01L_sec,$top01R_sec,$top02L_sec,$top02R_sec,$top03L_sec,$top03R_sec,$top04L_sec,$top04R_sec,$top05L_sec,$top05R_sec,$top06L_sec,$top06R_sec,$top07L_sec,$top07R_sec,$top08L_sec,$top08R_sec,$top09L_sec,$top09R_sec,$top10L_sec,$top10R_sec,$top11L_sec,$top11R_sec,$top12L_sec,$top12R);
                if (array_sum($sumtop_sec)!=0) {
                    echo $sumtop_sec = array_sum($sumtop_sec);
                }
            ?>
            </td>
            <td>
            <?php $sumq1_sec=array($qty11_sec,$qty12_sec,$qty13_sec,$qty14_sec,$qty15_sec,$qty16_sec,$qty17_sec,$qty18_sec,$qty19_sec,$qty110_sec,$qty111_sec,$qty112_sec,$qty113_sec,$qty114_sec,$qty115_sec,$qty116_sec,$qty117_sec,$qty118_sec,$qty119_sec,$qty1110_sec,$qty1111_sec,$qty1112_sec,$qty1113_sec,$qty1114_sec);
                if (array_sum($sumq1_sec)!=0) {
                    echo $sumq1_sec = array_sum($sumq1_sec);
                }
            ?>
            </td>
            <td>
            <?php $sumq2_sec=array($qty21_sec,$qty22_sec,$qty23_sec,$qty24_sec,$qty25_sec,$qty26_sec,$qty27_sec,$qty28_sec,$qty29_sec,$qty210_sec,$qty211_sec,$qty212_sec,$qty213_sec,$qty214_sec,$qty215_sec,$qty216_sec,$qty217_sec,$qty218_sec,$qty219_sec,$qty2110_sec,$qty2111_sec,$qty2112_sec,$qty2113_sec,$qty2114_sec);
                if (array_sum($sumq2_sec)!=0) {
                    echo $sumq2_sec = array_sum($sumq2_sec);
                }
            ?>
            </td>
            <td>
            <?php $sumq3_sec=array($qty31_sec,$qty32_sec,$qty33_sec,$qty34_sec,$qty35_sec,$qty36_sec,$qty37_sec,$qty38_sec,$qty39_sec,$qty310_sec,$qty311_sec,$qty312_sec,$qty313_sec,$qty314_sec,$qty315_sec,$qty316_sec,$qty317_sec,$qty318_sec,$qty319_sec,$qty3110_sec,$qty3111_sec,$qty3112_sec,$qty3113_sec,$qty3114_sec);
                if (array_sum($sumq3_sec)!=0) {
                    echo $sumq3_sec = array_sum($sumq3_sec);
                }
            ?>
            </td>
            <td>
            <?php $sumq4_sec=array($qty41_sec,$qty42_sec,$qty43_sec,$qty44_sec,$qty45_sec,$qty46_sec,$qty47_sec,$qty48_sec,$qty49_sec,$qty410_sec,$qty411_sec,$qty412_sec,$qty413_sec,$qty414_sec,$qty415_sec,$qty416_sec,$qty417_sec,$qty418_sec,$qty419_sec,$qty4110_sec,$qty4111_sec,$qty4112_sec,$qty4113_sec,$qty4114_sec);
                if (array_sum($sumq4_sec)!=0) {
                    echo $sumq4_sec = array_sum($sumq4_sec);
                }
            ?>
            </td>
            <td>
            <?php
                foreach ($datajson2 as $value) {
                //$sum = 0;
                $rows2 = array($value->Q1,$value->Q2,$value->Q3,$value->Q4);
                $QQ2 = array_sum($rows2);
                $sumrows2 += $QQ2;
                }
                if ($sumrows2!=0) {
                   echo $sumrows2;
                }

            ?>
            </td>
            <td>
            <?php $sumq_all_sec=array($sumq1_sec,$sumq2_sec,$sumq3_sec,$sumq4_sec);
                if (array_sum($sumq_all_sec)!=0) {
                    $sumq_all_sec = array_sum($sumq_all_sec);
                    $sumper_sec = ($sumq_all_sec/$sumtop_sec)*100;
                    echo $sumper_sec_format_number = number_format($sumper_sec, 2, '.', '');
                }
            ?>
            </td>
            <td class="f10"><br><b>สรุปการผลิต</b></td>
            <td colspan="4" align="left" class="f10">
                <br><b>Cure : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
                if ($sumrows2!=0) {
                   echo $sumrows2;
                }

            ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;เส้น</b>
            </td>
        </tr>
        <tr>
            <td colspan="17" class="f10" valign="bottom"><br><br><b>ผู้ตรวจสอบ : ......................................................................
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            ผู้อนุมัติ : ......................................................................</b></td>
        </tr>
    </table>
    <table class="table">
        <tr class="tr">
            <td class="td" align="left">
                Ref.WI-PP-2.12
            </td>
            <td class="td" align="right">
                FM-PP-2.12.3,Issue #1
            </td>
        </tr>
    </table>
    <table >
        <tr>
            <td colspan="5">
                <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
                style="padding-left:10px;height:55px; width:auto;" /></a>
            </td>
            <td align="center" colspan="12" class="f12">
                <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>CURING REPORT</b>
            </td>
        </tr>
        <tr>
            <td rowspan="2" text-rotate="90" class="f10"><b>รายชื่อ</b></td>
            <td colspan="16" class="f10"><br>
            <b>DATE : <?php echo $datecuring; ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>SHIFT : <?php if($shift=="day"){ echo "กลางวัน"; }else{ echo "กลางคืน";} ?></b>
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
            <td  width="5%"><br><b>Cure code</b></td>
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
            <td width="3%"><br><b>%</b></td>
            <td width="7%"><br><b>Press</b></td>
            <td width="5%"><br><b>TimeOn</b></td>
            <td width="5%"><br><b>TimeOff</b></td>
            <td width="5%"><br><b>TotalTime</b></td>
            <td><br><b>Causes of down time</b></td>
        </tr>

        <tr text-rotate="90">
            <td rowspan="9" text-rotate="90" class="f10">
                <?php
                // $ids=array();
                // foreach ($dataname_third1 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
                ?>
            </td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b01_third; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b01_third && $value['PressSide'] === 'L') {
                                $code01L_third .= $value['CuringCode'].",";
                                $top01L_third .= $value['rate12'].",";
                                $q1_text01L_third .= $value['Q1'].",";
                                $q2_text01L_third .= $value['Q2'].",";
                                $q3_text01L_third .= $value['Q3'].",";
                                $q4_text01L_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code01L_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top01L_third = trim($top01L_third, ",");
                $top01L_third = explode(",", $top01L_third);
                echo $top01L_third = $top01L_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text01L_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text01L_third, ","); ?></td>
            <td><?php echo trim($q3_text01L_third, ","); ?></td>
            <td><?php echo trim($q4_text01L_third, ","); ?></td>
            <td><?php $rows1_third=array($qty11_third,$qty21_third,$qty31_third,$qty41_third);
                if (array_sum($rows1_third)!=0) {
                    echo $rows1_third_new = array_sum($rows1_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows1_third_new)) {
                $newrate = ($rows1_third_new/$top01L_third)*100;
                echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b01_third && $value['PressSide'] === 'R') {
                                $code01R_third .= $value['CuringCode'].",";
                                $top01R_third .= $value['rate12'].",";
                                $q1_text01R_third .= $value['Q1'].",";
                                $q2_text01R_third .= $value['Q2'].",";
                                $q3_text01R_third .= $value['Q3'].",";
                                $q4_text01R_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code01R_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top01R_third = trim($top01R_third, ",");
                $top01R_third = explode(",", $top01R_third);
                echo $top01R_third = $top01R_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text01R_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text01R_third, ","); ?></td>
            <td><?php echo trim($q3_text01R_third, ","); ?></td>
            <td><?php echo trim($q4_text01R_third, ","); ?></td>
            <td><?php $rows2_third=array($qty12_third,$qty22_third,$qty32_third,$qty42_third);
                if (array_sum($rows2_third)!=0) {
                    echo $rows2_third_new = array_sum($rows2_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows2_third_new)) {
                $newrate = ($rows2_third_new/$top01R_third)*100;
                echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b02_third; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b02_third && $value['PressSide'] === 'L') {
                                $code02L_third .= $value['CuringCode'].",";
                                $top02L_third .= $value['rate12'].",";
                                $q1_text02L_third .= $value['Q1'].",";
                                $q2_text02L_third .= $value['Q2'].",";
                                $q3_text02L_third .= $value['Q3'].",";
                                $q4_text02L_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code02L_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top02L_third = trim($top02L_third, ",");
                $top02L_third = explode(",", $top02L_third);
                echo $top02L_third = $top02L_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text02L_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text02L_third, ","); ?></td>
            <td><?php echo trim($q3_text02L_third, ","); ?></td>
            <td><?php echo trim($q4_text02L_third, ","); ?></td>
            <td><?php $rows3_third=array($qty13_third,$qty23_third,$qty33_third,$qty43_third);
                if (array_sum($rows3_third)!=0) {
                    echo $rows3_third_new = array_sum($rows3_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows3_third_new)) {
            $newrate = ($rows3_third_new/$top02L_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b02_third && $value['PressSide'] === 'R') {
                                $code02R_third .= $value['CuringCode'].",";
                                $top02R_third .= $value['rate12'].",";
                                $q1_text02R_third .= $value['Q1'].",";
                                $q2_text02R_third .= $value['Q2'].",";
                                $q3_text02R_third .= $value['Q3'].",";
                                $q4_text02R_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code02R_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top02R_third = trim($top02R_third, ",");
                $top02R_third = explode(",", $top02R_third);
                echo $top02R_third = $top02R_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text02R_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text02R_third, ","); ?></td>
            <td><?php echo trim($q3_text02R_third, ","); ?></td>
            <td><?php echo trim($q4_text02R_third, ","); ?></td>
            <td><?php $rows4_third=array($qty14_third,$qty24_third,$qty34_third,$qty44_third);
                if (array_sum($rows4_third)!=0) {
                    echo $rows4_third_new =  array_sum($rows4_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows4_third_new)) {
            $newrate = ($rows4_third_new/$top02R_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b03_third; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b03_third && $value['PressSide'] === 'L') {
                                $code03L_third .= $value['CuringCode'].",";
                                $top03L_third .= $value['rate12'].",";
                                $q1_text03L_third .= $value['Q1'].",";
                                $q2_text03L_third .= $value['Q2'].",";
                                $q3_text03L_third .= $value['Q3'].",";
                                $q4_text03L_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code03L_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top03L_third = trim($top03L_third, ",");
                $top03L_third = explode(",", $top03L_third);
                echo $top03L_third = $top03L_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text03L_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text03L_third, ","); ?></td>
            <td><?php echo trim($q3_text03L_third, ","); ?></td>
            <td><?php echo trim($q4_text03L_third, ","); ?></td>
           <td><?php $rows5_third=array($qty15_third,$qty25_third,$qty35_third,$qty45_third);
                if (array_sum($rows5_third)!=0) {
                    echo $rows5_third_new =  array_sum($rows5_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows5_third_new)) {
            $newrate = ($rows5_third_new/$top03L_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b03_third && $value['PressSide'] === 'R') {
                                $code03R_third .= $value['CuringCode'].",";
                                $top03R_third .= $value['rate12'].",";
                                $q1_text03R_third .= $value['Q1'].",";
                                $q2_text03R_third .= $value['Q2'].",";
                                $q3_text03R_third .= $value['Q3'].",";
                                $q4_text03R_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code03R_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top03R_third = trim($top03R_third, ",");
                $top03R_third = explode(",", $top03R_third);
                echo $top03R_third = $top03R_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text03R_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text03R_third, ","); ?></td>
            <td><?php echo trim($q3_text03R_third, ","); ?></td>
            <td><?php echo trim($q4_text03R_third, ","); ?></td>
            <td><?php $rows6_third=array($qty16_third,$qty26_third,$qty36_third,$qty46_third);
                if (array_sum($rows6_third)!=0) {
                    echo $rows6_third_new =  array_sum($rows6_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows6_third_new)) {
            $newrate = ($rows6_third_new/$top03R_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b04_third; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b04_third && $value['PressSide'] === 'L') {
                                $code04L_third .= $value['CuringCode'].",";
                                $top04L_third .= $value['rate12'].",";
                                $q1_text04L_third .= $value['Q1'].",";
                                $q2_text04L_third .= $value['Q2'].",";
                                $q3_text04L_third .= $value['Q3'].",";
                                $q4_text04L_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code04L_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top04L_third = trim($top04L_third, ",");
                $top04L_third = explode(",", $top04L_third);
                echo $top04L_third = $top04L_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text04L_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text04L_third, ","); ?></td>
            <td><?php echo trim($q3_text04L_third, ","); ?></td>
            <td><?php echo trim($q4_text04L_third, ","); ?></td>
            <td><?php $rows7_third=array($qty17_third,$qty27_third,$qty37_third,$qty47_third);
                if (array_sum($rows7_third)!=0) {
                    echo $rows7_third_new =  array_sum($rows7_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows7_third_new)) {
            $newrate = ($rows7_third_new/$top04L_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b04_third && $value['PressSide'] === 'R') {
                                $code04R_third .= $value['CuringCode'].",";
                                $top04R_third .= $value['rate12'].",";
                                $q1_text04R_third .= $value['Q1'].",";
                                $q2_text04R_third .= $value['Q2'].",";
                                $q3_text04R_third .= $value['Q3'].",";
                                $q4_text04R_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code04R_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top04R_third = trim($top04R_third, ",");
                $top04R_third = explode(",", $top04R_third);
                echo $top04R_third = $top04R_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text04R_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text04R_third, ","); ?></td>
            <td><?php echo trim($q3_text04R_third, ","); ?></td>
            <td><?php echo trim($q4_text04R_third, ","); ?></td>
            <td><?php $rows8_third=array($qty18_third,$qty28_third,$qty38_third,$qty48_third);
                if (array_sum($rows8_third)!=0) {
                    echo $rows8_third_new =  array_sum($rows8_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows8_third_new)) {
            $newrate = ($rows8_third_new/$top04R_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                // $ids=array();
                // foreach ($dataname_third2 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
                ?>
            </td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b05_third; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b05_third && $value['PressSide'] === 'L') {
                                $code05L_third .= $value['CuringCode'].",";
                                $top05L_third .= $value['rate12'].",";
                                $q1_text05L_third .= $value['Q1'].",";
                                $q2_text05L_third .= $value['Q2'].",";
                                $q3_text05L_third .= $value['Q3'].",";
                                $q4_text05L_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code05L_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top05L_third = trim($top05L_third, ",");
                $top05L_third = explode(",", $top05L_third);
                echo $top05L_third = $top05L_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text05L_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text05L_third, ","); ?></td>
            <td><?php echo trim($q3_text05L_third, ","); ?></td>
            <td><?php echo trim($q4_text05L_third, ","); ?></td>
            <td><?php $rows9_third=array($qty19_third,$qty29_third,$qty39_third,$qty49_third);
                if (array_sum($rows9_third)!=0) {
                    echo $rows9_third_new =  array_sum($rows9_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows9_third_new)) {
            $newrate = ($rows9_third_new/$top05L_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b05_third && $value['PressSide'] === 'R') {
                                $code05R_third .= $value['CuringCode'].",";
                                $top05R_third .= $value['rate12'].",";
                                $q1_text05R_third .= $value['Q1'].",";
                                $q2_text05R_third .= $value['Q2'].",";
                                $q3_text05R_third .= $value['Q3'].",";
                                $q4_text05R_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code05R_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top05R_third = trim($top05R_third, ",");
                $top05R_third = explode(",", $top05R_third);
                echo $top05R_third = $top05R_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text05R_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text05R_third, ","); ?></td>
            <td><?php echo trim($q3_text05R_third, ","); ?></td>
            <td><?php echo trim($q4_text05R_third, ","); ?></td>
            <td><?php $rows10_third=array($qty110_third,$qty210_third,$qty310_third,$qty410_third);
                if (array_sum($rows10_third)!=0) {
                    echo $rows10_third_new =  array_sum($rows10_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows10_third_new)) {
            $newrate = ($rows10_third_new/$top05R_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b06_third; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b06_third && $value['PressSide'] === 'L') {
                                $code06L_third .= $value['CuringCode'].",";
                                $top06L_third .= $value['rate12'].",";
                                $q1_text06L_third .= $value['Q1'].",";
                                $q2_text06L_third .= $value['Q2'].",";
                                $q3_text06L_third .= $value['Q3'].",";
                                $q4_text06L_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code06L_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top06L_third = trim($top06L_third, ",");
                $top06L_third = explode(",", $top06L_third);
                echo $top06L_third = $top06L_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text06L_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text06L_third, ","); ?></td>
            <td><?php echo trim($q3_text06L_third, ","); ?></td>
            <td><?php echo trim($q4_text06L_third, ","); ?></td>
            <td><?php $rows11_third=array($qty111_third,$qty211_third,$qty311_third,$qty411_third);
                if (array_sum($rows11_third)!=0) {
                    echo $rows11_third_new =  array_sum($rows11_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows11_third_new)) {
            $newrate = ($rows11_third_new/$top06L_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b06_third && $value['PressSide'] === 'R') {
                                $code06R_third .= $value['CuringCode'].",";
                                $top06R_third .= $value['rate12'].",";
                                $q1_text06R_third .= $value['Q1'].",";
                                $q2_text06R_third .= $value['Q2'].",";
                                $q3_text06R_third .= $value['Q3'].",";
                                $q4_text06R_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code06R_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top06R_third = trim($top06R_third, ",");
                $top06R_third = explode(",", $top06R_third);
                echo $top06R_third = $top06R_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text06R_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text06R_third, ","); ?></td>
            <td><?php echo trim($q3_text06R_third, ","); ?></td>
            <td><?php echo trim($q4_text06R_third, ","); ?></td>
            <td><?php $rows12_third=array($qty112_third,$qty212_third,$qty312_third,$qty412_third);
                if (array_sum($rows12_third)!=0) {
                    echo $rows12_third_new =  array_sum($rows12_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows12_third_new)) {
            $newrate = ($rows12_third_new/$top06R_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b07_third; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b07_third && $value['PressSide'] === 'L') {
                                $code07L_third .= $value['CuringCode'].",";
                                $top07L_third .= $value['rate12'].",";
                                $q1_text07L_third .= $value['Q1'].",";
                                $q2_text07L_third .= $value['Q2'].",";
                                $q3_text07L_third .= $value['Q3'].",";
                                $q4_text07L_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code07L_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top07L_third = trim($top07L_third, ",");
                $top07L_third = explode(",", $top07L_third);
                echo $top07L_third = $top07L_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text07L_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text07L_third, ","); ?></td>
            <td><?php echo trim($q3_text07L_third, ","); ?></td>
            <td><?php echo trim($q4_text07L_third, ","); ?></td>
            <td><?php $rows13_third=array($qty113_third,$qty213_third,$qty313_third,$qty413_third);
                if (array_sum($rows13_third)!=0) {
                    echo $rows13_third_new =  array_sum($rows13_third);
                }?>
            <td>
            <?php if (isset($rows13_third_new)) {
            $newrate = ($rows13_third_new/$top07L_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b07_third && $value['PressSide'] === 'R') {
                                $code07R_third .= $value['CuringCode'].",";
                                $top07R_third .= $value['rate12'].",";
                                $q1_text07R_third .= $value['Q1'].",";
                                $q2_text07R_third .= $value['Q2'].",";
                                $q3_text07R_third .= $value['Q3'].",";
                                $q4_text07R_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code07R_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top07R_third = trim($top07R_third, ",");
                $top07R_third = explode(",", $top07R_third);
                echo $top07R_third = $top07R_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text07R_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text07R_third, ","); ?></td>
            <td><?php echo trim($q3_text07R_third, ","); ?></td>
            <td><?php echo trim($q4_text07R_third, ","); ?></td>
            <td><?php $rows14_third=array($qty114_third,$qty214_third,$qty314_third,$qty414_third);
                if (array_sum($rows14_third)!=0) {
                    echo $rows14_third_new =  array_sum($rows14_third);
                }?>
            <td>
            <?php if (isset($rows14_third_new)) {
            $newrate = ($rows14_third_new/$top07R_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b08_third; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b08_third && $value['PressSide'] === 'L') {
                                $code08L_third .= $value['CuringCode'].",";
                                $top08L_third .= $value['rate12'].",";
                                $q1_text08L_third .= $value['Q1'].",";
                                $q2_text08L_third .= $value['Q2'].",";
                                $q3_text08L_third .= $value['Q3'].",";
                                $q4_text08L_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code08L_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top08L_third = trim($top08L_third, ",");
                $top08L_third = explode(",", $top08L_third);
                echo $top08L_third = $top08L_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text08L_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text08L_third, ","); ?></td>
            <td><?php echo trim($q3_text08L_third, ","); ?></td>
            <td><?php echo trim($q4_text08L_third, ","); ?></td>
            <td><?php $rows15_third=array($qty115_third,$qty215_third,$qty315_third,$qty415_third);
                if (array_sum($rows15_third)!=0) {
                    echo $rows15_third_new =  array_sum($rows15_third);
                }?>
            <td>
            <?php if (isset($rows15_third_new)) {
            $newrate = ($rows15_third_new/$top08L_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b08_third && $value['PressSide'] === 'R') {
                                $code08R_third .= $value['CuringCode'].",";
                                $top08R_third .= $value['rate12'].",";
                                $q1_text08R_third .= $value['Q1'].",";
                                $q2_text08R_third .= $value['Q2'].",";
                                $q3_text08R_third .= $value['Q3'].",";
                                $q4_text08R_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code08R_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top08R_third = trim($top08R_third, ",");
                $top08R_third = explode(",", $top08R_third);
                echo $top08R_third = $top08R_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text08R_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text08R_third, ","); ?></td>
            <td><?php echo trim($q3_text08R_third, ","); ?></td>
            <td><?php echo trim($q4_text08R_third, ","); ?></td>
            <<td><?php $rows16_third=array($qty116_third,$qty216_third,$qty316_third,$qty416_third);
                if (array_sum($rows16_third)!=0) {
                    echo $rows16_third_new =  array_sum($rows16_third);
                }?>
            <td>
            <?php if (isset($rows16_third_new)) {
            $newrate = ($rows16_third_new/$top08R_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                // $ids=array();
                // foreach ($dataname_third3 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
                ?>
            </td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b09_third; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b09_third && $value['PressSide'] === 'L') {
                                $code09L_third .= $value['CuringCode'].",";
                                $top09L_third .= $value['rate12'].",";
                                $q1_text09L_third .= $value['Q1'].",";
                                $q2_text09L_third .= $value['Q2'].",";
                                $q3_text09L_third .= $value['Q3'].",";
                                $q4_text09L_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code09L_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top09L_third = trim($top09L_third, ",");
                $top09L_third = explode(",", $top09L_third);
                echo $top09L_third = $top09L_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text09L_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text09L_third, ","); ?></td>
            <td><?php echo trim($q3_text09L_third, ","); ?></td>
            <td><?php echo trim($q4_text09L_third, ","); ?></td>
            <td><?php $rows17_third=array($qty117_third,$qty217_third,$qty317_third,$qty417_third);
                if (array_sum($rows17_third)!=0) {
                    echo $rows17_third_new =  array_sum($rows17_third);
                }?>
            <td>
            <?php if (isset($rows17_third_new)) {
            $newrate = ($rows17_third_new/$top09L_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b09_third && $value['PressSide'] === 'R') {
                                $code09R_third .= $value['CuringCode'].",";
                                $top09R_third .= $value['rate12'].",";
                                $q1_text09R_third .= $value['Q1'].",";
                                $q2_text09R_third .= $value['Q2'].",";
                                $q3_text09R_third .= $value['Q3'].",";
                                $q4_text09R_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code09R_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top09R_third = trim($top09R_third, ",");
                $top09R_third = explode(",", $top09R_third);
                echo $top09R_third = $top09R_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text09R_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text09R_third, ","); ?></td>
            <td><?php echo trim($q3_text09R_third, ","); ?></td>
            <td><?php echo trim($q4_text09R_third, ","); ?></td>
            <td><?php $rows18_third=array($qty118_third,$qty218_third,$qty318_third,$qty418_third);
                if (array_sum($rows18_third)!=0) {
                    echo $rows18_third_new =  array_sum($rows18_third);
                }?>
            <td>
            <?php if (isset($rows18_third_new)) {
            $newrate = ($rows18_third_new/$top09R_third)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b10_third; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b10_third && $value['PressSide'] === 'L') {
                                $code10L_third .= $value['CuringCode'].",";
                                $top10L_third .= $value['rate12'].",";
                                $q1_text10L_third .= $value['Q1'].",";
                                $q2_text10L_third .= $value['Q2'].",";
                                $q3_text10L_third .= $value['Q3'].",";
                                $q4_text10L_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code10L_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top10L_third = trim($top10L_third, ",");
                $top10L_third = explode(",", $top10L_third);
                echo $top10L_third = $top10L_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text10L_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text10L_third, ","); ?></td>
            <td><?php echo trim($q3_text10L_third, ","); ?></td>
            <td><?php echo trim($q4_text10L_third, ","); ?></td>
            <td><?php $rows19_third=array($qty119_third,$qty219_third,$qty319_third,$qty419_third); if (array_sum($rows19_third)!=0) {
                    echo $rows19_third_new =  array_sum($rows19_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows19_third_new)) {
                $newrate = ($rows19_third_new/$top10L_third)*100;
                echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b10_third && $value['PressSide'] === 'R') {
                                $code10R_third .= $value['CuringCode'].",";
                                $top10R_third .= $value['rate12'].",";
                                $q1_text10R_third .= $value['Q1'].",";
                                $q2_text10R_third .= $value['Q2'].",";
                                $q3_text10R_third .= $value['Q3'].",";
                                $q4_text10R_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code10R_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top10R_third = trim($top10R_third, ",");
                $top10R_third = explode(",", $top10R_third);
                echo $top10R_third = $top10R_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text10R_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text10R_third, ","); ?></td>
            <td><?php echo trim($q3_text10R_third, ","); ?></td>
            <td><?php echo trim($q4_text10R_third, ","); ?></td>
            <td><?php $rows20_third=array($qty1110_third,$qty2110_third,$qty3110_third,$qty4110_third);
                if (array_sum($rows20_third)!=0) {
                    echo $rows20_third_new =  array_sum($rows20_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows20_third_new)) {
                $newrate = ($rows20_third_new/$top10R_third)*100;
                echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b11_third; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b11_third && $value['PressSide'] === 'L') {
                                $code11L_third .= $value['CuringCode'].",";
                                $top11L_third .= $value['rate12'].",";
                                $q1_text11L_third .= $value['Q1'].",";
                                $q2_text11L_third .= $value['Q2'].",";
                                $q3_text11L_third .= $value['Q3'].",";
                                $q4_text11L_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code11L_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top11L_third = trim($top11L_third, ",");
                $top11L_third = explode(",", $top11L_third);
                echo $top11L_third = $top11L_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text11L_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text11L_third, ","); ?></td>
            <td><?php echo trim($q3_text11L_third, ","); ?></td>
            <td><?php echo trim($q4_text11L_third, ","); ?></td>
            <td><?php $rows21_third=array($qty1111_third,$qty2111_third,$qty3111_third,$qty4111_third);
            if (array_sum($rows21_third)!=0) {
                    echo $rows21_third_new =   array_sum($rows21_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows21_third_new)) {
                $newrate = ($rows21_third_new/$top11L_third)*100;
                echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b11_third && $value['PressSide'] === 'R') {
                                $code11R_third .= $value['CuringCode'].",";
                                $top11R_third .= $value['rate12'].",";
                                $q1_text11R_third .= $value['Q1'].",";
                                $q2_text11R_third .= $value['Q2'].",";
                                $q3_text11R_third .= $value['Q3'].",";
                                $q4_text11R_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code11R_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top11R_third = trim($top11R_third, ",");
                $top11R_third = explode(",", $top11R_third);
                echo $top11R_third = $top11R_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text11R_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text11R_third, ","); ?></td>
            <td><?php echo trim($q3_text11R_third, ","); ?></td>
            <td><?php echo trim($q4_text11R_third, ","); ?></td>
            <td><?php $rows22_third=array($qty1112_third,$qty2112_third,$qty3112_third,$qty4112_third);
            if (array_sum($rows22_third)!=0) {
                    echo $rows22_third_new =   array_sum($rows22_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows22_third_new)) {
                $newrate = ($rows22_third_new/$top11R_third)*100;
                echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b12_third; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b12_third && $value['PressSide'] === 'L') {
                                $code12L_third .= $value['CuringCode'].",";
                                $top12L_third .= $value['rate12'].",";
                                $q1_text12L_third .= $value['Q1'].",";
                                $q2_text12L_third .= $value['Q2'].",";
                                $q3_text12L_third .= $value['Q3'].",";
                                $q4_text12L_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code12L_third, ",");
                ?>
            </td>
            <td>
               <?php
                $top12L_third = trim($top12L_third, ",");
                $top12L_third = explode(",", $top12L_third);
                echo $top12L_third = $top12L_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text12L_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text12L_third, ","); ?></td>
            <td><?php echo trim($q3_text12L_third, ","); ?></td>
            <td><?php echo trim($q4_text12L_third, ","); ?></td>
            <<td><?php $rows23_third=array($qty1113_third,$qty2113_third,$qty3113_third,$qty4113_third);
            if (array_sum($rows23_third)!=0) {
                    echo $rows23_third_new =   array_sum($rows23_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows23_third_new)) {
                $newrate = ($rows23_third_new/$top12L_third)*100;
                echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                        foreach ($datajsonQ3 as $key => $value) {
                            if ($value['PressNo'] === $b12_third && $value['PressSide'] === 'R') {
                                $code12R_third .= $value['CuringCode'].",";
                                $top12R_third .= $value['rate12'].",";
                                $q1_text12R_third .= $value['Q1'].",";
                                $q2_text12R_third .= $value['Q2'].",";
                                $q3_text12R_third .= $value['Q3'].",";
                                $q4_text12R_third .= $value['Q4'].",";
                            }
                        }
                    echo trim($code12R_third, ",");
                ?>
            </td>
            <td>
                <?php
                $top12R_third = trim($top12R_third, ",");
                $top12R_third = explode(",", $top12R_third);
                echo $top12R_third = $top12R_third[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text12R_third, ","); ?>
            </td>
            <td><?php echo trim($q2_text12R_third, ","); ?></td>
            <td><?php echo trim($q3_text12R_third, ","); ?></td>
            <td><?php echo trim($q4_text12R_third, ","); ?></td>
            <td><?php $rows24_third=array($qty1114_third,$qty2114_third,$qty3114_third,$qty4114_third);
            if (array_sum($rows24_third)!=0) {
                    echo $rows24_third_new =   array_sum($rows24_third);
                }?>
            </td>
            <td>
            <?php if (isset($rows24_third_new)) {
                $newrate = ($rows24_third_new/$top12R_third)*100;
                echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td colspan="2" class="f10"><br>
                <b>Total</b>
            </td>
            <td></td>
            <td></td>
            <td>
            <?php $sumtop_third=array($top01L_third,$top01R_third,$top02L_third,$top02R_third,$top03L_third,$top03R_third,$top04L_third,$top04R_third,$top05L_third,$top05R_third,$top06L_third,$top06R_third,$top07L_third,$top07R_third,$top08L_third,$top08R_third,$top09L_third,$top09R_third,$top10L_third,$top10R_third,$top11L_third,$top11R_third,$top12L_third,$top12R_third);
                if (array_sum($sumtop_third)!=0) {
                    echo $sumtop_third = array_sum($sumtop_third);
                }
            ?>
            </td>
            <td>
            <?php $sumq1_third=array($qty11_third,$qty12_third,$qty13_third,$qty14_third,$qty15_third,$qty16_third,$qty17_third,$qty18_third,$qty19_third,$qty110_third,$qty111_third,$qty112_third,$qty113_third,$qty114_third,$qty115_third,$qty116_third,$qty117_third,$qty118_third,$qty119_third,$qty1110_third,$qty1111_third,$qty1112_third,$qty1113_third,$qty1114_third);
                if (array_sum($sumq1_third)!=0) {
                    echo $sumq1_third = array_sum($sumq1_third);
                }
            ?>
            </td>
            <td>
            <?php $sumq2_third=array($qty21_third,$qty22_third,$qty23_third,$qty24_third,$qty25_third,$qty26_third,$qty27_third,$qty28_third,$qty29_third,$qty210_third,$qty211_third,$qty212_third,$qty213_third,$qty214_third,$qty215_third,$qty216_third,$qty217_third,$qty218_third,$qty219_third,$qty2110_third,$qty2111_third,$qty2112_third,$qty2113_third,$qty2114_third);
                if (array_sum($sumq2_third)!=0) {
                    echo $sumq2_third = array_sum($sumq2_third);
                }
            ?>
            </td>
            <td>
            <?php $sumq3_third=array($qty31_third,$qty32_third,$qty33_third,$qty34_third,$qty35_third,$qty36_third,$qty37_third,$qty38_third,$qty39_third,$qty310_third,$qty311_third,$qty312_third,$qty313_third,$qty314_third,$qty315_third,$qty316_third,$qty317_third,$qty318_third,$qty319_third,$qty3110_third,$qty3111_third,$qty3112_third,$qty3113_third,$qty3114_third);
                if (array_sum($sumq3_third)!=0) {
                    echo $sumq3_third = array_sum($sumq3_third);
                }
            ?>
            </td>
            <td>
            <?php $sumq4_third=array($qty41_third,$qty42_third,$qty43_third,$qty44_third,$qty45_third,$qty46_third,$qty47_third,$qty48_third,$qty49_third,$qty410_third,$qty411_third,$qty412_third,$qty413_third,$qty414_third,$qty415_third,$qty416_third,$qty417_third,$qty418_third,$qty419_third,$qty4110_third,$qty4111_third,$qty4112_third,$qty4113_third,$qty4114_third);
                if (array_sum($sumq4_third)!=0) {
                    echo $sumq4_third = array_sum($sumq4_third);
                }
            ?>
            </td>
            <td>
            <?php
                foreach ($datajson3 as $value) {
                $sum = 0;
                $rows3 = array($value->Q1,$value->Q2,$value->Q3,$value->Q4);
                $QQ3 = array_sum($rows3);
                $sumrows3 += $QQ3;
                }
                if ($sumrows3!=0) {
                   echo $sumrows3;
                }

            ?>
            </td>
            <td>
            <?php $sumq_all_third=array($sumq1_third,$sumq2_third,$sumq3_third,$sumq4_third);
                if (array_sum($sumq_all_third)!=0) {
                    $sumq_all_third = array_sum($sumq_all_third);
                    $sumper_third = ($sumq_all_third/$sumtop_third)*100;
                    echo $sumper_third_format_number = number_format($sumper_third, 2, '.', '');
                }
            ?>
            </td>
            <td class="f10"><br><b>สรุปการผลิต</b></td>
            <td colspan="4" align="left" class="f10">
                <br><b>Cure : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
                if ($sumrows3!=0) {
                    echo $sumrows3;
                }

            ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;เส้น</b>
            </td>
        </tr>
        <tr>
            <td colspan="17" class="f10" valign="bottom"><br><br><b>ผู้ตรวจสอบ : ......................................................................
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            ผู้อนุมัติ : ......................................................................</b></td>
        </tr>
    </table>
    <table class="table">
        <tr class="tr">
            <td class="td" align="left">
                Ref.WI-PP-2.12
            </td>
            <td class="td" align="right">
                FM-PP-2.12.3,Issue #1
            </td>
        </tr>
    </table>
<?php }else if(isset($press1) && isset($press2)){ ?>
    <table >
        <tr>
            <td colspan="5">
                <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
                style="padding-left:10px;height:55px; width:auto;" /></a>
            </td>
            <td align="center" colspan="12" class="f12">
                <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>CURING REPORT</b>
            </td>
        </tr>
        <tr>
            <td rowspan="2" text-rotate="90" class="f10"><b>รายชื่อ</b></td>
            <td colspan="16" class="f10"><br>
            <b>DATE : <?php echo $datecuring; ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>SHIFT : <?php if($shift=="day"){ echo "กลางวัน"; }else{ echo "กลางคืน";} ?></b>
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
            <td width="5%"><br><b>Cure code</b></td>
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
            <td width="7%"><br><b>Press</b></td>
            <td width="5%"><br><b>TimeOn</b></td>
            <td width="5%"><br><b>TimeOff</b></td>
            <td width="5%"><br><b>TotalTime</b></td>
            <td><br><b>Causes of down time</b></td>
        </tr>

        <tr text-rotate="90">
            <td rowspan="9" text-rotate="90" class="f10">
                <?php
                // $ids=array();
                // foreach ($dataname1 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
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
                                $code01L .= $value['CuringCode'].",";
                                $top01L .= $value['rate12'].",";
                                $q1_text01L .= $value['Q1'].",";
                                $q2_text01L .= $value['Q2'].",";
                                $q3_text01L .= $value['Q3'].",";
                                $q4_text01L .= $value['Q4'].",";
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
            <td><?php $rows1=array($qty11,$qty21,$qty31,$qty41);
                if (array_sum($rows1)!=0) {
                    echo $rows1_new = array_sum($rows1);
                }?>
            </td>
            <td>
            <?php if (isset($rows1_new)) {
            $newrate = ($rows1_new/$top01L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code01R .= $value['CuringCode'].",";
                                $top01R .= $value['rate12'].",";
                                $q1_text01R .= $value['Q1'].",";
                                $q2_text01R .= $value['Q2'].",";
                                $q3_text01R .= $value['Q3'].",";
                                $q4_text01R .= $value['Q4'].",";
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
            <td><?php $rows2=array($qty12,$qty22,$qty32,$qty42);
                if (array_sum($rows2)!=0) {
                    echo $rows2_new = array_sum($rows2);
                }?>
            </td>
            <td>
            <?php if (isset($rows2_new)) {
            $newrate = ($rows2_new/$top01R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code02L .= $value['CuringCode'].",";
                                $top02L .= $value['rate12'].",";
                                $q1_text02L .= $value['Q1'].",";
                                $q2_text02L .= $value['Q2'].",";
                                $q3_text02L .= $value['Q3'].",";
                                $q4_text02L .= $value['Q4'].",";
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
            <td><?php $rows3=array($qty13,$qty23,$qty33,$qty43);
                if (array_sum($rows3)!=0) {
                    echo $rows3_new = array_sum($rows3);
                }?>
            </td>
            <td>
            <?php if (isset($rows3_new)) {
            $newrate = ($rows3_new/$top02L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code02R .= $value['CuringCode'].",";
                                $top02R .= $value['rate12'].",";
                                $q1_text02R .= $value['Q1'].",";
                                $q2_text02R .= $value['Q2'].",";
                                $q3_text02R .= $value['Q3'].",";
                                $q4_text02R .= $value['Q4'].",";
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
            <td><?php $rows4=array($qty14,$qty24,$qty34,$qty44);
                if (array_sum($rows4)!=0) {
                    echo $rows4_new = array_sum($rows4);
                }?>
            </td>
            <td>
            <?php if (isset($rows4_new)) {
            $newrate = ($rows4_new/$top02R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code03L .= $value['CuringCode'].",";
                                $top03L .= $value['rate12'].",";
                                $q1_text03L .= $value['Q1'].",";
                                $q2_text03L .= $value['Q2'].",";
                                $q3_text03L .= $value['Q3'].",";
                                $q4_text03L .= $value['Q4'].",";
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
            <td><?php $rows5=array($qty15,$qty25,$qty35,$qty45);
                if (array_sum($rows5)!=0) {
                    echo $rows5_new = array_sum($rows5);
                }?>
            </td>
            <td>
            <?php if (isset($rows5_new)) {
            $newrate = ($rows5_new/$top03L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code03R .= $value['CuringCode'].",";
                                $top03R .= $value['rate12'].",";
                                $q1_text03R .= $value['Q1'].",";
                                $q2_text03R .= $value['Q2'].",";
                                $q3_text03R .= $value['Q3'].",";
                                $q4_text03R .= $value['Q4'].",";
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
            <td><?php $rows6=array($qty16,$qty26,$qty36,$qty46);
                if (array_sum($rows6)!=0) {
                    echo $rows6_new = array_sum($rows6);
                }?>
            </td>
            <td>
            <?php if (isset($rows6_new)) {
            $newrate = ($rows6_new/$top03R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code04L .= $value['CuringCode'].",";
                                $top04L .= $value['rate12'].",";
                                $q1_text04L .= $value['Q1'].",";
                                $q2_text04L .= $value['Q2'].",";
                                $q3_text04L .= $value['Q3'].",";
                                $q4_text04L .= $value['Q4'].",";
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
            <td><?php $rows7=array($qty17,$qty27,$qty37,$qty47);
                if (array_sum($rows7)!=0) {
                    echo $rows7_new = array_sum($rows7);
                }?>
            </td>
            <td>
            <?php if (isset($rows7_new)) {
            $newrate = ($rows7_new/$top04L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code04R .= $value['CuringCode'].",";
                                $top04R .= $value['rate12'].",";
                                $q1_text04R .= $value['Q1'].",";
                                $q2_text04R .= $value['Q2'].",";
                                $q3_text04R .= $value['Q3'].",";
                                $q4_text04R .= $value['Q4'].",";
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
            <td><?php $rows8=array($qty18,$qty28,$qty38,$qty48);
                if (array_sum($rows8)!=0) {
                    echo $rows8_new = array_sum($rows8);
                }?>
            </td>
            <td>
            <?php if (isset($rows8_new)) {
            $newrate = ($rows8_new/$top04R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                // $ids=array();
                // foreach ($dataname2 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
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
                                $code05L .= $value['CuringCode'].",";
                                $top05L .= $value['rate12'].",";
                                $q1_text05L .= $value['Q1'].",";
                                $q2_text05L .= $value['Q2'].",";
                                $q3_text05L .= $value['Q3'].",";
                                $q4_text05L .= $value['Q4'].",";
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
            <td><?php $rows9=array($qty19,$qty29,$qty39,$qty49);
                if (array_sum($rows9)!=0) {
                    echo $rows9_new = array_sum($rows9);
                }?>
            </td>
            <td>
            <?php if (isset($rows9_new)) {
            $newrate = ($rows9_new/$top05L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code05R .= $value['CuringCode'].",";
                                $top05R .= $value['rate12'].",";
                                $q1_text05R .= $value['Q1'].",";
                                $q2_text05R .= $value['Q2'].",";
                                $q3_text05R .= $value['Q3'].",";
                                $q4_text05R .= $value['Q4'].",";
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
            <td><?php $rows10=array($qty110,$qty210,$qty310,$qty410);
                if (array_sum($rows10)!=0) {
                    echo $rows10_new = array_sum($rows10);
                }?>
            </td>
            <td>
            <?php if (isset($rows10_new)) {
            $newrate = ($rows10_new/$top05R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code06L .= $value['CuringCode'].",";
                                $top06L .= $value['rate12'].",";
                                $q1_text06L .= $value['Q1'].",";
                                $q2_text06L .= $value['Q2'].",";
                                $q3_text06L .= $value['Q3'].",";
                                $q4_text06L .= $value['Q4'].",";
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
            <td><?php $rows11=array($qty111,$qty211,$qty311,$qty411);
                if (array_sum($rows11)!=0) {
                    echo $rows11_new = array_sum($rows11);
                }?>
            </td>
            <td>
            <?php if (isset($rows11_new)) {
            $newrate = ($rows11_new/$top06L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code06R .= $value['CuringCode'].",";
                                $top06R .= $value['rate12'].",";
                                $q1_text06R .= $value['Q1'].",";
                                $q2_text06R .= $value['Q2'].",";
                                $q3_text06R .= $value['Q3'].",";
                                $q4_text06R .= $value['Q4'].",";
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
            <td><?php $rows12=array($qty112,$qty212,$qty312,$qty412);
                if (array_sum($rows12)!=0) {
                    echo $rows12_new = array_sum($rows12);
                }?>
            </td>
            <td>
            <?php if (isset($rows12_new)) {
            $newrate = ($rows12_new/$top06R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code07L .= $value['CuringCode'].",";
                                $top07L .= $value['rate12'].",";
                                $q1_text07L .= $value['Q1'].",";
                                $q2_text07L .= $value['Q2'].",";
                                $q3_text07L .= $value['Q3'].",";
                                $q4_text07L .= $value['Q4'].",";
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
            <td><?php $rows13=array($qty113,$qty213,$qty313,$qty413);
                if (array_sum($rows13)!=0) {
                    echo $rows13_new = array_sum($rows13);
                }?>
            </td>
            <td>
            <?php if (isset($rows13_new)) {
            $newrate = ($rows13_new/$top07L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code07R .= $value['CuringCode'].",";
                                $top07R .= $value['rate12'].",";
                                $q1_text07R .= $value['Q1'].",";
                                $q2_text07R .= $value['Q2'].",";
                                $q3_text07R .= $value['Q3'].",";
                                $q4_text07R .= $value['Q4'].",";
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
            <td><?php $rows14=array($qty114,$qty214,$qty314,$qty414);
                if (array_sum($rows14)!=0) {
                    echo $rows14_new = array_sum($rows14);
                }?>
            </td>
            <td>
            <?php if (isset($rows14_new)) {
            $newrate = ($rows14_new/$top07R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code08L .= $value['CuringCode'].",";
                                $top08L .= $value['rate12'].",";
                                $q1_text08L .= $value['Q1'].",";
                                $q2_text08L .= $value['Q2'].",";
                                $q3_text08L .= $value['Q3'].",";
                                $q4_text08L .= $value['Q4'].",";
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
            <td><?php $rows15=array($qty115,$qty215,$qty315,$qty415);
                if (array_sum($rows15)!=0) {
                    echo $rows15_new = array_sum($rows15);
                }?>
            </td>
            <td>
            <?php if (isset($rows15_new)) {
            $newrate = ($rows15_new/$top08L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code08R .= $value['CuringCode'].",";
                                $top08R .= $value['rate12'].",";
                                $q1_text08R .= $value['Q1'].",";
                                $q2_text08R .= $value['Q2'].",";
                                $q3_text08R .= $value['Q3'].",";
                                $q4_text08R .= $value['Q4'].",";
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
            <<td><?php $rows16=array($qty116,$qty216,$qty316,$qty416);
                if (array_sum($rows16)!=0) {
                    echo $rows16_new = array_sum($rows16);
                }?>
            </td>
            <td>
            <?php if (isset($rows16_new)) {
            $newrate = ($rows16_new/$top08R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                // $ids=array();
                // foreach ($dataname3 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
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
                                $code09L .= $value['CuringCode'].",";
                                $top09L .= $value['rate12'].",";
                                $q1_text09L .= $value['Q1'].",";
                                $q2_text09L .= $value['Q2'].",";
                                $q3_text09L .= $value['Q3'].",";
                                $q4_text09L .= $value['Q4'].",";
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
            <td><?php $rows17=array($qty117,$qty217,$qty317,$qty417);
                if (array_sum($rows17)!=0) {
                    echo $rows17_new = array_sum($rows17);
                }?>
            </td>
            <td>
            <?php if (isset($rows17_new)) {
            $newrate = ($rows17_new/$top09L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code09R .= $value['CuringCode'].",";
                                $top09R .= $value['rate12'].",";
                                $q1_text09R .= $value['Q1'].",";
                                $q2_text09R .= $value['Q2'].",";
                                $q3_text09R .= $value['Q3'].",";
                                $q4_text09R .= $value['Q4'].",";
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
            <td><?php $rows18=array($qty118,$qty218,$qty318,$qty418);
                if (array_sum($rows18)!=0) {
                    echo $rows18_new = array_sum($rows18);
                }?>
            </td>
            <td>
            <?php if (isset($rows18_new)) {
            $newrate = ($rows18_new/$top09R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b10 && $value['PressSide'] === 'L') {
                                $code10L .= $value['CuringCode'].",";
                                $top10L .= $value['rate12'].",";
                                $q1_text10L .= $value['Q1'].",";
                                $q2_text10L .= $value['Q2'].",";
                                $q3_text10L .= $value['Q3'].",";
                                $q4_text10L .= $value['Q4'].",";
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
            <td><?php $rows19=array($qty119,$qty219,$qty319,$qty419);
                if (array_sum($rows19)!=0) {
                    echo $rows19_new = array_sum($rows19);
                }?>
            </td>
            <td>
            <?php if (isset($rows19_new)) {
            $newrate = ($rows19_new/$top10L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                            if ($value['PressNo'] === $b10 && $value['PressSide'] === 'R') {
                                $code10R .= $value['CuringCode'].",";
                                $top10R .= $value['rate12'].",";
                                $q1_text10R .= $value['Q1'].",";
                                $q2_text10R .= $value['Q2'].",";
                                $q3_text10R .= $value['Q3'].",";
                                $q4_text10R .= $value['Q4'].",";
                            }
                        }
                    echo trim($code10R, ",");
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
            <td><?php $rows20=array($qty1110,$qty2110,$qty3110,$qty4110);
                if (array_sum($rows20)!=0) {
                    echo $rows20_new = array_sum($rows20);
                }?>
            </td>
            <td>
            <?php if (isset($rows20_new)) {
            $newrate = ($rows20_new/$top10R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b11; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b11 && $value['PressSide'] === 'L') {
                                $code11L .= $value['CuringCode'].",";
                                $top11L .= $value['rate12'].",";
                                $q1_text11L .= $value['Q1'].",";
                                $q2_text11L .= $value['Q2'].",";
                                $q3_text11L .= $value['Q3'].",";
                                $q4_text11L .= $value['Q4'].",";
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
            <td><?php $rows21=array($qty1111,$qty2111,$qty3111,$qty4111);
                if (array_sum($rows21)!=0) {
                    echo $rows21_new = array_sum($rows21);
                }?>
            </td>
            <td>
            <?php if (isset($rows21_new)) {
            $newrate = ($rows21_new/$top11L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                            if ($value['PressNo'] === $b11 && $value['PressSide'] === 'R') {
                                $code11R .= $value['CuringCode'].",";
                                $top11R .= $value['rate12'].",";
                                $q1_text11R .= $value['Q1'].",";
                                $q2_text11R .= $value['Q2'].",";
                                $q3_text11R .= $value['Q3'].",";
                                $q4_text11R .= $value['Q4'].",";
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
            <td><?php $rows22=array($qty1112,$qty2112,$qty3112,$qty4112);
                if (array_sum($rows22)!=0) {
                    echo $rows22_new = array_sum($rows22);
                }?>
            </td>
            <td>
            <?php if (isset($rows22_new)) {
            $newrate = ($rows22_new/$top11R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b12; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b12 && $value['PressSide'] === 'L') {
                                $code12L .= $value['CuringCode'].",";
                                $top12L .= $value['rate12'].",";
                                $q1_text12L .= $value['Q1'].",";
                                $q2_text12L .= $value['Q2'].",";
                                $q3_text12L .= $value['Q3'].",";
                                $q4_text12L .= $value['Q4'].",";
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
            <td><?php $rows23=array($qty1113,$qty2113,$qty3113,$qty4113);
                if (array_sum($rows23)!=0) {
                    echo $rows23_new = array_sum($rows23);
                }?>
            </td>
            <td>
            <?php if (isset($rows23_new)) {
            $newrate = ($rows23_new/$top12L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                                $code12R .= $value['CuringCode'].",";
                                $top12R .= $value['rate12'].",";
                                $q1_text12R .= $value['Q1'].",";
                                $q2_text12R .= $value['Q2'].",";
                                $q3_text12R .= $value['Q3'].",";
                                $q4_text12R .= $value['Q4'].",";
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
            <td><?php $rows24=array($qty1114,$qty2114,$qty3114,$qty4114);
                if (array_sum($rows24)!=0) {
                    echo $rows24_new = array_sum($rows24);
                }?>
            </td>
            <td>
            <?php if (isset($rows24_new)) {
            $newrate = ($rows24_new/$top12R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td colspan="2" class="f10"><br>
                <b>Total</b>
            </td>
            <td></td>
            <td></td>
            <td>
            <?php $sumtop=array($top01L,$top01R,$top02L,$top02R,$top03L,$top03R,$top04L,$top04R,$top05L,$top05R,$top06L,$top06R,$top07L,$top07R,$top08L,$top08R,$top09L,$top09R,$top10L,$top10R,$top11L,$top11R,$top12L,$top12R);
                if (array_sum($sumtop)!=0) {
                    echo $sumtop = array_sum($sumtop);
                }
            ?>
            </td>
            <td>
            <?php $sumq1=array($qty11,$qty12,$qty13,$qty14,$qty15,$qty16,$qty17,$qty18,$qty19,$qty110,$qty111,$qty112,$qty113,$qty114,$qty115,$qty116,$qty117,$qty118,$qty119,$qty1110,$qty1111,$qty1112,$qty1113,$qty1114);
                if (array_sum($sumq1)!=0) {
                    echo $sumq1 = array_sum($sumq1);
                }
            ?>
            </td>
            <td>
            <?php $sumq2=array($qty21,$qty22,$qty23,$qty24,$qty25,$qty26,$qty27,$qty28,$qty29,$qty210,$qty211,$qty212,$qty213,$qty214,$qty215,$qty216,$qty217,$qty218,$qty219,$qty2110,$qty2111,$qty2112,$qty2113,$qty2114);
                if (array_sum($sumq2)!=0) {
                    echo $sumq2 = array_sum($sumq2);
                }
            ?>
            </td>
            <td>
            <?php $sumq3=array($qty31,$qty32,$qty33,$qty34,$qty35,$qty36,$qty37,$qty38,$qty39,$qty310,$qty311,$qty312,$qty313,$qty314,$qty315,$qty316,$qty317,$qty318,$qty319,$qty3110,$qty3111,$qty3112,$qty3113,$qty3114);
                if (array_sum($sumq3)!=0) {
                    echo $sumq3 = array_sum($sumq3);
                }
            ?>
            </td>
            <td>
            <?php $sumq4=array($qty41,$qty42,$qty43,$qty44,$qty45,$qty46,$qty47,$qty48,$qty49,$qty410,$qty411,$qty412,$qty413,$qty414,$qty415,$qty416,$qty417,$qty418,$qty419,$qty4110,$qty4111,$qty4112,$qty4113,$qty4114);
                if (array_sum($sumq4)!=0) {
                    echo $sumq4 = array_sum($sumq4);
                }
            ?>
            </td>
            <td>
            <?php
                foreach ($datajson as $value) {
                //$sum = 0;
                $rows1 = array($value->Q1,$value->Q2,$value->Q3,$value->Q4);
                $QQ1 = array_sum($rows1);
                $sumrows1 += $QQ1;
                }
                if ($sumrows1!=0) {
                    echo $sumrows1;
                }
            ?>
            </td>
            <td>
            <?php $sumq_all=array($sumq1,$sumq2,$sumq3,$sumq4);
                if (array_sum($sumq_all)!=0) {
                    $sumq_all = array_sum($sumq_all);
                    if($sumtop == "" || $sumtop == 0)
                    {
                      $sumper = 0;
                    }
                    else {
                    $sumper = ($sumq_all/$sumtop)*100;

                    }
                    echo $sumper_format_number = number_format($sumper, 2, '.', '');
                }
            ?>
            </td>
            <td class="f10"><br><b>สรุปการผลิต</b></td>
            <td colspan="4" align="left" class="f10">
                <br><b>Cure : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            if ($sumrows1!=0) {
                echo $sumrows1;
            }
            ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;เส้น</b>
            </td>
        </tr>
        <tr>
            <td colspan="17" class="f10" valign="bottom"><br><br><b>ผู้ตรวจสอบ : ......................................................................
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            ผู้อนุมัติ : ......................................................................</b></td>
        </tr>
    </table>
    <table class="table">
        <tr class="tr">
            <td class="td" align="left">
                Ref.WI-PP-2.12
            </td>
            <td class="td" align="right">
                FM-PP-2.12.3,Issue #1
            </td>
        </tr>
    </table>
    <table >
        <tr>
            <td colspan="5">
                <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
                style="padding-left:10px;height:55px; width:auto;" /></a>
            </td>
            <td align="center" colspan="12" class="f12">
                <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>CURING REPORT</b>
            </td>
        </tr>
        <tr>
            <td rowspan="2" text-rotate="90" class="f10"><b>รายชื่อ</b></td>
            <td colspan="16" class="f10"><br>
            <b>DATE : <?php echo $datecuring; ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>SHIFT : <?php if($shift=="day"){ echo "กลางวัน"; }else{ echo "กลางคืน";} ?></b>
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
            <td  width="5%"><br><b>Cure code</b></td>
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
            <td width="3%"><br><b>%</b></td>
            <td width="7%"><br><b>Press</b></td>
            <td width="5%"><br><b>TimeOn</b></td>
            <td width="5%"><br><b>TimeOff</b></td>
            <td width="5%"><br><b>TotalTime</b></td>
            <td><br><b>Causes of down time</b></td>
        </tr>

        <tr text-rotate="90">
            <td rowspan="9" text-rotate="90" class="f10">
                <?php
                // $ids=array();
                // foreach ($dataname_sec1 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
                ?>
            </td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b01_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b01_sec && $value['PressSide'] === 'L') {
                                $code01L_sec .= $value['CuringCode'].",";
                                $top01L_sec .= $value['rate12'].",";
                                $q1_text01L_sec .= $value['Q1'].",";
                                $q2_text01L_sec .= $value['Q2'].",";
                                $q3_text01L_sec .= $value['Q3'].",";
                                $q4_text01L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code01L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top01L_sec = trim($top01L_sec, ",");
                $top01L_sec = explode(",", $top01L_sec);
                echo $top01L_sec = $top01L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text01L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text01L_sec, ","); ?></td>
            <td><?php echo trim($q3_text01L_sec, ","); ?></td>
            <td><?php echo trim($q4_text01L_sec, ","); ?></td>
            <td><?php $rows1_sec=array($qty11_sec,$qty21_sec,$qty31_sec,$qty41_sec);
                if (array_sum($rows1_sec)!=0) {
                    echo $rows1_sec_new = array_sum($rows1_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows1_sec_new)) {
                $newrate = ($rows1_sec_new/$top01L_sec)*100;
                echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b01_sec && $value['PressSide'] === 'R') {
                                $code01R_sec .= $value['CuringCode'].",";
                                $top01R_sec .= $value['rate12'].",";
                                $q1_text01R_sec .= $value['Q1'].",";
                                $q2_text01R_sec .= $value['Q2'].",";
                                $q3_text01R_sec .= $value['Q3'].",";
                                $q4_text01R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code01R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top01R_sec = trim($top01R_sec, ",");
                $top01R_sec = explode(",", $top01R_sec);
                echo $top01R_sec = $top01R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text01R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text01R_sec, ","); ?></td>
            <td><?php echo trim($q3_text01R_sec, ","); ?></td>
            <td><?php echo trim($q4_text01R_sec, ","); ?></td>
            <td><?php $rows2_sec=array($qty12_sec,$qty22_sec,$qty32_sec,$qty42_sec);
                if (array_sum($rows2_sec)!=0) {
                    echo $rows2_sec_new = array_sum($rows2_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows2_sec_new)) {
                $newrate = ($rows2_sec_new/$top01R_sec)*100;
                echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b02_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b02_sec && $value['PressSide'] === 'L') {
                                $code02L_sec .= $value['CuringCode'].",";
                                $top02L_sec .= $value['rate12'].",";
                                $q1_text02L_sec .= $value['Q1'].",";
                                $q2_text02L_sec .= $value['Q2'].",";
                                $q3_text02L_sec .= $value['Q3'].",";
                                $q4_text02L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code02L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top02L_sec = trim($top02L_sec, ",");
                $top02L_sec = explode(",", $top02L_sec);
                echo $top02L_sec = $top02L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text02L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text02L_sec, ","); ?></td>
            <td><?php echo trim($q3_text02L_sec, ","); ?></td>
            <td><?php echo trim($q4_text02L_sec, ","); ?></td>
            <td><?php $rows3_sec=array($qty13_sec,$qty23_sec,$qty33_sec,$qty43_sec);
                if (array_sum($rows3_sec)!=0) {
                    echo $rows3_sec_new = array_sum($rows3_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows3_sec_new)) {
            $newrate = ($rows3_sec_new/$top02L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b02_sec && $value['PressSide'] === 'R') {
                                $code02R_sec .= $value['CuringCode'].",";
                                $top02R_sec .= $value['rate12'].",";
                                $q1_text02R_sec .= $value['Q1'].",";
                                $q2_text02R_sec .= $value['Q2'].",";
                                $q3_text02R_sec .= $value['Q3'].",";
                                $q4_text02R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code02R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top02R_sec = trim($top02R_sec, ",");
                $top02R_sec = explode(",", $top02R_sec);
                echo $top02R_sec = $top02R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text02R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text02R_sec, ","); ?></td>
            <td><?php echo trim($q3_text02R_sec, ","); ?></td>
            <td><?php echo trim($q4_text02R_sec, ","); ?></td>
            <td><?php $rows4_sec=array($qty14_sec,$qty24_sec,$qty34_sec,$qty44_sec);
                if (array_sum($rows4_sec)!=0) {
                    echo $rows4_sec_new =  array_sum($rows4_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows4_sec_new)) {
            $newrate = ($rows4_sec_new/$top02R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b03_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b03_sec && $value['PressSide'] === 'L') {
                                $code03L_sec .= $value['CuringCode'].",";
                                $top03L_sec .= $value['rate12'].",";
                                $q1_text03L_sec .= $value['Q1'].",";
                                $q2_text03L_sec .= $value['Q2'].",";
                                $q3_text03L_sec .= $value['Q3'].",";
                                $q4_text03L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code03L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top03L_sec = trim($top03L_sec, ",");
                $top03L_sec = explode(",", $top03L_sec);
                echo $top03L_sec = $top03L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text03L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text03L_sec, ","); ?></td>
            <td><?php echo trim($q3_text03L_sec, ","); ?></td>
            <td><?php echo trim($q4_text03L_sec, ","); ?></td>
           <td><?php $rows5_sec=array($qty15_sec,$qty25_sec,$qty35_sec,$qty45_sec);
                if (array_sum($rows5_sec)!=0) {
                    echo $rows5_sec_new =  array_sum($rows5_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows5_sec_new)) {
            $newrate = ($rows5_sec_new/$top03L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b03_sec && $value['PressSide'] === 'R') {
                                $code03R_sec .= $value['CuringCode'].",";
                                $top03R_sec .= $value['rate12'].",";
                                $q1_text03R_sec .= $value['Q1'].",";
                                $q2_text03R_sec .= $value['Q2'].",";
                                $q3_text03R_sec .= $value['Q3'].",";
                                $q4_text03R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code03R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top03R_sec = trim($top03R_sec, ",");
                $top03R_sec = explode(",", $top03R_sec);
                echo $top03R_sec = $top03R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text03R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text03R_sec, ","); ?></td>
            <td><?php echo trim($q3_text03R_sec, ","); ?></td>
            <td><?php echo trim($q4_text03R_sec, ","); ?></td>
            <td><?php $rows6_sec=array($qty16_sec,$qty26_sec,$qty36_sec,$qty46_sec);
                if (array_sum($rows6_sec)!=0) {
                    echo $rows6_sec_new =  array_sum($rows6_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows6_sec_new)) {
            $newrate = ($rows6_sec_new/$top03R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b04_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b04_sec && $value['PressSide'] === 'L') {
                                $code04L_sec .= $value['CuringCode'].",";
                                $top04L_sec .= $value['rate12'].",";
                                $q1_text04L_sec .= $value['Q1'].",";
                                $q2_text04L_sec .= $value['Q2'].",";
                                $q3_text04L_sec .= $value['Q3'].",";
                                $q4_text04L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code04L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top04L_sec = trim($top04L_sec, ",");
                $top04L_sec = explode(",", $top04L_sec);
                echo $top04L_sec = $top04L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text04L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text04L_sec, ","); ?></td>
            <td><?php echo trim($q3_text04L_sec, ","); ?></td>
            <td><?php echo trim($q4_text04L_sec, ","); ?></td>
            <td><?php $rows7_sec=array($qty17_sec,$qty27_sec,$qty37_sec,$qty47_sec);
                if (array_sum($rows7_sec)!=0) {
                    echo $rows7_sec_new =  array_sum($rows7_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows7_sec_new)) {
            $newrate = ($rows7_sec_new/$top04L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b04_sec && $value['PressSide'] === 'R') {
                                $code04R_sec .= $value['CuringCode'].",";
                                $top04R_sec .= $value['rate12'].",";
                                $q1_text04R_sec .= $value['Q1'].",";
                                $q2_text04R_sec .= $value['Q2'].",";
                                $q3_text04R_sec .= $value['Q3'].",";
                                $q4_text04R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code04R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top04R_sec = trim($top04R_sec, ",");
                $top04R_sec = explode(",", $top04R_sec);
                echo $top04R_sec = $top04R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text04R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text04R_sec, ","); ?></td>
            <td><?php echo trim($q3_text04R_sec, ","); ?></td>
            <td><?php echo trim($q4_text04R_sec, ","); ?></td>
            <td><?php $rows8_sec=array($qty18_sec,$qty28_sec,$qty38_sec,$qty48_sec);
                if (array_sum($rows8_sec)!=0) {
                    echo $rows8_sec_new =  array_sum($rows8_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows8_sec_new)) {
            $newrate = ($rows8_sec_new/$top04R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                // $ids=array();
                // foreach ($dataname_sec2 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
                ?>
            </td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b05_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b05_sec && $value['PressSide'] === 'L') {
                                $code05L_sec .= $value['CuringCode'].",";
                                $top05L_sec .= $value['rate12'].",";
                                $q1_text05L_sec .= $value['Q1'].",";
                                $q2_text05L_sec .= $value['Q2'].",";
                                $q3_text05L_sec .= $value['Q3'].",";
                                $q4_text05L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code05L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top05L_sec = trim($top05L_sec, ",");
                $top05L_sec = explode(",", $top05L_sec);
                echo $top05L_sec = $top05L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text05L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text05L_sec, ","); ?></td>
            <td><?php echo trim($q3_text05L_sec, ","); ?></td>
            <td><?php echo trim($q4_text05L_sec, ","); ?></td>
            <td><?php $rows9_sec=array($qty19_sec,$qty29_sec,$qty39_sec,$qty49_sec);
                if (array_sum($rows9_sec)!=0) {
                    echo $rows9_sec_new =  array_sum($rows9_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows9_sec_new)) {
            $newrate = ($rows9_sec_new/$top05L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b05_sec && $value['PressSide'] === 'R') {
                                $code05R_sec .= $value['CuringCode'].",";
                                $top05R_sec .= $value['rate12'].",";
                                $q1_text05R_sec .= $value['Q1'].",";
                                $q2_text05R_sec .= $value['Q2'].",";
                                $q3_text05R_sec .= $value['Q3'].",";
                                $q4_text05R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code05R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top05R_sec = trim($top05R_sec, ",");
                $top05R_sec = explode(",", $top05R_sec);
                echo $top05R_sec = $top05R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text05R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text05R_sec, ","); ?></td>
            <td><?php echo trim($q3_text05R_sec, ","); ?></td>
            <td><?php echo trim($q4_text05R_sec, ","); ?></td>
            <td><?php $rows10_sec=array($qty110_sec,$qty210_sec,$qty310_sec,$qty410_sec);
                if (array_sum($rows10_sec)!=0) {
                    echo $rows10_sec_new =  array_sum($rows10_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows10_sec_new)) {
            $newrate = ($rows10_sec_new/$top05R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b06_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b06_sec && $value['PressSide'] === 'L') {
                                $code06L_sec .= $value['CuringCode'].",";
                                $top06L_sec .= $value['rate12'].",";
                                $q1_text06L_sec .= $value['Q1'].",";
                                $q2_text06L_sec .= $value['Q2'].",";
                                $q3_text06L_sec .= $value['Q3'].",";
                                $q4_text06L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code06L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top06L_sec = trim($top06L_sec, ",");
                $top06L_sec = explode(",", $top06L_sec);
                echo $top06L_sec = $top06L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text06L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text06L_sec, ","); ?></td>
            <td><?php echo trim($q3_text06L_sec, ","); ?></td>
            <td><?php echo trim($q4_text06L_sec, ","); ?></td>
            <td><?php $rows11_sec=array($qty111_sec,$qty211_sec,$qty311_sec,$qty411_sec);
                if (array_sum($rows11_sec)!=0) {
                    echo $rows11_sec_new =  array_sum($rows11_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows11_sec_new)) {
            $newrate = ($rows11_sec_new/$top06L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b06_sec && $value['PressSide'] === 'R') {
                                $code06R_sec .= $value['CuringCode'].",";
                                $top06R_sec .= $value['rate12'].",";
                                $q1_text06R_sec .= $value['Q1'].",";
                                $q2_text06R_sec .= $value['Q2'].",";
                                $q3_text06R_sec .= $value['Q3'].",";
                                $q4_text06R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code06R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top06R_sec = trim($top06R_sec, ",");
                $top06R_sec = explode(",", $top06R_sec);
                echo $top06R_sec = $top06R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text06R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text06R_sec, ","); ?></td>
            <td><?php echo trim($q3_text06R_sec, ","); ?></td>
            <td><?php echo trim($q4_text06R_sec, ","); ?></td>
            <td><?php $rows12_sec=array($qty112_sec,$qty212_sec,$qty312_sec,$qty412_sec);
                if (array_sum($rows12_sec)!=0) {
                    echo $rows12_sec_new =  array_sum($rows12_sec);
                }?>
            </td>
            <td>
            <?php if (isset($rows12_sec_new)) {
            $newrate = ($rows12_sec_new/$top06R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b07_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b07_sec && $value['PressSide'] === 'L') {
                                $code07L_sec .= $value['CuringCode'].",";
                                $top07L_sec .= $value['rate12'].",";
                                $q1_text07L_sec .= $value['Q1'].",";
                                $q2_text07L_sec .= $value['Q2'].",";
                                $q3_text07L_sec .= $value['Q3'].",";
                                $q4_text07L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code07L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top07L_sec = trim($top07L_sec, ",");
                $top07L_sec = explode(",", $top07L_sec);
                echo $top07L_sec = $top07L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text07L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text07L_sec, ","); ?></td>
            <td><?php echo trim($q3_text07L_sec, ","); ?></td>
            <td><?php echo trim($q4_text07L_sec, ","); ?></td>
            <td><?php $rows13_sec=array($qty113_sec,$qty213_sec,$qty313_sec,$qty413_sec);
                if (array_sum($rows13_sec)!=0) {
                    echo $rows13_sec_new =  array_sum($rows13_sec);
                }?>
            <td>
            <?php if (isset($rows13_sec_new)) {
            $newrate = ($rows13_sec_new/$top07L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b07_sec && $value['PressSide'] === 'R') {
                                $code07R_sec .= $value['CuringCode'].",";
                                $top07R_sec .= $value['rate12'].",";
                                $q1_text07R_sec .= $value['Q1'].",";
                                $q2_text07R_sec .= $value['Q2'].",";
                                $q3_text07R_sec .= $value['Q3'].",";
                                $q4_text07R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code07R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top07R_sec = trim($top07R_sec, ",");
                $top07R_sec = explode(",", $top07R_sec);
                echo $top07R_sec = $top07R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text07R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text07R_sec, ","); ?></td>
            <td><?php echo trim($q3_text07R_sec, ","); ?></td>
            <td><?php echo trim($q4_text07R_sec, ","); ?></td>
            <td><?php $rows14_sec=array($qty114_sec,$qty214_sec,$qty314_sec,$qty414_sec);
                if (array_sum($rows14_sec)!=0) {
                    echo $rows14_sec_new =  array_sum($rows14_sec);
                }?>
            <td>
            <?php if (isset($rows14_sec_new)) {
            $newrate = ($rows14_sec_new/$top07R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b08_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b08_sec && $value['PressSide'] === 'L') {
                                $code08L_sec .= $value['CuringCode'].",";
                                $top08L_sec .= $value['rate12'].",";
                                $q1_text08L_sec .= $value['Q1'].",";
                                $q2_text08L_sec .= $value['Q2'].",";
                                $q3_text08L_sec .= $value['Q3'].",";
                                $q4_text08L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code08L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top08L_sec = trim($top08L_sec, ",");
                $top08L_sec = explode(",", $top08L_sec);
                echo $top08L_sec = $top08L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text08L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text08L_sec, ","); ?></td>
            <td><?php echo trim($q3_text08L_sec, ","); ?></td>
            <td><?php echo trim($q4_text08L_sec, ","); ?></td>
            <td><?php $rows15_sec=array($qty115_sec,$qty215_sec,$qty315_sec,$qty415_sec);
                if (array_sum($rows15_sec)!=0) {
                    echo $rows15_sec_new =  array_sum($rows15_sec);
                }?>
            <td>
            <?php if (isset($rows15_sec_new)) {
            $newrate = ($rows15_sec_new/$top08L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b08_sec && $value['PressSide'] === 'R') {
                                $code08R_sec .= $value['CuringCode'].",";
                                $top08R_sec .= $value['rate12'].",";
                                $q1_text08R_sec .= $value['Q1'].",";
                                $q2_text08R_sec .= $value['Q2'].",";
                                $q3_text08R_sec .= $value['Q3'].",";
                                $q4_text08R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code08R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top08R_sec = trim($top08R_sec, ",");
                $top08R_sec = explode(",", $top08R_sec);
                echo $top08R_sec = $top08R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text08R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text08R_sec, ","); ?></td>
            <td><?php echo trim($q3_text08R_sec, ","); ?></td>
            <td><?php echo trim($q4_text08R_sec, ","); ?></td>
            <<td><?php $rows16_sec=array($qty116_sec,$qty216_sec,$qty316_sec,$qty416_sec);
                if (array_sum($rows16_sec)!=0) {
                    echo $rows16_sec_new =  array_sum($rows16_sec);
                }?>
            <td>
            <?php if (isset($rows16_sec_new)) {
            $newrate = ($rows16_sec_new/$top08R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                // $ids=array();
                // foreach ($dataname_sec3 as $value) {
                //     if ($value!=='' && $value !==null) {
                //         $ids[]=$value;
                //     }
                // }
                // echo implode(",", $ids);
                ?>
            </td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b09_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b09_sec && $value['PressSide'] === 'L') {
                                $code09L_sec .= $value['CuringCode'].",";
                                $top09L_sec .= $value['rate12'].",";
                                $q1_text09L_sec .= $value['Q1'].",";
                                $q2_text09L_sec .= $value['Q2'].",";
                                $q3_text09L_sec .= $value['Q3'].",";
                                $q4_text09L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code09L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top09L_sec = trim($top09L_sec, ",");
                $top09L_sec = explode(",", $top09L_sec);
                echo $top09L_sec = $top09L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text09L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text09L_sec, ","); ?></td>
            <td><?php echo trim($q3_text09L_sec, ","); ?></td>
            <td><?php echo trim($q4_text09L_sec, ","); ?></td>
            <td><?php $rows17_sec=array($qty117_sec,$qty217_sec,$qty317_sec,$qty417_sec);
                if (array_sum($rows17_sec)!=0) {
                    echo $rows17_sec_new =  array_sum($rows17_sec);
                }?>
            <td>
            <?php if (isset($rows17_sec_new)) {
            $newrate = ($rows17_sec_new/$top09L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b09_sec && $value['PressSide'] === 'R') {
                                $code09R_sec .= $value['CuringCode'].",";
                                $top09R_sec .= $value['rate12'].",";
                                $q1_text09R_sec .= $value['Q1'].",";
                                $q2_text09R_sec .= $value['Q2'].",";
                                $q3_text09R_sec .= $value['Q3'].",";
                                $q4_text09R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code09R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top09R_sec = trim($top09R_sec, ",");
                $top09R_sec = explode(",", $top09R_sec);
                echo $top09R_sec = $top09R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text09R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text09R_sec, ","); ?></td>
            <td><?php echo trim($q3_text09R_sec, ","); ?></td>
            <td><?php echo trim($q4_text09R_sec, ","); ?></td>
            <td><?php $rows18_sec=array($qty118_sec,$qty218_sec,$qty318_sec,$qty418_sec);
                if (array_sum($rows18_sec)!=0) {
                    echo $rows18_sec_new =  array_sum($rows18_sec);
                }?>
            <td>
            <?php if (isset($rows18_sec_new)) {
            $newrate = ($rows18_sec_new/$top09R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b10_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b10_sec && $value['PressSide'] === 'L') {
                                $code10L_sec .= $value['CuringCode'].",";
                                $top10L_sec .= $value['rate12'].",";
                                $q1_text10L_sec .= $value['Q1'].",";
                                $q2_text10L_sec .= $value['Q2'].",";
                                $q3_text10L_sec .= $value['Q3'].",";
                                $q4_text10L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code10L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top10L_sec = trim($top10L_sec, ",");
                $top10L_sec = explode(",", $top10L_sec);
                echo $top10L_sec = $top10L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text10L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text10L_sec, ","); ?></td>
            <td><?php echo trim($q3_text10L_sec, ","); ?></td>
            <td><?php echo trim($q4_text10L_sec, ","); ?></td>
            <td><?php $rows19_sec=array($qty119_sec,$qty219_sec,$qty319_sec,$qty419_sec);
                if (array_sum($rows19_sec)!=0) {
                    echo $rows19_sec_new =  array_sum($rows19_sec);
                }?>
            <td>
            <?php if (isset($rows19_sec_new)) {
            $newrate = ($rows19_sec_new/$top10L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b10_sec && $value['PressSide'] === 'R') {
                                $code10R_sec .= $value['CuringCode'].",";
                                $top10R_sec .= $value['rate12'].",";
                                $q1_text10R_sec .= $value['Q1'].",";
                                $q2_text10R_sec .= $value['Q2'].",";
                                $q3_text10R_sec .= $value['Q3'].",";
                                $q4_text10R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code10R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top10R_sec = trim($top10R_sec, ",");
                $top10R_sec = explode(",", $top10R_sec);
                echo $top10R_sec = $top10R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text10R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text10R_sec, ","); ?></td>
            <td><?php echo trim($q3_text10R_sec, ","); ?></td>
            <td><?php echo trim($q4_text10R_sec, ","); ?></td>
            <td><?php $rows20_sec=array($qty1110_sec,$qty2110_sec,$qty3110_sec,$qty4110_sec);
                if (array_sum($rows20_sec)!=0) {
                    echo $rows20_sec_new =  array_sum($rows20_sec);
                }?>
            <td>
            <?php if (isset($rows20_sec_new)) {
            $newrate = ($rows20_sec_new/$top10R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b11_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b11_sec && $value['PressSide'] === 'L') {
                                $code11L_sec .= $value['CuringCode'].",";
                                $top11L_sec .= $value['rate12'].",";
                                $q1_text11L_sec .= $value['Q1'].",";
                                $q2_text11L_sec .= $value['Q2'].",";
                                $q3_text11L_sec .= $value['Q3'].",";
                                $q4_text11L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code11L_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top11L_sec = trim($top11L_sec, ",");
                $top11L_sec = explode(",", $top11L_sec);
                echo $top11L_sec = $top11L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text11L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text11L_sec, ","); ?></td>
            <td><?php echo trim($q3_text11L_sec, ","); ?></td>
            <td><?php echo trim($q4_text11L_sec, ","); ?></td>
            <td><?php $rows21_sec=array($qty1111_sec,$qty2111_sec,$qty3111_sec,$qty4111_sec);
                if (array_sum($rows21_sec)!=0) {
                    echo $rows21_sec_new =  array_sum($rows21_sec);
                }?>
            <td>
            <?php if (isset($rows21_sec_new)) {
            $newrate = ($rows21_sec_new/$top11L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b11_sec && $value['PressSide'] === 'R') {
                                $code11R_sec .= $value['CuringCode'].",";
                                $top11R_sec .= $value['rate12'].",";
                                $q1_text11R_sec .= $value['Q1'].",";
                                $q2_text11R_sec .= $value['Q2'].",";
                                $q3_text11R_sec .= $value['Q3'].",";
                                $q4_text11R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code11R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top11R_sec = trim($top11R_sec, ",");
                $top11R_sec = explode(",", $top11R_sec);
                echo $top11R_sec = $top11R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text11R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text11R_sec, ","); ?></td>
            <td><?php echo trim($q3_text11R_sec, ","); ?></td>
            <td><?php echo trim($q4_text11R_sec, ","); ?></td>
            <td><?php $rows22_sec=array($qty1112_sec,$qty2112_sec,$qty3112_sec,$qty4112_sec);
                if (array_sum($rows22_sec)!=0) {
                    echo $rows22_sec_new =  array_sum($rows22_sec);
                }?>
            <td>
            <?php if (isset($rows22_sec_new)) {
            $newrate = ($rows22_sec_new/$top11R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b12_sec; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b12_sec && $value['PressSide'] === 'L') {
                                $code12L_sec .= $value['CuringCode'].",";
                                $top12L_sec .= $value['rate12'].",";
                                $q1_text12L_sec .= $value['Q1'].",";
                                $q2_text12L_sec .= $value['Q2'].",";
                                $q3_text12L_sec .= $value['Q3'].",";
                                $q4_text12L_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code12L_sec, ",");
                ?>
            </td>
            <td>
               <?php
                $top12L_sec = trim($top12L_sec, ",");
                $top12L_sec = explode(",", $top12L_sec);
                echo $top12L_sec = $top12L_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text12L_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text12L_sec, ","); ?></td>
            <td><?php echo trim($q3_text12L_sec, ","); ?></td>
            <td><?php echo trim($q4_text12L_sec, ","); ?></td>
            <td><?php $rows23_sec=array($qty1113_sec,$qty2113_sec,$qty3113_sec,$qty4113_sec);
                if (array_sum($rows23_sec)!=0) {
                    echo $rows23_sec_new =  array_sum($rows23_sec);
                }?>
            <td>
            <?php if (isset($rows23_sec_new)) {
            $newrate = ($rows23_sec_new/$top12L_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                        foreach ($datajsonQ2 as $key => $value) {
                            if ($value['PressNo'] === $b12_sec && $value['PressSide'] === 'R') {
                                $code12R_sec .= $value['CuringCode'].",";
                                $top12R_sec .= $value['rate12'].",";
                                $q1_text12R_sec .= $value['Q1'].",";
                                $q2_text12R_sec .= $value['Q2'].",";
                                $q3_text12R_sec .= $value['Q3'].",";
                                $q4_text12R_sec .= $value['Q4'].",";
                            }
                        }
                    echo trim($code12R_sec, ",");
                ?>
            </td>
            <td>
                <?php
                $top12R_sec = trim($top12R_sec, ",");
                $top12R_sec = explode(",", $top12R_sec);
                echo $top12R_sec = $top12R_sec[0];
                ?>
            </td>
            <td>
                <?php echo trim($q1_text12R_sec, ","); ?>
            </td>
            <td><?php echo trim($q2_text12R_sec, ","); ?></td>
            <td><?php echo trim($q3_text12R_sec, ","); ?></td>
            <td><?php echo trim($q4_text12R_sec, ","); ?></td>
            <td><?php $rows24_sec=array($qty1114_sec,$qty2114_sec,$qty3114_sec,$qty4114_sec);
                if (array_sum($rows24_sec)!=0) {
                    echo $rows24_sec_new =  array_sum($rows24_sec);
                }?>
            <td>
            <?php if (isset($rows24_sec_new)) {
            $newrate = ($rows24_sec_new/$top12R_sec)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td colspan="2" class="f10"><br>
                <b>Total</b>
            </td>
            <td></td>
            <td></td>
            <td>
            <?php $sumtop_sec=array($top01L_sec,$top01R_sec,$top02L_sec,$top02R_sec,$top03L_sec,$top03R_sec,$top04L_sec,$top04R_sec,$top05L_sec,$top05R_sec,$top06L_sec,$top06R_sec,$top07L_sec,$top07R_sec,$top08L_sec,$top08R_sec,$top09L_sec,$top09R_sec,$top10L_sec,$top10R_sec,$top11L_sec,$top11R_sec,$top12L_sec,$top12R_sec);
                if (array_sum($sumtop_sec)!=0) {
                    echo $sumtop_sec = array_sum($sumtop_sec);
                }
            ?>
            </td>
            <td>
            <?php $sumq1_sec=array($qty11_sec,$qty12_sec,$qty13_sec,$qty14_sec,$qty15_sec,$qty16_sec,$qty17_sec,$qty18_sec,$qty19_sec,$qty110_sec,$qty111_sec,$qty112_sec,$qty113_sec,$qty114_sec,$qty115_sec,$qty116_sec,$qty117_sec,$qty118_sec,$qty119_sec,$qty1110_sec,$qty1111_sec,$qty1112_sec,$qty1113_sec,$qty1114_sec);
                if (array_sum($sumq1_sec)!=0) {
                    echo $sumq1_sec = array_sum($sumq1_sec);
                }
            ?>
            </td>
            <td>
            <?php $sumq2_sec=array($qty21_sec,$qty22_sec,$qty23_sec,$qty24_sec,$qty25_sec,$qty26_sec,$qty27_sec,$qty28_sec,$qty29_sec,$qty210_sec,$qty211_sec,$qty212_sec,$qty213_sec,$qty214_sec,$qty215_sec,$qty216_sec,$qty217_sec,$qty218_sec,$qty219_sec,$qty2110_sec,$qty2111_sec,$qty2112_sec,$qty2113_sec,$qty2114_sec);
                if (array_sum($sumq2_sec)!=0) {
                    echo $sumq2_sec = array_sum($sumq2_sec);
                }
            ?>
            </td>
            <td>
            <?php $sumq3_sec=array($qty31_sec,$qty32_sec,$qty33_sec,$qty34_sec,$qty35_sec,$qty36_sec,$qty37_sec,$qty38_sec,$qty39_sec,$qty310_sec,$qty311_sec,$qty312_sec,$qty313_sec,$qty314_sec,$qty315_sec,$qty316_sec,$qty317_sec,$qty318_sec,$qty319_sec,$qty3110_sec,$qty3111_sec,$qty3112_sec,$qty3113_sec,$qty3114_sec);
                if (array_sum($sumq3_sec)!=0) {
                    echo $sumq3_sec = array_sum($sumq3_sec);
                }
            ?>
            </td>
            <td>
            <?php $sumq4_sec=array($qty41_sec,$qty42_sec,$qty43_sec,$qty44_sec,$qty45_sec,$qty46_sec,$qty47_sec,$qty48_sec,$qty49_sec,$qty410_sec,$qty411_sec,$qty412_sec,$qty413_sec,$qty414_sec,$qty415_sec,$qty416_sec,$qty417_sec,$qty418_sec,$qty419_sec,$qty4110_sec,$qty4111_sec,$qty4112_sec,$qty4113_sec,$qty4114_sec);
                if (array_sum($sumq4_sec)!=0) {
                    echo $sumq4_sec = array_sum($sumq4_sec);
                }
            ?>
            </td>
            <td>
            <?php
                foreach ($datajson2 as $value) {
                $sum = 0;
                $rows2 = array($value->Q1,$value->Q2,$value->Q3,$value->Q4);
                $QQ2 = array_sum($rows2);
                $sumrows2 += $QQ2;
                }
                if ($sumrows2!=0) {
                   echo $sumrows2;
                }

            ?>
            </td>
            <td>
            <?php $sumq_all_sec=array($sumq1_sec,$sumq2_sec,$sumq3_sec,$sumq4_sec);
                if (array_sum($sumq_all_sec)!=0) {
                    $sumq_all_sec = array_sum($sumq_all_sec);
                    $sumper_sec = ($sumq_all_sec/$sumtop_sec)*100;
                    echo $sumper_sec_format_number = number_format($sumper_sec, 2, '.', '');
                }
            ?>
            </td>
            <td class="f10"><br><b>สรุปการผลิต</b></td>
            <td colspan="4" align="left" class="f10">
                <br><b>Cure : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
                if ($sumrows2!=0) {
                    echo $sumrows2;
                }

            ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;เส้น</b>
            </td>
        </tr>
        <tr>
            <td colspan="17" class="f10" valign="bottom"><br><br><b>ผู้ตรวจสอบ : ......................................................................
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            ผู้อนุมัติ : ......................................................................</b></td>
        </tr>
    </table>
    <table class="table">
        <tr class="tr">
            <td class="td" align="left">
                Ref.WI-PP-2.12
            </td>
            <td class="td" align="right">
                FM-PP-2.12.3,Issue #1
            </td>
        </tr>
    </table>
<?php }else if(isset($press1)){ ?>
    <table >
        <tr>
            <td colspan="5">
                <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
                style="padding-left:10px;height:55px; width:auto;" /></a>
            </td>
            <td align="center" colspan="12" class="f12">
                <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>CURING REPORT</b>
            </td>
        </tr>
        <tr>
            <td rowspan="2" text-rotate="90" class="f10"><b>รายชื่อ</b></td>
            <td colspan="16" class="f10"><br>
            <b>DATE : <?php echo $datecuring; ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>SHIFT : <?php if($shift=="day"){ echo "กลางวัน"; }else{ echo "กลางคืน";} ?></b>
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
            <td width="7%"><br><b>Press</b></td>
            <td width="5%"><br><b>TimeOn</b></td>
            <td width="5%"><br><b>TimeOff</b></td>
            <td width="5%"><br><b>TotalTime</b></td>
            <td><br><b>Causes of down time</b></td>
        </tr>
        <tr text-rotate="90">
            <td rowspan="9" text-rotate="90" class="f10">
                <?php
                // $nx = "";
                // foreach ($dataname1 as $value) {
                //     $nx .= $value['Name'].",";
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
                                $code01L .= $value['CuringCode'].",";
                                $top01L .= $value['rate12'].",";
                                $q1_text01L .= $value['Q1'].",";
                                $q2_text01L .= $value['Q2'].",";
                                $q3_text01L .= $value['Q3'].",";
                                $q4_text01L .= $value['Q4'].",";
                            }
                        }
                    echo trim($code01L, ",");
                ?>
            </td>
              <td <?php if(trim($top01L, ",")=='0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows1=array($qty11,$qty21,$qty31,$qty41);
                if (array_sum($rows1)!=0) {
                    echo $rows1_new = array_sum($rows1);
                }?>
            </td>
            <td>
            <?php if (isset($rows1_new) && $top01L!=0) {
            $newrate = ($rows1_new/$top01L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code01R .= $value['CuringCode'].",";
                                $top01R .= $value['rate12'].",";
                                $q1_text01R .= $value['Q1'].",";
                                $q2_text01R .= $value['Q2'].",";
                                $q3_text01R .= $value['Q3'].",";
                                $q4_text01R .= $value['Q4'].",";
                            }
                        }
                    echo trim($code01R, ",");
                ?>
            </td>
            <td <?php if(trim($top01R, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows2=array($qty12,$qty22,$qty32,$qty42);
                if (array_sum($rows2)!=0) {
                    echo $rows2_new = array_sum($rows2);
                }?>
            </td>
            <td>
            <?php if (isset($rows2_new) && $top01R!=0) {
            $newrate = ($rows2_new/$top01R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code02L .= $value['CuringCode'].",";
                                $top02L .= $value['rate12'].",";
                                $q1_text02L .= $value['Q1'].",";
                                $q2_text02L .= $value['Q2'].",";
                                $q3_text02L .= $value['Q3'].",";
                                $q4_text02L .= $value['Q4'].",";
                            }
                        }
                    echo trim($code02L, ",");
                ?>
            </td>
              <td <?php if(trim($top02L, ",")=='0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows3=array($qty13,$qty23,$qty33,$qty43);
                if (array_sum($rows3)!=0) {
                    echo $rows3_new = array_sum($rows3);
                }?>
            </td>
            <td>
            <?php if (isset($rows3_new) && $top02L!=0) {
            $newrate = ($rows3_new/$top02L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code02R .= $value['CuringCode'].",";
                                $top02R .= $value['rate12'].",";
                                $q1_text02R .= $value['Q1'].",";
                                $q2_text02R .= $value['Q2'].",";
                                $q3_text02R .= $value['Q3'].",";
                                $q4_text02R .= $value['Q4'].",";
                            }
                        }
                    echo trim($code02R, ",");
                ?>
            </td>
            <td <?php if(trim($top02R, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows4=array($qty14,$qty24,$qty34,$qty44);
                if (array_sum($rows4)!=0) {
                    echo $rows4_new = array_sum($rows4);
                }?>
            </td>
            <td>
            <?php if (isset($rows4_new) && $top02R!=0) {
            $newrate = ($rows4_new/$top02R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code03L .= $value['CuringCode'].",";
                                $top03L .= $value['rate12'].",";
                                $q1_text03L .= $value['Q1'].",";
                                $q2_text03L .= $value['Q2'].",";
                                $q3_text03L .= $value['Q3'].",";
                                $q4_text03L .= $value['Q4'].",";
                            }
                        }
                    echo trim($code03L, ",");
                ?>
            </td>
            <td <?php if(trim($top03L, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
                <?php
                // $top03L = trim($top03L, ",");
                // $top03L = explode(",", $top03L);
                echo trim($top03L, ",");
                ?>
            </td>
            <td>
                <?php echo trim($q1_text03L, ","); ?>
            </td>
            <td><?php echo trim($q2_text03L, ","); ?></td>
            <td><?php echo trim($q3_text03L, ","); ?></td>
            <td><?php echo trim($q4_text03L, ","); ?></td>
            <td><?php $rows5=array($qty15,$qty25,$qty35,$qty45);
                if (array_sum($rows5)!=0) {
                    echo $rows5_new = array_sum($rows5);
                }?>
            </td>
            <td>
            <?php if (isset($rows5_new) && $top03L!=0) {
            $newrate = ($rows5_new/$top03L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code03R .= $value['CuringCode'].",";
                                $top03R .= $value['rate12'].",";
                                $q1_text03R .= $value['Q1'].",";
                                $q2_text03R .= $value['Q2'].",";
                                $q3_text03R .= $value['Q3'].",";
                                $q4_text03R .= $value['Q4'].",";
                            }
                        }
                    echo trim($code03R, ",");
                ?>
            </td>
            <td <?php if(trim($top03R, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows6=array($qty16,$qty26,$qty36,$qty46);
                if (array_sum($rows6)!=0) {
                    echo $rows6_new = array_sum($rows6);
                }?>
            </td>
            <td>
            <?php if (isset($rows6_new) && $top03R!=0) {
            $newrate = ($rows6_new/$top03R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code04L .= $value['CuringCode'].",";
                                $top04L .= $value['rate12'].",";
                                $q1_text04L .= $value['Q1'].",";
                                $q2_text04L .= $value['Q2'].",";
                                $q3_text04L .= $value['Q3'].",";
                                $q4_text04L .= $value['Q4'].",";
                            }
                        }
                    echo trim($code04L, ",");
                ?>
            </td>
            <td <?php if(trim($top04L, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows7=array($qty17,$qty27,$qty37,$qty47);
                if (array_sum($rows7)!=0) {
                    echo $rows7_new = array_sum($rows7);
                }?>
            </td>
            <td>
            <?php if (isset($rows7_new) && $top04L!=0) {
            $newrate = ($rows7_new/$top04L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code04R .= $value['CuringCode'].",";
                                $top04R .= $value['rate12'].",";
                                $q1_text04R .= $value['Q1'].",";
                                $q2_text04R .= $value['Q2'].",";
                                $q3_text04R .= $value['Q3'].",";
                                $q4_text04R .= $value['Q4'].",";
                            }
                        }
                    echo trim($code04R, ",");
                ?>
            </td>
            <td <?php if(trim($top04R, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows8=array($qty18,$qty28,$qty38,$qty48);
                if (array_sum($rows8)!=0) {
                    echo $rows8_new = array_sum($rows8);
                }?>
            </td>
            <td>
            <?php if (isset($rows8_new) && $top04R!=0) {
            $newrate = ($rows8_new/$top04R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                //     $nx .= $value['Name'].",";
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
                                $code05L .= $value['CuringCode'].",";
                                $top05L .= $value['rate12'].",";
                                $q1_text05L .= $value['Q1'].",";
                                $q2_text05L .= $value['Q2'].",";
                                $q3_text05L .= $value['Q3'].",";
                                $q4_text05L .= $value['Q4'].",";
                            }
                        }
                    echo trim($code05L, ",");
                ?>
            </td>
            <td <?php if(trim($top05L, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows9=array($qty19,$qty29,$qty39,$qty49);
                if (array_sum($rows9)!=0) {
                    echo $rows9_new = array_sum($rows9);
                }?>
            </td>
            <td>
            <?php if (isset($rows9_new) && $top05L!=0) {
            $newrate = ($rows9_new/$top05L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code05R .= $value['CuringCode'].",";
                                $top05R .= $value['rate12'].",";
                                $q1_text05R .= $value['Q1'].",";
                                $q2_text05R .= $value['Q2'].",";
                                $q3_text05R .= $value['Q3'].",";
                                $q4_text05R .= $value['Q4'].",";
                            }
                        }
                    echo trim($code05R, ",");
                ?>
            </td>
            <td <?php if(trim($top05R, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows10=array($qty110,$qty210,$qty310,$qty410);
                if (array_sum($rows10)!=0) {
                    echo $rows10_new = array_sum($rows10);
                }?>
            </td>
            <td>
            <?php if (isset($rows10_new) && $top05R!=0) {
            $newrate = ($rows10_new/$top05R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code06L .= $value['CuringCode'].",";
                                $top06L .= $value['rate12'].",";
                                $q1_text06L .= $value['Q1'].",";
                                $q2_text06L .= $value['Q2'].",";
                                $q3_text06L .= $value['Q3'].",";
                                $q4_text06L .= $value['Q4'].",";
                            }
                        }
                    echo trim($code06L, ",");
                ?>
            </td>
            <td <?php if(trim($top06L, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows11=array($qty111,$qty211,$qty311,$qty411);
                if (array_sum($rows11)!=0) {
                    echo $rows11_new = array_sum($rows11);
                }?>
            </td>
            <td>
            <?php if (isset($rows11_new) && $top06L!=0) {
            $newrate = ($rows11_new/$top06L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code06R .= $value['CuringCode'].",";
                                $top06R .= $value['rate12'].",";
                                $q1_text06R .= $value['Q1'].",";
                                $q2_text06R .= $value['Q2'].",";
                                $q3_text06R .= $value['Q3'].",";
                                $q4_text06R .= $value['Q4'].",";
                            }
                        }
                    echo trim($code06R, ",");
                ?>
            </td>
            <td <?php if(trim($top06R, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows12=array($qty112,$qty212,$qty312,$qty412);
                if (array_sum($rows12)!=0) {
                    echo $rows12_new = array_sum($rows12);
                }?>
            </td>
            <td>
            <?php if (isset($rows12_new) && $top06R!=0) {
            $newrate = ($rows12_new/$top06R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code07L .= $value['CuringCode'].",";
                                $top07L .= $value['rate12'].",";
                                $q1_text07L .= $value['Q1'].",";
                                $q2_text07L .= $value['Q2'].",";
                                $q3_text07L .= $value['Q3'].",";
                                $q4_text07L .= $value['Q4'].",";
                            }
                        }
                    echo trim($code07L, ",");
                ?>
            </td>
            <td <?php if(trim($top07L, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows13=array($qty113,$qty213,$qty313,$qty413);
                if (array_sum($rows13)!=0) {
                    echo $rows13_new = array_sum($rows13);
                }?>
            </td>
            <td>
            <?php if (isset($rows13_new) && $top07L!=0) {
            $newrate = ($rows13_new/$top07L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code07R .= $value['CuringCode'].",";
                                $top07R .= $value['rate12'].",";
                                $q1_text07R .= $value['Q1'].",";
                                $q2_text07R .= $value['Q2'].",";
                                $q3_text07R .= $value['Q3'].",";
                                $q4_text07R .= $value['Q4'].",";
                            }
                        }
                    echo trim($code07R, ",");
                ?>
            </td>
            <td <?php if(trim($top07R, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows14=array($qty114,$qty214,$qty314,$qty414);
                if (array_sum($rows14)!=0) {
                    echo $rows14_new = array_sum($rows14);
                }?>
            </td>
            <td>
            <?php if (isset($rows14_new) && $top07R!=0) {
            $newrate = ($rows14_new/$top07R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code08L .= $value['CuringCode'].",";
                                $top08L .= $value['rate12'].",";
                                $q1_text08L .= $value['Q1'].",";
                                $q2_text08L .= $value['Q2'].",";
                                $q3_text08L .= $value['Q3'].",";
                                $q4_text08L .= $value['Q4'].",";
                            }
                        }
                    echo trim($code08L, ",");
                ?>
            </td>
            <td <?php if(trim($top08L, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows15=array($qty115,$qty215,$qty315,$qty415);
                if (array_sum($rows15)!=0) {
                    echo $rows15_new = array_sum($rows15);
                }?>
            </td>
            <td>
            <?php if (isset($rows15_new) && $top08L!=0) {
            $newrate = ($rows15_new/$top08L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code08R .= $value['CuringCode'].",";
                                $top08R .= $value['rate12'].",";
                                $q1_text08R .= $value['Q1'].",";
                                $q2_text08R .= $value['Q2'].",";
                                $q3_text08R .= $value['Q3'].",";
                                $q4_text08R .= $value['Q4'].",";
                            }
                        }
                    echo trim($code08R, ",");
                ?>
            </td>
            <td <?php if(trim($top08R, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <<td><?php $rows16=array($qty116,$qty216,$qty316,$qty416);
                if (array_sum($rows16)!=0) {
                    echo $rows16_new = array_sum($rows16);
                }?>
            </td>
            <td>
            <?php if (isset($rows16_new) && $top08R!=0) {
            $newrate = ($rows16_new/$top08R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                //     $nx .= $value['Name'].",";
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
                                $code09L .= $value['CuringCode'].",";
                                $top09L .= $value['rate12'].",";
                                $q1_text09L .= $value['Q1'].",";
                                $q2_text09L .= $value['Q2'].",";
                                $q3_text09L .= $value['Q3'].",";
                                $q4_text09L .= $value['Q4'].",";
                            }
                        }
                    echo trim($code09L, ",");
                ?>
            </td>
            <td <?php if(trim($top09L, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows17=array($qty117,$qty217,$qty317,$qty417);
                if (array_sum($rows17)!=0) {
                    echo $rows17_new = array_sum($rows17);
                }?>
            </td>
            <td>
            <?php if (isset($rows17_new) && $top09L!=0) {
            $newrate = ($rows17_new/$top09L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                $code09R .= $value['CuringCode'].",";
                                $top09R .= $value['rate12'].",";
                                $q1_text09R .= $value['Q1'].",";
                                $q2_text09R .= $value['Q2'].",";
                                $q3_text09R .= $value['Q3'].",";
                                $q4_text09R .= $value['Q4'].",";
                            }
                        }
                    echo trim($code09R, ",");
                ?>
            </td>
            <td <?php if(trim($top09R, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows18=array($qty118,$qty218,$qty318,$qty418);
                if (array_sum($rows18)!=0) {
                    echo $rows18_new = array_sum($rows18);
                }?>
            </td>
            <td>
            <?php if (isset($rows18_new) && $top09R!=0) {
            $newrate = ($rows18_new/$top09R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
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
                                    $code10L .= $value['CuringCode'].",";
                                    $temp_curing_code = $value['CuringCode'];
                                }

                                $top10L .= $value['rate12'].",";

                                if ($temp_q1 !== $value['Q1']) {
                                    $q1_text10L .= $value['Q1'].",";
                                    $temp_q1 = $value['Q1'];
                                }

                                if ($temp_q2 !== $value['Q2']) {
                                    $q2_text10L .= $value['Q2'].",";
                                    $temp_q2 = $value['Q2'];
                                }

                                if ($temp_q3 !== $value['Q3']) {
                                    $q3_text10L .= $value['Q3'].",";
                                    $temp_q3 = $value['Q3'];
                                }

                                if ($temp_q4 !== $value['Q4']) {
                                    $q4_text10L .= $value['Q4'].",";
                                    $temp_q4 = $value['Q4'];
                                }
                            }
                        }
                    echo trim($code10L, ",");
                ?>
            </td>
            <td <?php if(trim($top10L, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows19=array($qty119,$qty219,$qty319,$qty419);
                if (array_sum($rows19)!=0) {
                    echo $rows19_new = array_sum($rows19);
                }?>
            </td>
            <td>
            <?php if (isset($rows19_new) && $top10L!=0) {
            $newrate = ($rows19_new/$top10L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                        $temp_curing_code = '';
                        $temp_q1 = '';
                        $temp_q2 = '';
                        $temp_q3 = '';
                        $temp_q4 = '';
                        $code10R = '';

                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b10 && $value['PressSide'] === 'R') {
                                if ($temp_curing_code !== $value['CuringCode']) {
                                    $code10R .= $value['CuringCode'].",";
                                    $temp_curing_code = $value['CuringCode'];
                                }

                                $top10R .= $value['rate12'].",";

                                if ($temp_q1 !== $value['Q1']) {
                                    $q1_text10R .= $value['Q1'].",";
                                    $temp_q1 = $value['Q1'];
                                }

                                if ($temp_q2 !== $value['Q2']) {
                                    $q2_text10R .= $value['Q2'].",";
                                    $temp_q2 = $value['Q2'];
                                }

                                if ($temp_q3 !== $value['Q3']) {
                                    $q3_text10R .= $value['Q3'].",";
                                    $temp_q3 = $value['Q3'];
                                }

                                if ($temp_q4 !== $value['Q4']) {
                                    $q4_text10R .= $value['Q4'].",";
                                    $temp_q4 = $value['Q4'];
                                }
                            }
                        }
                    echo trim($code10R, ",");
                ?>
            </td>
            <td <?php if(trim($top10R, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows20=array($qty1110,$qty2110,$qty3110,$qty4110);
                if (array_sum($rows20)!=0) {
                    echo $rows20_new = array_sum($rows20);
                }?>
            </td>
            <td>
            <?php if (isset($rows20_new) && $top10R!=0) {
            $newrate = ($rows20_new/$top10R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b11; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b11 && $value['PressSide'] === 'L') {
                                $code11L .= $value['CuringCode'].",";
                                $top11L .= $value['rate12'].",";
                                $q1_text11L .= $value['Q1'].",";
                                $q2_text11L .= $value['Q2'].",";
                                $q3_text11L .= $value['Q3'].",";
                                $q4_text11L .= $value['Q4'].",";
                            }
                        }
                    echo trim($code11L, ",");
                ?>
            </td>
            <td <?php if(trim($top11L, ",")== '0' ): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows21=array($qty1111,$qty2111,$qty3111,$qty4111);
                if (array_sum($rows21)!=0) {
                    echo $rows21_new = array_sum($rows21);
                }?>
            </td>
            <td>
            <?php if (isset($rows21_new) && $top11L!=0) {
            $newrate = ($rows21_new/$top11L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                            if ($value['PressNo'] === $b11 && $value['PressSide'] === 'R') {
                                $code11R .= $value['CuringCode'].",";
                                $top11R .= $value['rate12'].",";
                                $q1_text11R .= $value['Q1'].",";
                                $q2_text11R .= $value['Q2'].",";
                                $q3_text11R .= $value['Q3'].",";
                                $q4_text11R .= $value['Q4'].",";
                            }
                        }
                    echo trim($code11R, ",");
                ?>
            </td>
            <td <?php if(trim($top11R, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows22=array($qty1112,$qty2112,$qty3112,$qty4112);
                if (array_sum($rows22)!=0) {
                    echo $rows22_new = array_sum($rows22);
                }?>
            </td>
            <td>
            <?php if (isset($rows22_new) && $top11R!=0) {
            $newrate = ($rows22_new/$top11R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2"><?php echo $b12; ?></td>
            <td>L</td>
            <td>
                <?php
                        foreach ($datajsonQ as $key => $value) {
                            if ($value['PressNo'] === $b12 && $value['PressSide'] === 'L') {
                                $code12L .= $value['CuringCode'].",";
                                $top12L .= $value['rate12'].",";
                                $q1_text12L .= $value['Q1'].",";
                                $q2_text12L .= $value['Q2'].",";
                                $q3_text12L .= $value['Q3'].",";
                                $q4_text12L .= $value['Q4'].",";
                            }
                        }
                    echo trim($code12L, ",");
                ?>
            </td>
            <td <?php if(trim($top12L, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows23=array($qty1113,$qty2113,$qty3113,$qty4113);
                if (array_sum($rows23)!=0) {
                    echo $rows23_new = array_sum($rows23);
                }?>
            </td>
            <td>
            <?php if (isset($rows23_new) && $top12L!=0) {
            $newrate = ($rows23_new/$top12L)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
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
                                $code12R .= $value['CuringCode'].",";
                                $top12R .= $value['rate12'].",";
                                $q1_text12R .= $value['Q1'].",";
                                $q2_text12R .= $value['Q2'].",";
                                $q3_text12R .= $value['Q3'].",";
                                $q4_text12R .= $value['Q4'].",";
                            }
                        }
                    echo trim($code12R, ",");
                ?>
            </td>
            <td <?php if(trim($top12R, ",")== '0'): ?> style="background-color:#EE1010;" <?php endif; ?>>
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
            <td><?php $rows24=array($qty1114,$qty2114,$qty3114,$qty4114);
                if (array_sum($rows24)!=0) {
                    echo $rows24_new = array_sum($rows24);
                }?>
            </td>
            <td>
            <?php if (isset($rows24_new) && $top12R!=0) {
            $newrate = ($rows24_new/$top12R)*100;
            echo $newrate_format_number = number_format($newrate, 2, '.', '');
            }?>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td colspan="2" class="f10"><br>
                <b>Total</b>
            </td>
            <td></td>
            <td></td>
            <td>
            <?php $sumtop=array($top01L,$top01R,$top02L,$top02R,$top03L,$top03R,$top04L,$top04R,$top05L,$top05R,$top06L,$top06R,$top07L,$top07R,$top08L,$top08R,$top09L,$top09R,$top10L,$top10R,$top11L,$top11R,$top12L,$top12R);
                if (array_sum($sumtop)!=0) {
                    echo $sumtop = array_sum($sumtop);
                }

                else {
                  echo $sumtop = 0;
                }
            ?>
            </td>
            <td>
            <?php $sumq1=array($qty11,$qty12,$qty13,$qty14,$qty15,$qty16,$qty17,$qty18,$qty19,$qty110,$qty111,$qty112,$qty113,$qty114,$qty115,$qty116,$qty117,$qty118,$qty119,$qty1110,$qty1111,$qty1112,$qty1113,$qty1114);
                if (array_sum($sumq1)!=0) {
                    echo $sumq1 = array_sum($sumq1);
                }
            ?>
            </td>
            <td>
            <?php $sumq2=array($qty21,$qty22,$qty23,$qty24,$qty25,$qty26,$qty27,$qty28,$qty29,$qty210,$qty211,$qty212,$qty213,$qty214,$qty215,$qty216,$qty217,$qty218,$qty219,$qty2110,$qty2111,$qty2112,$qty2113,$qty2114);
                if (array_sum($sumq2)!=0) {
                    echo $sumq2 = array_sum($sumq2);
                }
            ?>
            </td>
            <td>
            <?php $sumq3=array($qty31,$qty32,$qty33,$qty34,$qty35,$qty36,$qty37,$qty38,$qty39,$qty310,$qty311,$qty312,$qty313,$qty314,$qty315,$qty316,$qty317,$qty318,$qty319,$qty3110,$qty3111,$qty3112,$qty3113,$qty3114);
                if (array_sum($sumq3)!=0) {
                    echo $sumq3 = array_sum($sumq3);
                }
            ?>
            </td>
            <td>
            <?php $sumq4=array($qty41,$qty42,$qty43,$qty44,$qty45,$qty46,$qty47,$qty48,$qty49,$qty410,$qty411,$qty412,$qty413,$qty414,$qty415,$qty416,$qty417,$qty418,$qty419,$qty4110,$qty4111,$qty4112,$qty4113,$qty4114);
                if (array_sum($sumq4)!=0) {
                    echo $sumq4 = array_sum($sumq4);
                }
            ?>
            </td>
            <td>
                <?php
                    // foreach ($datajson as $value) {
                    // //$sum = 0;
                    // $rows1 = array($value->Q1,$value->Q2,$value->Q3,$value->Q4);
                    // $QQ1 = array_sum($rows1);
                    // $sumrows1 += $QQ1;
                    // }
                    // if ($sumrows1!=0) {
                    //     echo $sumrows1;
                    // }

                    $sum_all=array($sumq1,$sumq2,$sumq3,$sumq4);
                    if (array_sum($sum_all)!=0) {
                        echo $sumrows1 = array_sum($sum_all);
                    }
                ?>
            </td>
            <td>
            <?php $sumq_all=array($sumq1,$sumq2,$sumq3,$sumq4);
              if (array_sum($sumq_all)!=0) {

                    $sumq_all = array_sum($sumq_all);
                    if($sumtop == "" || $sumtop == 0)
                    {
                      $sumper = 0;
                    }
                    else {
                    $sumper = ($sumq_all/$sumtop)*100;

                    }
                    echo $sumper_format_number = number_format($sumper, 2, '.', '');
                }
            ?>
            </td>
            <td class="f10"><br><b>สรุปการผลิต</b></td>
            <td colspan="4" align="left" class="f10">
                <br><b>Cure : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            if ($sumrows1!=0) {
                echo $sumrows1;
            }
            ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;เส้น</b>
            </td>
        </tr>
        <tr>
            <td colspan="17" class="f10" valign="bottom"><br><br><b>ผู้ตรวจสอบ : ......................................................................
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            ผู้อนุมัติ : ......................................................................</b></td>
        </tr>
    </table>
    <table class="table">
        <tr class="tr">
            <td class="td" align="left">
                Ref.WI-PP-2.12
            </td>
            <td class="td" align="right">
                FM-PP-2.12.3,Issued #1
                <!-- FM-PP-2.12.3,Issued #1 -->
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
?>
