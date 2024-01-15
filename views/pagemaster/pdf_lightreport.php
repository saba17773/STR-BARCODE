<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Mode Light Buff Report</title>
    <style type="text/css">
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
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
            font-size: 16px;
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
                    <td align="center" colspan="7" class="f12">
                        <h2><b>Mode Light Buff Report</b></h2>
                    </td>
                </tr>
                <tr>
                    <td colspan="10" class="f12">
                        <b>Date : <?php echo $date; ?></b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Shift : <?php if($shift == "day"){echo "กลางวัน";}else{echo "กลางคืน";} ?></b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Type : <?php echo $Type; ?></b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>BOI : <?php echo $BOIName; ?></b>
                    </td>
                </tr>
                <tr>
                    <td width='3%'>
                       <b> No.</b>
                    </td>
                    <td width='8%'>
                    <b>  Serial</b>
                    </td>
                    <td width='10%'>
                    <b>   Barcode</b>
                    </td>
                    <td width='6%'>
                    <b>   Cure Code</b>
                    </td>
                    <td width='40%'>
                    <b>   Item Name</b>
                    </td>
                    <td width='7%'>
                    <b>   Press</b>
                    </td>
                    <td width='15%'>
                    <b>   Curing Date</b>
                    </td>
                    <td width='15%'>
                    <b>  Cure Operator</b>
                    </td>
                    <td width='10%'>
                        Weekly</b>
                    </td>
                    <td width='15%'>
                    <b>  Date Light Buff</b>
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
                    <?php echo $value->TemplateSerialNo; ?>
                    </td>
                    <td>
                    <?php echo $value->Barcode; ?>
                    </td>
                    <td>
                    <?php echo $value->CuringCode; ?>
                    </td>
                    <td style="text-align: left;">
                        <?php echo $value->NameTH; ?>
                    </td>
                    <td>
                    <?php echo $value->Press; ?>
                    </td>
                    <td>
                    <?php //echo substr($value->CuringDate, 0,16);
                        $_date = new Datetime($value->CuringDate);
                        echo $_date->format("d-m-Y H:i:s");
                        ?>
                    </td>
                    <td>
                    <?php echo $value->Name; ?>
                    </td>
                    <td>
                    <?php echo $value->Batch; ?>
                    </td>
                    <td>
                    <?php //echo substr($value->CuringDate, 0,16);
                        $_date = new Datetime($value->CreateDate);
                        echo $_date->format("d-m-Y H:i:s");
                        ?>
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
$pdf = new mPDF('th', 'A4-L', 0, '', 10, 10, 10, 10);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
?>