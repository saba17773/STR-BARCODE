<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=FinalHold" . Date("Ymd_His") . ".xls");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Report Final Hold </title>
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
                <td align="center" colspan="6" class="f12">
                    <h2><b>Report Final Hold</b></h2>
                </td>
                <td colspan="3">
                    <!-- <a class="navbar-brand"><img src="./assets/images/STR.jpg" style="padding-left:10px;height:30px; width:auto;" /></a> -->
                </td>
            </tr>
            <tr>
                <td colspan="12" class="f12">
                    <b>Date : <?php echo $date; ?></b>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b>Time : <?php echo $time; ?></b>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b>Product Group : <?php echo $BOIName; ?></b>
                </td>
            </tr>
            <tr>
                <td width='5%'>
                    No.
                </td>
                <td width='10%'>
                    วันที่
                </td>
                <td width='5%'>
                    Barcode
                </td>
                <td width='5%'>
                    Curing Code
                </td>
                <td width='20%'>
                    Item Name
                </td>
                <td width='5%'>
                    Batch
                </td>
                <td width='10%'>
                    Date Build
                </td>
                <td width='5%'>
                    GT Code
                </td>
                <td width='5%'>
                    Press No.
                </td>
                <td width='5%'>
                    Press Side
                </td>
                <td width='5%'>
                    Shift
                </td>
                <td width='20%'>
                    Description
                </td>
            </tr>
        </thead>
        <?php 
        $row = 1 ;
        foreach ($datajson as $key => $value) {
        ?>
            <tr>
                <td>
                    <?php echo $row++; ?>
                </td>
                <td>
                    <?php echo $value->UpdateDate; ?>
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
                    <?php echo $value->Batch; ?>
                </td>
                <td>
                    <?php echo $value->DateBuild; ?>
                </td>
                <td>
                    <?php echo $value->GT_Code; ?>
                </td>
                <td>
                    <?php echo $value->PressNo; ?>
                </td>
                <td>
                    <?php echo $value->PressSide; ?>
                </td>
                <td>
                    <?php echo $value->Shift; ?>
                </td>
                <td>
                    <?php echo $value->DefectDesc; ?>
                </td>
        </tr>

        <?php } ?>

    </table>

</body>

</html>