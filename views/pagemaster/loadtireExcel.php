<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=loadtireReport_" . Date("Ymd_His") . ".xls");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Building master Report </title>
    <style>
        body {
            font-size: 0.8em;
        }
    </style>
</head>

<body>
    <table border="1">
        <thead>
            <tr>
                <td colspan="3">
                    <!-- <a class="navbar-brand"><img src="./assets/images/STR.jpg" style="padding-left:10px;height:30px; width:auto;" /></a> -->
                </td>
                <td align="center" colspan="5" class="f12">
                    <h2>รายงานจำนวนการโหลดยางขึ้นตู้</h2>
                </td>
            </tr>
            <tr>
                <td colspan="8" class="f12" align="left">
                    <b>Date : </b><?php echo $date; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b>BOI : </b><?php if ($BOI == "" || $BOI == 1) {
                                        echo "ALL";
                                    } else {
                                        echo $BOI;
                                    } ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b>Export Time : </b><?php echo date("H:i:s"); ?>
                </td>
            </tr>
            <tr>
                <td width="5%">
                    <b>No.</b>
                </td>
                <td>
                    <b>Barcode</b>
                </td>
                <td>
                    <b>Item No.</b>
                </td>
                <td>
                    <b>Item Name</b>
                </td>
                <td>
                    <b>Brand</b>
                </td>
                <td>
                    <b>Batch No.</b>
                </td>

                <td>
                    <b>Loading Date</b>
                </td>
                <td>
                    <b>Sale order</b>
                </td>
            </tr>
        </thead>
        <?php

        $x = 1;

        foreach ($rows as $value) {
        ?>
            <tr>
                <td>
                    <?php echo $x; ?>
                </td>
                <td>
                    <?php echo $value["Barcode"]; ?>
                </td>
                <td>
                    <?php echo $value["ItemID"]; ?>
                </td>
                <td style="text-align: left" ];>
                    <?php echo $value["NameTH"]; ?>
                </td>
                <td>
                    <?php echo $value["Brand"]; ?>
                </td>
                <td>
                    <?php echo $value["Batch"]; ?>
                </td>
                <td>
                    <?php $datecreate = date_create($value["CreatedDate"]);
                    echo date_format($datecreate, "d/m/Y H:i"); ?>
                </td>
                <td>
                    <?php echo $value["OrderId"]; ?>
                </td>
            </tr>
        <?php
            $x += 1;
        }

        ?>
        <!-- <tr>
                <td colspan="6">
                    <b>Total</b>
                </td>
                <td>
                    <#?php
                   
                    echo number_format($qty_total);
                    ?>
                </td>
                <td>

                </td>
            </tr> -->
    </table>

</body>

</html>