<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=CureInventorey" . Date("Ymd_His") . ".xls");
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
                <td align="center" colspan="3" class="f12">
                    <h2><b>Cured Inventory Report</b></h2>
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
                    Date/Time
                </td>
                <td width='20%'>
                    Batch
                </td>
                <td width='20%'>
                    CuredCode
                </td>
                <td width='20%'>
                    Press
                </td>
                <!-- <td width='20%'>
            Serail No.
        </td> -->
                <!-- <td width='20%'>
            Barcode No.
        </td> -->
                <td width='10%'>
                    Check Cure
                </td>
            </tr>
        </thead>

        <?php foreach ($datajson as $key => $value) {
        ?>
            <tr>
                <td>
                    <?php echo $value->Row; ?>
                </td>
                <td>
                    <?php //echo substr($value->CuringDate, 0,16);
                    $_date = new Datetime($value->CuringDate);
                    echo $_date->format("d-m-Y H:i:s");
                    ?>
                </td>
                <td>
                    <?php echo $value->Batch; ?>
                </td>
                <td>
                    <?php echo $value->CuringCode; ?>
                </td>
                <td>
                    <?php echo $value->PressNo . $value->PressSide; ?>
                </td>
                <!-- <td>
                        <#?php echo $value->TemplateSerialNo; ?>
                    </td>
                    <td>
                        <#?php echo $value->Barcode; ?>
                    </td> -->
                <td>
                    <?php echo $value->checkcur; ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td align="center" colspan="5">
                Total
            </td>
            <td>
                <?php foreach ($datajson as $key => $value) {
                    // var_dump();
                    $row = $value->Row;
                }
                echo $row . " เส้น";
                ?>
            </td>
            <td align="center" colspan="6">

            </td>
        </tr>
    </table>

</body>

</html>