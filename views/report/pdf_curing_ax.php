<?php
ob_start();
// var_dump($data);exit;
use App\Components\Utils as U;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Report Curing AX</title>
</head>

<style>
    .info {
        font-size: 0.7em;
    }
</style>

<body>
    <table width="100%" border="1" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="text-align: center;">
                    <img src="./assets/images/str.jpg" width="100" alt="">
                </th>
                <th style="text-align: center; padding: 30px;">

                    <div>SIAMTRUCK RADIAL CO. LTD.</div>
                    <div>Curing AX Report</div>
                </th>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px; border-bottom: 0px;">
                    <table width="100%" border="0">
                        <tr>
                            <td class="info">
                                Date : <?php echo $date_curing; ?>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                            <td class="info">
                                Week : <?php echo U::getWeek($date_curing); ?>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                            <td class="info">
                                Shift : <?php if ($shift == "day") {
                                            echo "กลางวัน";
                                        } else {
                                            echo "กลางคืน";
                                        } ?>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                            <td class="info">
                                BOI : <?php echo $BOIName; ?>

                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                            <td class="info">
                                Export Date: <?php echo date("Y-m-d H:i:s"); ?>
                            </td>

                        </tr>
                    </table>
                </td>
            </tr>
        </thead>
    </table>
    <table width="100%" border="1" cellspacing="0" style="text-align: center;">
        <thead>
            <tr style="background: #cccccc;">
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
                <th>Final Received</th>
                <th>Diff</th>
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
        $result = [];
        foreach ($data as $d) {
            $id = $d->CuringCode;

            $result[$id]["Q1"] += $d->Q1;
            $result[$id]["item"] = $d->ItemNo;
            $result[$id]["Q2"] += $d->Q2;
            $result[$id]["Q3"] += $d->Q3;
            $result[$id]["Q4"] += $d->Q4;
            $result[$id]["Q5"] += $d->Q5;
            $result[$id]["Q6"] += $d->Q6;
            $result[$id]["RECEIVED_ALL"] += $d->RECEIVED_ALL;
            $result[$id]["Total"] = $d->Total;
            $result[$id]["WeightQ1"] += $d->WeightQ1;            
            $result[$id]["WeightQ2"] += $d->WeightQ2;            
            $result[$id]["WeightQ3"] += $d->WeightQ3;            
            $result[$id]["WeightQ4"] += $d->WeightQ4;            


            // if (isset($result[$id])) {
            //     $result[$id]["Q1"] += $d->Q1;
            //     $result[$id]["Q2"] += $d->Q2;
            //     $result[$id]["Q3"] += $d->Q3;
            //     $result[$id]["Q4"] += $d->Q4;
            //     $result[$id]["Q5"] += $d->Q5;
            //     $result[$id]["Q6"] += $d->Q6;
            //     $result[$id]["RECEIVED_ALL"] += $d->RECEIVED_ALL;

            //     echo "<pre>" . print_r($result, true) . "</pre>";
            // } else {
            //     $result[$id] = [
            //         "code" => $d->CuringCode,
            //         "item" => $d->ItemNo,
            //         "Q1" => 0,
            //         "Q2" => 0,
            //         "Q3" => 0,
            //         "Q4" => 0,
            //         "Q5" => 0,
            //         "Q6" => 0,
            //         "RECEIVED_ALL" => 0
            //     ];
            // }
        }
        // echo "<pre>";print_r($data);exit();echo "/<pre>";
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
        $xx = 1;

        // echo "<pre>" . print_r($result, true) . "</pre>";
        // exit;

        foreach ($result as $key => $value) { ?>

            <?php if ($xx % 2 === 0) : ?>
                <tr style="background: #f5f5f5;">
                <?php else : ?>
                <tr>
                <?php endif;
            $xx++; ?>

                <td style="padding: 3px; width: 100px;"><?php echo $key; ?></td>
                <td style="padding: 3px; width: 100px;"><?php if ($value["item"] !== 0) echo $value["item"]; ?></td>
                <td style="padding: 3px; width: 130px;"><?php if ($value["Q1"] !== 0) echo $value["Q1"]; ?></td>
                <?php $total_q1 += $value['Q1']; ?>
                <td style="padding: 3px; width: 130px;"><?php if ($value["Q2"] !== 0) echo $value["Q2"]; ?></td>
                <?php $total_q2 += $value['Q2']; ?>
                <td style="padding: 3px; width: 130px;"><?php if ($value["Q3"] !== 0) echo $value["Q3"]; ?></td>
                <?php $total_q3 += $value['Q3']; ?>
                <td style="padding: 3px; width: 130px;"><?php if ($value["Q4"] !== 0) echo $value["Q4"]; ?></td>
                <?php $total_q4 += $value['Q4']; ?>
                <?php $WeightQ1 += $value['WeightQ1']; ?>
                <?php $WeightQ2 += $value['WeightQ2']; ?>
                <?php $WeightQ3 += $value['WeightQ3']; ?>
                <?php $WeightQ4 += $value['WeightQ4']; ?>

                <?php
                $rowTotal = (int) $value["Q1"] + (int) $value["Q2"] + (int) $value["Q3"] + (int) $value["Q4"];
                $grandTotal += $rowTotal;
                $rowTotalWeight = (int) $value["WeightQ1"] + (int) $value["WeightQ2"] + (int) $value["WeightQ3"] + (int) $value["WeightQ4"]; 
                $grandTotalWeight += $rowTotalWeight;
                if ($rowTotal !== 0) {                
                ?>
                    <?php if ($rowTotal === $value["RECEIVED_ALL"]) : ?>
                        <td style="padding: 3px; width: 100px; font-weight: bold; ">
                        <?php else : ?>
                        <td style="padding: 3px; width: 100px; font-weight: bold;">
                        <?php endif; ?>

                        <?php echo $rowTotal; ?>
                        </td>
                    <?php
                }
                    ?>
                    <?php if ($rowTotal === $value["RECEIVED_ALL"]) : ?>
                        <td style="width: 120px; font-weight: bold;">
                            <img src="./assets/images/check.png" width="20" alt="">
                        </td>
                    <?php else : ?>
                        <td style="width: 120px; font-weight: bold;"></td>
                    <?php endif; ?>
                    <td style="padding: 3px; width: 130px;">
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

                        ?></td>
                </tr>
            <?php } ?>
            <tr style="background: #cccccc">
                <td colspan="2">Total</td>
                <td><?php echo $total_q1; ?></td>
                <td><?php echo $total_q2; ?></td>
                <td><?php echo $total_q3; ?></td>
                <td><?php echo $total_q4; ?></td>
                <td><?php echo number_format((int) $grandTotal); ?></td>
                <td></td>
                <td><?php echo number_format((int) $grandTotalDiff); ?></td>
            </tr>
            
            <tr style="background: #cccccc">
                <td colspan="2">Total Weight(k.g)</td>
                <td><?php echo number_format((float)$WeightQ1/1000, 2); ?></td>
                <td><?php echo number_format((float)$WeightQ2/1000, 2); ?></td>
                <td><?php echo number_format((float)$WeightQ3/1000, 2); ?></td>
                <td><?php echo number_format((float)$WeightQ4/1000, 2); ?></td>
                <td><?php echo number_format((float) $grandTotalWeight/1000, 2); ?></td>
                <td></td>               
            </tr>

    </table>
    <!-- <table cellpadding="40" width="100%" align="center">
        <tr>
            <td style="text-align: center; font-weight: bold;">
                ________________________________
                <br> Operator
            </td>
            <td style="text-align: center; font-weight: bold;">
                 ________________________________
                <br> Leader
            </td>
        </tr>
    </table> -->
</body>

</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf = new mPDF('th', 'A4', 0, '', 2, 2, 2, 2);
// $mpdf->SetHTMLFooter("<span style='text-align: left;'>Ref.WI-MP-1.15</span> <span style='text-align: right;'>FM-MP-1.15.1,Issue #3</span>");
// $mpdf->SetHTMLFooter('
// <table class="table" width="100%">
// <tr class="tr">
//     <td class="td" align="left">
//         Ref.WI-MP-1.15
//     </td>
//     <td class="td" align="right">
//         FM-MP-1.15.1,Issue #3
//     </td>
// </tr>
// </table>
// ');
$mpdf->WriteHTML($html);
$mpdf->Output();
