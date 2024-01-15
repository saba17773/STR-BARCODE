<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=LightBuffInventorey" . Date("Ymd_His") . ".xls");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Light Buff Inventory </title>
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
                    <td style="border-top: 0px; padding: 20px; width:50px;">
                        No.
                    </td>
                    <td style="border-top: 0px; padding: 20px; width:150px;">
                        Barcode
                    </td>
                    <td style="border-top: 0px; padding: 20px; width:150px;">
                        Date Cure
                    </td>
                    <td style="border-top: 0px; padding: 20px; width:150px;">
                        Cure Code
                    </td>
                    <td style="border-top: 0px; padding: 20px; width:150px;">
                        Date Light Buff
                    </td>
                     <td style="border-top: 0px; padding: 20px; width:150px;">
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

</body>

</html>