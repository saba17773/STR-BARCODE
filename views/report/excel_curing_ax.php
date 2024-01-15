<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=Curing_AX_Report" . Date("Ymd_His") . ".xls");
// echo "<pree>";
// print_r($data);
// print_r($_SESSION);
// echo "</pree>";
?>
<?php ob_start();

use App\Components\Utils as U;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Report Curing AX</title>
    <style>
        body {
            font-size: 0.8em;
        }
    </style>
</head>

<body>

    <table width="100%" border="1" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="text-align: center;" colspan="2">

                </th>
                <th style="text-align: center; padding: 30px;" colspan="5">
                    <div>SIAMTRUCK RADIAL CO. LTD.</div>
                    <div>Curing AX Report</div>
                </th>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px; border-bottom: 0px;">
                    <table width="100%" border="0">
                        <tr>
                            <td>
                                Date : <?php echo $date_curing; ?>
                            </td>
                            <td>
                                Week : <?php echo U::getWeek($date_curing); ?>
                            </td>
                            <td>
                                Shift : <?php if ($shift == "day") {
                                            echo "กลางวัน";
                                        } else {
                                            echo "กลางคืน";
                                        } ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td colspan="2" style="padding: 10px; border-bottom: 0px;">
                    BOI : <?php echo $BOIName; ?>
                </td>
                <td colspan="3" style="padding: 10px; border-bottom: 0px;">
                    <p align="right"> <b align="right"> Export Date :</b><?php echo date("Y-m-d H:i:s"); ?></p>
                </td>
            </tr>
        </thead>
    </table>
    <table width="100%" border="1" cellspacing="0" style="text-align: center;">
        <thead>
            <tr style="background: #eeeeee;">
                <th>
                    Curing Code
                </th>
                <th>
                    Item No
                </th>
                <th>
                    <?php if ($shift === 'day') { ?>
                        08.00 - 11.00
                    <?php } else { ?>
                        20.00 - 23.00
                    <?php } ?>
                </th>
                <th>
                    <?php if ($shift === 'day') { ?>
                        11.00 - 14.00
                    <?php } else { ?>
                        23.00 - 02.00
                    <?php } ?>
                </th>
                <th>
                    <?php if ($shift === 'day') { ?>
                        14.00 - 17.00
                    <?php } else { ?>
                        02.00 - 05.00
                    <?php } ?>
                </th>
                <th>
                    <?php if ($shift === 'day') { ?>
                        17.00 - 20.00
                    <?php } else { ?>
                        05.00 - 08.00
                    <?php } ?>
                </th>
                <th>
                    Total
                </th>
                <th>
                    Diff
                </th>
            </tr>
        </thead>
        <!--  <tfoot>
          <tr>
            <td>Ref.WI-MP-1.15</td>
            <td>FM-MP-1.15.1,Issue #3</td>
          </tr>
        </tfoot> -->
        <?php
        $grandTotal = 0;
        $result = array();
        foreach ($data as $d) {
            $id = $d->CuringCode;
            if (!isset($result[$id])) {
                $result[$id]["Q1"] = $d->Q1;
                $result[$id]["Q2"] = $d->Q2;
                $result[$id]["Q3"] = $d->Q3;
                $result[$id]["Q4"] = $d->Q4;
                $result[$id]["Q5"] = $d->Q5;
                $result[$id]["Q6"] = $d->Q6;
                $result[$id]["code"] = $d->CuringCode;
                $result[$id]["item"] = $d->ItemNo;
                $result[$id]["WeightQ1"] += $d->WeightQ1;
                $result[$id]["WeightQ2"] += $d->WeightQ2;
                $result[$id]["WeightQ3"] += $d->WeightQ3;
                $result[$id]["WeightQ4"] += $d->WeightQ4;
            } else if (isset($result[$id])) {
                $result[$id]["Q1"] += $d->Q1;
                $result[$id]["Q2"] += $d->Q2;
                $result[$id]["Q3"] += $d->Q3;
                $result[$id]["Q4"] += $d->Q4;
                $result[$id]["Q5"] += $d->Q5;
                $result[$id]["Q6"] += $d->Q6;
                $result[$id]["Total"] = $d->Total;
                $result[$id]["WeightQ1"] += $d->WeightQ1;
                $result[$id]["WeightQ2"] += $d->WeightQ2;
                $result[$id]["WeightQ3"] += $d->WeightQ3;
                $result[$id]["WeightQ4"] += $d->WeightQ4; 
            } else {
                $result[$id] = [
                    "code" => $d->CuringCode,
                    "item" => $d->ItemNo,
                    "Q1" => 0,
                    "Q2" => 0,
                    "Q3" => 0,
                    "Q4" => 0,
                    "Q5" => 0,
                    "Q6" => 0
                ];
            }
        }
        $total_q1 = 0;
        $total_q2 = 0;
        $total_q3 = 0;
        $total_q4 = 0;
        $total_q5 = 0;
        $total_q6 = 0;
        $WeightQ1 = 0;
        $WeightQ2 = 0;
        $WeightQ3 = 0;
        $WeightQ4 = 0;

        foreach ($result as $key => $value) { ?>
            <tr>
                <td style="padding: 3px;"><?php echo $key; ?></td>
                <td style="padding: 3px;"><?php if ($value["item"] !== 0) echo $value["item"]; ?></td>
                <td style="padding: 3px;"><?php if ($value["Q1"] !== 0) echo $value["Q1"]; ?></td>
                <?php $total_q1 += $value['Q1']; ?>
                <td style="padding: 3px;"><?php if ($value["Q2"] !== 0) echo $value["Q2"]; ?></td>
                <?php $total_q2 += $value['Q2']; ?>
                <td style="padding: 3px;"><?php if ($value["Q3"] !== 0) echo $value["Q3"]; ?></td>
                <?php $total_q3 += $value['Q3']; ?>
                <td style="padding: 3px;"><?php if ($value["Q4"] !== 0) echo $value["Q4"]; ?></td>
                <?php $total_q4 += $value['Q4']; ?>    
                <?php $WeightQ1 += $value['WeightQ1']; ?>            
                <?php $WeightQ2 += $value['WeightQ2']; ?>            
                <?php $WeightQ3 += $value['WeightQ3']; ?>            
                <?php $WeightQ4 += $value['WeightQ4']; ?>            
                <td style="padding: 3px;">
                    <?php
                    $rowTotal = (int) $value["Q1"] + (int) $value["Q2"] + (int) $value["Q3"] + (int) $value["Q4"] + (int) $value["Q5"] + (int) $value["Q6"];
                    $grandTotal += $rowTotal;
                    $rowTotalWeight = (int) $value["WeightQ1"] + (int) $value["WeightQ2"] + (int) $value["WeightQ3"] + (int) $value["WeightQ4"]; 
                    $grandTotalWeight += $rowTotalWeight;                    
                    if ($rowTotal !== 0) {
                        echo $rowTotal;
                    }
                    ?>
                </td>
                <td style="padding: 3px;">
                    <?php
                    if ($value["Total"] == 0 || $value["Total"] == NULL) {
                        $rowTotalDiff = 0;
                    } else {
                        $rowTotalDiff =     $rowTotal - $value["Total"];
                    }
                    // $rowTotalDiff = $value["Total"] - $rowTotal;
                    $grandTotalDiff +=  $rowTotalDiff;

                    if ($rowTotalDiff == 0) {
                        echo "";
                    } else {
                        echo $rowTotalDiff;
                    }

                    ?>

                </td>
            </tr>
        <?php } ?>
        <tr style="background: #eeeeee">
            <td colspan="2">Total</td>
            <td><?php echo $total_q1; ?></td>
            <td><?php echo $total_q2; ?></td>
            <td><?php echo $total_q3; ?></td>
            <td><?php echo $total_q4; ?></td>            
            <td><?php echo number_format((int) $grandTotal); ?></td>
            <td><?php echo number_format((int) $grandTotalDiff); ?></td>
        </tr>

        <tr style="background: #eeeeee">
            <td colspan="2">Total Weight(k.g)</td>
            <td><?php echo number_format((float)$WeightQ1/1000, 2); ?></td>
            <td><?php echo number_format((float)$WeightQ2/1000, 2); ?></td>
            <td><?php echo number_format((float)$WeightQ3/1000, 2); ?></td>  
            <td><?php echo number_format((float)$WeightQ4/1000, 2); ?></td>
            <td><?php echo number_format((float)$grandTotalWeight/1000, 2); ?></td>            
            <td></td>                    
        </tr>

    </table>

</body>

</html>
<?
$html = ob_get_contents();
ob_end_clean();
