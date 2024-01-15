<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=Curetire_Scrap_Report_" . Date("Ymd_His") . ".xls");
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
	<table border="1" cellspacing="0" width="100%">
		<thead>
			<tr>
				<td colspan="2" align="center">

				</td>
				<td align="center" colspan="13" class="f14">
					<b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>Curetire Scrap Report</b>
				</td>
			</tr>
			<tr>
				<td align="left" colspan="15" class="f10">
					<br>Scrap Date : <?php echo $date; ?>&nbsp;&nbsp;&nbsp;&nbsp;
					BOI : <?php echo $BOIName; ?>

				</td>
			</tr>
			<tr>
				<th style="border-top: 0px; padding: 10px;">ลำดับ</th>
				<th style="border-top: 0px; padding: 10px;">Build MC</th>
				<th style="border-top: 0px; padding: 10px;">Date Build</th>
				<th style="border-top: 0px; padding: 10px;">GT.Code</th>
				<th style="border-top: 0px; padding: 10px;">Date Hold</th>
				<th style="border-top: 0px; padding: 10px;">Defect Description</th>
				<th style="border-top: 0px; padding: 10px;">Date Cure</th>
				<th style="border-top: 0px; padding: 10px;">Cure Code</th>
				<th style="border-top: 0px; padding: 10px;">Item No.</th>
				<th style="border-top: 0px; padding: 10px;">Barcode</th>
				<th style="border-top: 0px; padding: 10px;">Press No.</th>
				<th style="border-top: 0px; padding: 10px;">Defect Description</th>
				<th style="border-top: 0px; padding: 10px;">Weekly</th>
				<th style="border-top: 0px; padding: 10px;">กลุ่ม</th>
				<th style="border-top: 0px; padding: 10px;">NetWeight</th>
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
					// echo $i . var_dump($inr) . "<br>";
					// echo $i . " " . trim($value['Barcode']) . "<br>";
					// $i++;
				} else {
					foreach ($all_data as $z) {
						if (trim($z['Barcode']) === trim($value['Barcode'])) {
							$z['Barcode'] = $value['Barcode'];
							$z['CuringCode'] = $value['CuringCode'];
							$z["ItemID"] = $value["ItemID"];
							$z["DefectID"] = $value["DefectID"];
							$z["DefectDesc"] = $value["DefectDesc"];
							$z["Batch"] = $value["Batch"];
							$z["Shift"] = $value["Shift"];
							$z["PressNo"] = $var_dump["PressNo"];
							$data_[] = $z;
						}
					}
				}
			}
			?>
			<?php foreach ($data_ as $value) {

			?>
				<tr>
					<td style="padding: 5px; text-align: center;"><?php echo $i; ?></td>
					<td style="padding: 5px; width: 80px;"><?php echo $value["BuildingNo"]; ?></td>
					<td style="padding: 5px; width: 80px;"><?php echo  date('d-m-Y H:i', strtotime($value["DateBuild"])); ?></td>
					<td style="padding: 5px; width: 80px;"><?php echo $value["GT_Code"]; ?></td>
					<td style="padding: 5px; width: 80px;"><?php echo date('d-m-Y H:i', strtotime($value["CreateDateHold"])); ?></td>
					<td style="padding: 5px; text-align: left;"><?php echo $value["DefectDescHold"]; ?></td>
					<td style="padding: 5px; width: 80px;"><?php echo date('d-m-Y H:i', strtotime($value["CuringDate"])); ?></td>
					<td style="padding: 5px; width: 80px;"><?php echo $value["CuringCode"]; ?></td>
					<td style="padding: 5px;">
						<?php
						if ($value["ItemID"] === null || $value["ItemID"] === "") {
							echo "-";
						} else {
							echo $value["ItemID"];
						}
						?>
					</td>
					<td style="padding: 5px;"><?php echo $value["Barcode"]; ?></td>
					<td style="padding: 5px;">
						<?php
						if ($value["PressNo"] === null || $value["PressNo"] === "") {
							echo "-";
						} else {
							echo $value["PressNo"];
						}
						?>
					</td>
					<td style="padding: 5px; text-align: left;"><?php echo $value["DefectDesc"]; ?></td>
					<td style="padding: 5px; text-align: center;"><?php echo $value["Batch"]; ?></td>
					<td style="padding: 5px; text-align: center;"><?php echo $value["Shift"]; ?></td>
					<td style="padding: 5px; text-align: Right;"><?php echo number_format($value["NetWeight"], 4); ?></td>
					
				</tr>
			<?php $i++;
			} ?>
			<tr>
                <td colspan="14" align="Right"><b> Total</b></td>
                <td align="Right">
                <?php                    
                    $sumtotal = 0;
                    foreach ($data as $key => $value) {
                        $sumtotal += $value['NetWeight'];
                    }
                    echo number_format($sumtotal, 4); ?>
                </td>
            </tr>
		</tbody>
	</table>
	<br>
	<table border="0" width="100%" cellpadding="40">
		<tr>
			<td style="text-align: center; font-weight: bold;">
				________________________________
				<br> Operator
			</td>
			<td style="text-align: center; font-weight: bold;">
				________________________________
				<br> Leader
			</td>
		</tr>
	</table>
</body>

</html>