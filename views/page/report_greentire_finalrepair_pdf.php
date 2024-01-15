<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Report Greentire Final Repair</title>
    <style>
        body {
            font-size: 10px;
        }
    </style>
</head>

<body>
    <table width="100%" border="1" cellspacing="0">
        <tr>
            <td style="text-align: center;">
                <img src="./assets/images/str.jpg" width="150" alt="">
            </td>
            <td style="text-align: center; padding: 30px;">
                <div>SIAMTRUCK RADIAL CO. LTD.</div>
                <div>Final Repair Report</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 10px; border-bottom: 0px;">
                Scrap Date : <?php echo $date; ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                BOI : <?php echo $BOIName; ?>
            </td>
        </tr>
    </table>
    <table border="1" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th style="border-top: 0px; padding: 10px;">ลำดับ</th>
                <th style="border-top: 0px; padding: 10px;">GT Code</th>
                <th style="border-top: 0px; padding: 10px;">MC</th>
                <th style="border-top: 0px; padding: 10px;">Date Build</th>
                <th style="border-top: 0px; padding: 10px;">Building MC.</th>
                <th style="border-top: 0px; padding: 10px;">Shift Build</th>
                <th style="border-top: 0px; padding: 10px;">Date Cure</th>
                <th style="border-top: 0px; padding: 10px;">Barcode</th>
                <th style="border-top: 0px; padding: 10px;">Date Repair</th>
                <th style="border-top: 0px; padding: 10px;">Defect Description</th>
                <th style="border-top: 0px; padding: 10px;">Weekly</th>
                <!-- <th style="border-top: 0px; padding: 10px;">Shift Scrap</th> -->
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $check_deplicate = [];
            $all_data = [];
            $data_ = [];
            foreach ($data as $value) {
                $inr = in_array($value["Barcode"], $check_deplicate);
                if ($inr === false) {
                    $check_deplicate[] = trim($value['Barcode']);
                    $data_[] = $value;
                } else {
                    foreach ($all_data as $z) {
                        if (trim($z['Barcode']) === trim($value['Barcode'])) {

                            // $z['Barcode'] = $value['Barcode'];
                            $z['GT_Code'] = $value['GT_Code'];
                            $z["PressNo"] = $value["PressNo"];
                            $z["DateBuild"] = $value["DateBuild"];
                            $z["BuildingNo"] = $value["BuildingNo"];
                            $z["Shift"] = $value["Shift"];
                            $z["DateCure"] = $value["CuringDate"];
                            $z["Barcode"] = $value["Barcode"];
                            $z["DateCure"] = $value["CuringDate"];
                            $z["DateTimeRepair"] = $value["DateTimeRepair"];
                            $z["DefectDescriotion"] = $value["DefectDescriotion"];
                            $z['Week'] = $value['Week'];
                            // $z['Shift_Build'] = $value['Shift_Build'];
                            $data_[] = $z;
                        }
                    }
                }
            }


            ?>

            <?php foreach ($data_ as $value) { ?>
                <tr>
                    <td style="padding: 5px; text-align: center;"><?php echo $i; ?></td>

                    <td style="padding: 5px;"><?php echo $value["GT_Code"]; ?></td>
                    <td style="padding: 5px;"><?php echo $value["PressNo"]; ?></td>
                    <td style="padding: 5px;">
                        <?php
                        if ($value["DateBuild"] === null || $value["DateBuild"] === "") {
                            echo "-";
                        } else {
                            $DateBuild = date('d-m-Y H:i', strtotime($value["DateBuild"]));
                            echo $DateBuild;
                        }
                        ?>
                    </td>
                    <td style="padding: 5px; text-align: center;"><?php echo $value["BuildingNo"]; ?></td>

                    <td style="padding: 5px; text-align: center;">
                        <?php
                        echo $value["Shift"];

                        ?>
                    </td>
                    <td style="padding: 5px;">
                        <?php
                        if ($value["CuringDate"] === null || $value["CuringDate"] === "") {
                            echo "-";
                        } else {
                            $DateCure = date('d-m-Y H:i', strtotime($value["CuringDate"]));
                            echo $DateCure;
                        }
                        ?>
                    </td>
                    <td style="padding: 5px;"><?php echo $value["Barcode"]; ?></td>

                    <td style="padding: 5px;">
                        <?php
                        if ($value["DateTimeRepair"] === null || $value["DateTimeRepair"] === "") {
                            echo "-";
                        } else {
                            $DateTimeRepair = date('d-m-Y H:i', strtotime($value["DateTimeRepair"]));
                            echo $DateTimeRepair;
                        }
                        ?>
                    </td>
                    <td style="padding: 5px; "><?php echo $value["DefectDescriotion"]; ?></td>
                    <td style="padding: 5px; text-align: center;"><?php echo $value["Week"]; ?></td>
                </tr>
            <?php $i++;
            } ?>
        </tbody>
    </table>

    <!-- <table border="0" width="100%" cellpadding="30">
		<tr>
			<td>
				ผู้ส่ง ___________________________ (Q-Tech)
			</td>
			<td>
				ผู้รับ ___________________________ (ERP-AX)
			</td>
		</tr>
	</table> -->
</body>

</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf = new mPDF('th', 'A4', 0, '', 2, 2, 2, 2);
$mpdf->WriteHTML($html);
$mpdf->Output();
