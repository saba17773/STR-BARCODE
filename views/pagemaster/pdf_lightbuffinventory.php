<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Light Buff Inventory Report</title>
    <style type="text/css">
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 10px;
            text-align: center;
        }

        td,
        tr {
            border: 1px solid #000000;
            text-align: left;
            padding: 6px;
            text-align: center;
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
                    <td colspan="3">
                        <a class="navbar-brand"><img src="./assets/images/STR.jpg" style="padding-left:10px;height:30px; width:auto;" /></a>
                    </td>
                    <td align="center" colspan="3" class="f12">
                        <h2><b>Light Buff Inventory</b></h2>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="f12">
                        <b>Date : <?php echo $date; ?></b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Time : <?php echo $time; ?></b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>BOI : <?php echo $BOIName; ?></b>
                    </td>
                </tr>
                <tr>
                    <td width='10%'>
                        No.
                    </td>
                    <td width='20%'>
                        Barcode
                    </td>
                    <td width='20%'>
                        Date Cure
                    </td>
                    <td width='20%'>
                        Cure Code
                    </td>
                    <td width='20%'>
                        Date Light Buff
                    </td>
                    <td width='20%'>
                        Weekly
                    </td>
                  
                </tr>
            </thead>

            <?php 
            $row = 1;
            foreach ($datajson as $key => $value) {
            ?>
                <tr>
                    <td>
                        <?php echo $row; ?>
                    </td>
                    <td>
                    <?php echo $value->Barcode; ?>
                    </td>
                    <td>
                    <?php //echo substr($value->CuringDate, 0,16);
                        $_date = new Datetime($value->CuringDate);
                        echo $_date->format("d-m-Y H:i:s");
                        ?>
                    </td>
                    <td>
                        <?php echo $value->CuringCode; ?>
                    </td>
                    <td>
                    <?php 
                        $_date = new Datetime($value->UpdateDate);
                        echo $_date->format("d-m-Y H:i:s");
                        ?>
                    </td>
                 
                    <td>
                        <?php echo $value->Batchw; ?>
                    </td>
                </tr>
            <?php 
        
         $row ++;
        } ?>
            
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