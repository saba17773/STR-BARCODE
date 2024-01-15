<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Report Curetire Scrap</title>
	<style>
		body {
			font-size: 11px;
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
				<div>Curetire Scrap Report</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding: 10px; border-bottom: 0px;">
				Scrap Date : <?php echo $date; ?>&nbsp;&nbsp;&nbsp;&nbsp;
				BOI : <?php echo $BOIName; ?>
			</td>
		</tr>
	</table>
	<table border="1" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th style="border-top: 0px; padding: 10px; width:7%">ลำดับ</th>
				<th style="border-top: 0px; padding: 10px; width:8%">GT.Code</th>
				<th style="border-top: 0px; padding: 10px; width:8%">Cure Code</th>
				<th style="border-top: 0px; padding: 10px; width:9%">Item No.</th>
				<th style="border-top: 0px; padding: 10px; width:11%">Barcode</th>
				<th style="border-top: 0px; padding: 10px; width:6%">Press No.</th>
				<th style="border-top: 0px; padding: 10px; width:15%">Curing Date</th>
				<th style="border-top: 0px; padding: 10px; width:22%">Defect Description</th>
				<th style="border-top: 0px; padding: 10px; width:8%">Weekly</th>
				<th style="border-top: 0px; padding: 10px; width:6%">กลุ่ม</th>
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
					<td style="padding: 5px; width: 80px;"><?php echo $value["GT_Code"]; ?></td>
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
					<td style="padding: 5px; text-align: left;"><?php echo date('d-m-Y H:i', strtotime($value["CuringDate"])); ?></td>
					<td style="padding: 5px; text-align: left;"><?php echo $value["DefectDesc"]; ?></td>
					<td style="padding: 5px; text-align: center;"><?php echo $value["Batch"]; ?></td>
					<td style="padding: 5px; text-align: center;"><?php echo $value["Shift"]; ?></td>
				</tr>
			<?php $i++;
			} ?>
		</tbody>
	</table>

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
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf = new mPDF('th', 'A4', 0, '', 2, 2, 2, 30);
$mpdf->SetHTMLFooter('
<table class="table" width="100%">
<tr class="tr">
    <td class="td" align="left">
        Ref.WI-MP-1.15
    </td>
    <td class="td" align="right">
        FM-MP-1.15.1,Issue #3
    </td>
</tr>
</table>
');
$mpdf->WriteHTML($html);
$mpdf->Output();
