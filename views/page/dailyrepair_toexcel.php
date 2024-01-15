<?php
 header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=Daily Repair Report".Date("Ymd_His").".xls");
// echo "<pree>";
// print_r($data);
// print_r($_SESSION);
// echo "</pree>";
?>
<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

	<title>Final Finishing-INS Report</title>
	<style>

	</style>
</head>

<<body>
    <table border="1"  cellspacing="0" width="100%" >
        <thead>
            <tr>
                <td style="text-align: center; padding: 30px;" colspan="11">
                    <div>SIAMTRUCK RADIAL CO. LTD.</div>
                    <div>Daily Repair Report</div>
                </td>
            </tr>
            <tr>
                <td colspan="11"  style="border-top: 1px; padding: 10px;">
                    Date : <?php echo $date; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Shift : <?php echo $shift; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    BOI : <?php echo $BOIName; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Type : <?php echo $type; ?>
                </td>
            </tr>
                <tr>
                <th style="border-top: 0px; padding: 10px; width:50px;">ลำดับ</th>
                <th style="border-top: 0px; padding: 10px; width:60px;">GT Code</th>
                <th style="border-top: 0px; padding: 10px; width:60px;">Building MC.</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">Date Build</th>
                <th style="border-top: 0px; padding: 10px; width:50px;">Shift Build</th>
                <th style="border-top: 0px; padding: 10px; width:60px;">Press No.</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">Date Cure</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">Barcode</th>
                <th style="border-top: 0px; padding: 10px; width:110px;">Date Repair</th>
                <th style="border-top: 0px; padding: 10px; width:350px;">Defect Description</th>
                <th style="border-top: 0px; padding: 10px; width:90px;">weekly</th>
            </tr>
        </thead>
        <tbody> 
            <?php
            $i = 1;
            foreach($data as $key => $value){
                echo "<tr>";
                echo "<td align='left' style='padding:10px;'>" . $i . "</td>";
                echo "<td align='left' style='padding:10px;'>" . $value["GT_Code"] . "</td>";
                echo "<td align='left' style='padding:10px;'>" . $value["BuildingNo"] . "</td>";
                echo "<td align='left' style='padding:10px;'>" . date('d-m-Y  H:i', strtotime($value["DateBuild"])) . "</td>";
                echo "<td align='center' style='padding:10px;'>" . $value["Shift"] . "</td>";
                echo "<td align='left' style='padding:10px;'>" . $value["PressNo"] . "</td>";
                echo "<td align='left' style='padding:10px;'>" . date('d-m-Y  H:i', strtotime($value["CuringDate"])) . "</td>";
                echo "<td align='left' style='padding:10px;'>" . $value["Barcode"] . "</td>";
                echo "<td align='left' style='padding:10px;'>" . date('d-m-Y  H:i', strtotime($value["CreateDate"])) . "</td>";
                echo "<td align='left' style='padding:10px;'>" . $value["Description"] . "</td>";
                echo "<td align='left' style='padding:10px;'>" . $value["Batch"] . "</td>";
                echo "</tr>";
                $i++;
            }
            ?>
        </tbody>

        <!-- <tbody>
                
        </tbody> -->
    </table>

</body>

</html>
<?
$html = ob_get_contents();
ob_end_clean();
