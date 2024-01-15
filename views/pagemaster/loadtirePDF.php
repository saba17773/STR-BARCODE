<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-874"> -->
    <title>รายงานส่งยางเข้าคลังสินค้า</title>
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
                    <td width="7%">
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
                        <?php
                        $datecreate = date_create($value["CreatedDate"]);
                        echo date_format($datecreate, "Y-m-d H:i"); ?>
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