<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=Final_To_Warehouse" . Date("Ymd_His") . ".xls");
// echo "<pree>";
// print_r($data);
// print_r($_SESSION);
// echo "</pree>";
?>
<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>report_curetire_scrap_xcell </title>
    <style>
        body {
            font-size: 0.8em;
        }
    </style>
</head>

<body>

    <table width="100%" border="1" cellspacing="0">
        <tr>
            <td style="text-align: center;">
                <!-- <img src="./assets/images/str.jpg" width="150" alt=""> -->
            </td>
            <td style="text-align: center; padding: 30px;" colspan="6">
                <div>SIAMTRUCK RADIAL CO. LTD.</div>
                <div>Final Send To Warehouse</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 10px; border-bottom: 0px;">
                Date : <?php echo $date; ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                Shift : <?php
                        if ($shift == "day") {
                            echo "กลางวัน";
                        } else {
                            echo "กลางคืน";
                        }
                        ?>
            </td>
        </tr>
    </table>
    <table border="1" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th style="border-top: 0px; padding: 10px;">Truck</th>
                <th style="border-top: 0px; padding: 10px;">Time.</th>
                <th style="border-top: 0px; padding: 10px;">Item No.</th>
                <th style="border-top: 0px; padding: 10px;">Item Name.</th>
                <th style="border-top: 0px; padding: 10px;">CureCode</th>
                <th style="border-top: 0px; padding: 10px;">Batch No.</th>
                <th style="border-top: 0px; padding: 10px;">QTY</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $round = "";
            $descjour = "";
            foreach ($data as $key => $value) {

                echo "<tr>";

                if ($round != $value["TruckID"] || $descjour != $value["descjour"]) {
                    echo "<td align='center' rowspan=" . $value['rowspan'] . ">" .  $value["TruckID"] . "\n" . $value['descjour'] . "</td>";
                    echo "<td align='center' rowspan=" . $value['rowspan'] . ">" .  $value["Craeatedate"] . "</td>";
                }
                if ($value["ItemID"] == "") {
                    echo "<td>&nbsp;</td>";
                } else {
                    echo "<td>" . $value["ItemID"] . "</td>";
                }
                echo "<td align='left'>" . $value["NameTH"] . "</td>";
                echo "<td align='center'>" . $value["ID"] . "</td>";
                echo "<td align='center'>" . $value["Batch"] . "</td>";
                echo "<td align='center'>" . $value["qty"] . "</td>";




                echo "</tr>";
                if ($value['k'] != 0) {
                    echo "<tr  style='background: #CBF0F6;'>";
                    echo "<td colspan=4></td>";
                    echo "<td></td>";
                    echo "<td align='center'><b>Total</b></td>";
                    echo "<td align='center'>" . $value['total'] . "</td>";
                    echo "</tr>";
                }

                $round = $value["TruckID"];
                $descjour = $value["descjour"];
            }

            ?>
            <tr>
                <td colspan="5" align="center">Grand Total</td>
                <td align="center">

                    <?php
                    $sumqtytotal = 0;
                    foreach ($data as $key => $value) {
                        $sumqtytotal += $value['qty'];
                    }
                    echo number_format($sumqtytotal);
                    ?>

                </td>
            </tr>


        </tbody>
    </table>

</body>

</html>
<?
$html = ob_get_contents();
ob_end_clean();