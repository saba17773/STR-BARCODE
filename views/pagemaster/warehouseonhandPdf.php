<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-874"> -->
    <title>Report Warehouse Inventory Onhand</title>
    <!-- 	<link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" /> -->
    <style type="text/css">
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 10px;
            font-family: MS Sans Serif;
        }

        td,
        tr {
            border: 1px solid #000000;
            text-align: center;
            padding: 7px;
        }

        .f12 {
            font-size: 14px;
        }

        .f10 {
            font-size: 10px;
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
            padding: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <td colspan="3">
                        <a class="navbar-brand"><img src="./assets/images/STR.jpg" style="padding-left:10px;height:30px; width:auto;" /></a>
                    </td>
                    <td align="center" colspan="8" class="f12">
                        <h2>Report Warehouse Inventory Onhand</h2>
                    </td>
                </tr>
                <tr>
                    <td colspan="11" class="f12" align="left">
                        <b>Date : </b><?php echo date("d-m-Y"); ?>
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
                    <td width="7%">
                        <b>No.</b>
                    </td>
                    <td>
                        <b>Brand</b>
                    </td>
                    <td>
                        <b>Item No.</b>
                    </td>
                    <td>
                        <b>Item Name</b>
                    </td>

                    <td>
                        <b>Batch No.</b>
                    </td>

                    <td>
                        <b>Finish Good</b>
                    </td>
                    <td>
                        <b>BOM</b>
                    </td>
                    <td>
                        <b>Foil</b>
                    </td>
                    <td>
                        <b>Loading</b>
                    </td>
                    <td>
                        <b>Return</b>
                    </td>
                    <td>
                        <b>Total</b>
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
                        <?php echo $value["Brand"]; ?>
                    </td>
                    <td>
                        <?php echo $value["ItemID"]; ?>
                    </td>
                    <td style="text-align: left" ];>
                        <?php echo $value["NameTH"]; ?>
                    </td>
                    <td>
                        <?php echo $value["Batch"]; ?>
                    </td>
                    <td>
                        <?php
                        if ($value["FG"] == 0) {
                            $value["FG"] = "";
                        }
                        echo $value["FG"]; ?>
                    </td>
                    <td>
                        <?php
                        if ($value["BOM"] == 0) {
                            $value["BOM"] = "";
                        }
                        echo $value["BOM"]; ?>
                    </td>
                    <td>
                        <?php
                        if ($value["Foil"] == 0) {
                            $value["Foil"] = "";
                        }
                        echo $value["Foil"]; ?>
                    </td>
                    <td>
                        <?php
                        if ($value["Loading"] == 0) {
                            $value["Loading"] = "";
                        }
                        echo $value["Loading"]; ?>
                    </td>
                    <td>
                        <?php
                        if ($value["RT"] == 0) {
                            $value["RT"] = "";
                        }
                        echo $value["RT"]; ?>
                    </td>
                    <td>
                        <?php echo $value["Total"]; ?>
                    </td>
                </tr>
            <?php
                $x += 1;
            }

            ?>
            <tr>
                <td colspan="5">
                    <b>Total</b>
                </td>
                <td>
                    <?php
                    $sum = 0;
                    foreach ($rows as $value) {
                        $sum += $value["FG"];
                    }
                    echo number_format($sum);
                    ?>

                </td>
                <td>
                    <?php
                    $sum1 = 0;
                    foreach ($rows as $value) {
                        $sum1 += $value["BOM"];
                    }
                    echo number_format($sum1);
                    ?>
                </td>
                <td>
                    <?php
                    $sum2 = 0;
                    foreach ($rows as $value) {
                        $sum2 += $value["Foil"];
                    }
                    echo number_format($sum2);
                    ?>
                </td>
                <td>
                    <?php
                    $sum3 = 0;
                    foreach ($rows as $value) {
                        $sum3 += $value["Loading"];
                    }
                    echo number_format($sum3);
                    ?>
                </td>
                <td>
                    <?php
                    $sum4 = 0;
                    foreach ($rows as $value) {
                        $sum4 += $value["RT"];
                    }
                    echo number_format($sum4);
                    ?>
                </td>
                <td>
                    <?php
                    $sum5 = 0;
                    foreach ($rows as $value) {
                        $sum5 += $value["Total"];
                    }
                    echo number_format($sum5);
                    ?>
                </td>
            </tr>
        </table>

    </div>

</body>

</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th', 'A4', 0, '', 10, 10, 10, 10);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
?>