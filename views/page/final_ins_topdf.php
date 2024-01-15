<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Final Finishing-INS Report</title>
    <style>
        body {
            font-size: 10px;
        }
    </style>
</head>

<body>
    <table border="1" cellspacing="0" width="100%">
        <thead>
            <tr>
                <td style="text-align: center;" colspan="2">
                    <img src="./assets/images/str.jpg" width="150" alt="">
                </td>
                <td style="text-align: center; padding: 30px;" colspan="5">
                    <div>SIAMTRUCK RADIAL CO. LTD.</div>
                    <div>Final Finishing-INS Report</div>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    Date : <?php echo $date; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Shift : <?php echo $shift; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    BOI : <?php echo $BOIName; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    Export Date : <?php echo date("d-m-Y H:i:s"); ?>
                </td>
            </tr>
            <?php if($shift == 'กลางวัน'){ ?>
                <tr>
                <th style="border-top: 0px; padding: 10px; width:10px;">ลำดับ</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">User Inspector</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">08.00 - 11.00</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">11.00 - 14.00</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">14.00 - 17.00</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">17.00 - 20.00</th>
                <th style="border-top: 0px; padding: 10px; width:70px;">Total</th>
            </tr>
           <?php }else{ ?>
            <tr>
                <th style="border-top: 0px; padding: 10px; width:10px;">ลำดับ</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">User Inspector</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">20.00 - 23.00</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">23.00 - 02.00</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">02.00 - 05.00</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">05.00 - 08.00</th>
                <th style="border-top: 0px; padding: 10px; width:70px;">Total</th>
            </tr>
           <?php } ?>
        </thead>
        <tbody>
            <?php
            $i=1;
             foreach ($data as $key => $value)
             {
                echo "<tr>";
                echo "<td align='center'>" . $i . "</td>";
                echo "<td align='center'>" . $value["Username"] . "</td>";
                echo "<td align='center'>" . $value["Q1"] . "</td>";
                echo "<td align='center'>" . $value["Q2"] . "</td>";
                echo "<td align='center'>" . $value["Q3"] . "</td>";
                echo "<td align='center'>" . $value["Q4"] . "</td>";
                echo "<td align='center'>" . $value["TOTALQTY"] . "</td>";
                echo "</tr>";
                $i++;
             }
            ?>
            <tr>
                <td colspan="6" align="Right">Total</td>
                <td align="center">
                <?php                    
                    $sumtotal = 0;
                    foreach ($data as $key => $value) {
                        $sumtotal += $value['TOTALQTY'];
                    }
                    echo number_format($sumtotal); ?>
                </td>
            </tr>
        </tbody>
    </table>

</body>

</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf = new mPDF('th', 'A4', 0, '', 2, 2, 2, 2);
$mpdf->WriteHTML($html);
$mpdf->Output();
