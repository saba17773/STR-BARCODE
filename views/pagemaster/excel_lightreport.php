<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=LightBuffReport" . Date("Ymd_His") . ".xls");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Light Buff Report </title>
    <style>
        body {
            font-size: 0.8em;
        }
    </style>
</head>

<body>
<table border = "1">
            <thead>
                <tr>
                    <td colspan="3">
                        <!-- <a class="navbar-brand"><img src="./assets/images/STR.jpg" style="padding-left:10px;height:30px; width:auto;" /></a> -->
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
                <td style="border-top: 0px; padding: 2px; width:50px;">
                        No.
                    </td>
                    <td style="border-top: 0px; padding: 2px; width:80px;">
                        Serial
                    </td>
                    <td style="border-top: 0px; padding: 2px; width:90px;">
                        Barcode
                    </td>
                    <td style="border-top: 0px; padding: 2px; width:80px;">
                        Cure Code
                    </td>
                    <td style="border-top: 0px; padding: 2px; width:300px;">
                        Item Name
                    </td>
                    <td style="border-top: 0px; padding: 2px; width:80px;">
                        Press
                    </td>
                    <td style="border-top: 0px; padding: 2px; width:100px;">
                        Curing Date
                    </td>
                    <td style="border-top: 0px; padding: 2px; width:150px;">
                        Cure Operator
                    </td>
                    <td style="border-top: 0px; padding: 2px; width:80px;">
                        Weekly
                    </td>
                    <td style="border-top: 0px; padding: 2px; width:100px;">
                        Date Light Buff
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
                    <td>
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

</body>

</html>