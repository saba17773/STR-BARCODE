<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Internal Withdrawal Report</title>
    <!-- <link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" /> -->
    <style type="text/css">
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 10px;
        }

        td,
        tr {
            border: 1px solid #000000;
            text-align: left;
            padding: 5px;
        }

        .f12 {
            font-size: 14px;
            font-family: "Angsana New";
        }
    </style>
</head>

<body>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <td colspan="2" align="center">
                        <a class="navbar-brand"><img src="./assets/images/STR.jpg" style="padding-left:10px;height:50px; width:auto;" /></a>
                    </td>
                    <td align="center" colspan="3" class="f12">
                        <h2><b>Send To Warehouse</b>
                    </td>
                </tr>
                <tr>
                    <td align="left" colspan="5" style="border-right: 0; border-left: 0;">
                        <b>Truck: <?php echo $datahead[0]["TruckID"]; ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </b> <b>Description:<b> <?php echo $datahead[0]["JournalDescription"] ?>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <b>Create Date:<b> <?php echo  date('Y-m-d H:i', strtotime($datahead[0]["CreateDate"])); ?><BR>
                                        <b>First Scan:<b> <?php echo  date('Y-m-d H:i', strtotime($datahead[0]["FirstScan"])); ?>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                <b>First Scan:<b> <?php echo  date('Y-m-d H:i', strtotime($datahead[0]["Lastscan"])); ?>

                    </td>

                </tr>

                <tr>
                    <td align="center" width="10%">
                        <b>Curecode</b>
                    </td>
                    <td align="center" width="14%">
                        <b>Item No.</b>
                    </td>
                    <td align="center" width="60%">
                        <b>Item Name</b>
                    </td>
                    <td align="center" width="10%%">
                        <b>Batch No.</b>
                    </td>
                    <td align="center" width="10%">
                        <b>QTY</b>
                    </td>

                </tr>
            </thead>

            <?php
            // echo "<pre>" . print_r($datajson, true) . "</pre>";
            // exit;
            $i = 1;
            foreach ($datajson as  $value) {
            ?>
                <tr>
                    <td align="center">
                        <?php echo $value["ID"]; ?>
                    </td>
                    <td align="center">
                        <?php echo $value["ItemID"]; ?>
                    </td>
                    <td align="left">
                        <?php echo $value["NameTH"]; ?>
                    </td>
                    <td align="center">
                        <?php echo $value["Batch"]; ?>
                    </td>
                    <td align="center">
                        <?php echo $value["QTY"]; ?>
                    </td>
                </tr>
            <?php
            }

            ?>


            <!-- แทนที่ตรงนี้ -->
            <tr>
                <td colspan="3"></td>
                <td align="center">
                    <b>Total </b>

                </td>
                <td align="center">
                    <?php $sum = 0;
                    foreach ($datajson as $value) {
                        $sum += $value["QTY"];
                    }
                    echo $sum;
                    ?>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
<?php
$stylesheet = " table{
	                  width: 80%;
	                }
	                tr{
	                  border: 0px;
	                }
	                td{
	               	  border: 0px;
	               	  text-align: center;
	               	  padding: 15px;
	                }";


$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th', 'A4', 0, '', 3, 3, 3, 19);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->WriteHTML($stylesheet, 1);
$pdf->SetHTMLFooter($footer);
$pdf->Output();
?>