<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-874"> -->
    <title>Building Report By Code Report </title>
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
                    <td colspan="2">
                        <a class="navbar-brand"><img src="./assets/images/STR.jpg" style="padding-left:10px;height:30px; width:auto;" /></a>
                    </td>
                    <td align="center" colspan="5" class="f12">
                        <h2>Building Report By Code <?php echo $GT_COD; ?> </h2>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" class="f12" align="left">
                        <b>Date : </b><?php echo $date ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Shift : </b><?php if ($shift == 'day') {
                                            echo "กลางวัน";
                                        } else {
                                            echo "กลางคืน";
                                        } ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        <b>Export Date : </b><?php echo date("d-m-Y H:i:s"); ?>



                    </td>
                </tr>
                <tr>
                    <td width="7%">
                        <b>No.</b>
                    </td>
                    <td width="15%">
                        <b>Barcode</b>
                    </td>
                    <td width="15%">
                        <b>Date Build</b>
                    </td>
                    <td width="15%">
                        <b>GT Inspection Date</b>
                    </td>
                    <td width="15%">
                        <b>Curing Date</b>
                    </td>
                    <td width="15%">
                        <b>Hold Date</b>
                    </td>
                    <td width="15%">
                        <b>Disposal</b>
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
                        <?php
                        $DateBuild = date_create($value["DateBuild"]);
                        echo date_format($DateBuild, "Y-m-d H:i"); ?>
                    </td>
                    <td>
                        <?php
                        $GT_InspectionDate = date_create($value["GT_InspectionDate"]);
                        if ($value["GT_InspectionDate"] == NULL || $value["GT_InspectionDate"] == "") {
                            echo "";
                        } else {
                            echo date_format($GT_InspectionDate, "Y-m-d H:i");
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        $CuringDate = date_create($value["CuringDate"]);
                        if ($value["CuringDate"] == NULL || $value["CuringDate"] == "") {
                            echo "";
                        } else {
                            echo date_format($CuringDate, "Y-m-d H:i");
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $CreateDateDis = date_create($value["CreateDateDis"]);
                        if ($value["CreateDateDis"] == NULL || $value["CreateDateDis"] == "") {
                            echo "";
                        } else {
                            echo date_format($CreateDateDis, "Y-m-d H:i");
                        }
                        ?>
                    </td>
                    <td>
                        <?php echo $value["DisposalDesc"]; ?>
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