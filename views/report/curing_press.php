<?php ob_start();

function getShift()
{
	if ($_SESSION['Shift'] === 1) {
		$s = 'A';
	} else if ($_SESSION['Shift'] === 2) {
		$s = 'B';
	} else {
		$s = 'D';
	}
	return $s;
}

function getRemark($curingCode, $no, $L)
{
	if ($curingCode === $L[$no]->CuringCode) {
		return '';
	} else {
		return 'L = ' . $L[$no]->CuringCode . ', R = ' . $curingCode;
	}
}

function compareLR($L, $R)
{
	$countL = count($L);
	$countR = count($R);
	$countDiff = 0;
	$addSide = '';

	if ($countL > $countR) {
		$countDiff = (int) ($countL - $countR);
		$addSide = 'R';
	} else if ($countL < $countR) {
		$countDiff = (int) ($countR - $countL);
		$addSide = 'L';
	} else {
		$countDiff = 0;
	}
	return [$countDiff, $addSide];
}

$RCuringCode = '';
$LCuringCode = '';
$LGTCode = '';
$RGTCode = '';

$tempL = [];
$tempR = [];

$tempGTL = [];
$tempGTR = [];
foreach ($L as $v) {

	if (isset($v->CuringCode)) {
		if (!in_array($v->CuringCode, $tempL)) {
			$tempL[] = $v->CuringCode;
			$LCuringCode .= $v->CuringCode . ', ';
		}
	} else {
		$LCuringCode = '';
	}

	if (isset($v->GT_Code)) {
		// $LGTCode = $v->GT_Code;
		if (!in_array($v->GT_Code, $tempR)) {
			$tempR[] = $v->GT_Code;
			$LGTCode .= $v->GT_Code . ', ';
		}
	} else {
		$LGTCode = '';
	}
} ?>
<?php foreach ($R as $v) {

	if (isset($v->CuringCode)) {
		if (!in_array($v->CuringCode, $tempGTL)) {
			$tempGTL[] = $v->CuringCode;
			$RCuringCode .= $v->CuringCode . ', ';
		}
	} else {
		$RCuringCode = '';
	}

	if (isset($v->GT_Code)) {
		// $RGTCode = $v->GT_Code;
		if (!in_array($v->GT_Code, $tempGTR)) {
			$tempGTR[] = $v->GT_Code;
			$RGTCode .= $v->GT_Code . ', ';
		}
	} else {
		$RGTCode = '';
	}
}

$COUNT_SPL = 1;
$COUNT_SPR = 1;

$SPL = [
	'L20' => [],
	'L40' => [],
	'L60' => []
];
$SPR = [
	'R20' => [],
	'R40' => [],
	'R60' => []
];

foreach ($L as $V) {

	if ($COUNT_SPL <= 20) {
		$STACK_L = 'L20';
	} else if ($COUNT_SPL > 20 && $COUNT_SPL <= 40) {
		$STACK_L = 'L40';
	} else if ($COUNT_SPL > 40 && $COUNT_SPL <= 60) {
		$STACK_L = 'L60';
	}

	$SPL[$STACK_L][] = $V;
	$COUNT_SPL++;
}

foreach ($R as $V) {

	if ($COUNT_SPR <= 20) {
		$STACK_R = 'R20';
	} else if ($COUNT_SPR > 20 && $COUNT_SPR <= 40) {
		$STACK_R = 'R40';
	} else if ($COUNT_SPR > 40 && $COUNT_SPR <= 60) {
		$STACK_R = 'R60';
	}

	$SPR[$STACK_R][] = $V;
	$COUNT_SPR++;
}

// $SIDEVOL = compareLR($L, $R)[1];

// echo count($$SIDEVOL);
// exit;

// echo "<pre>" . print_r(compareLR($L, $R), true) . '</pre>';
// exit;

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Report Cured</title>
	<style>
		body {
			font-size: 14px;
		}

		.footer-left {
			text-align: left;
		}

		.footer-right {
			text-align: right;
		}
	</style>

</head>

<body>
	<!-- 20 -->
	<?php if (count($SPL['L20']) !== 0 || count($SPR['R20']) !== 0) : ?>
		<table width="100%" border="1" cellspacing="0">

			<tr>
				<th width="50%"><img src="./assets/images/str.jpg" width="200" alt=""></th>
				<th style="text-align: center; padding: 30px;" width="50%">
					<div style="font-size: 2em; font-weight: bold;"> <?php  echo $txthead; ?> </div>
				</th>
			</tr>
			<tr>
				<th colspan="2">
					<table width="100%" border="0" cellpadding="3">
						<tr>
							<th style="text-align: left;">Date : <?php echo date('d-m-Y', strtotime($date_curing)); ?></th>
							<th style="text-align: left;">Press : <?php echo $press_no; ?> L / <?php echo trim($LCuringCode, ', '); ?></th>
							<th style="text-align: left;">Press : <?php echo $press_no; ?> R / <?php echo trim($RCuringCode, ', '); ?></th>
							<th style="text-align: right;">กลุ่ม : ________<?php //echo getShift(); 
																			?> / กะ : <?php echo $shift; ?></th>
						</tr>
						<tr>
							<th style="text-align: left;">GT Code(L) : <?php echo trim($LGTCode, ', '); ?></th>
							<th style="text-align: left;">GT Code(R) : <?php echo trim($RGTCode, ', '); ?></th>
							<th style="text-align: left;">Weekly : <?php echo $weekly; ?></th>
							<th style="text-align: right;">ผู้บันทึก : ____________________________________________<?php //echo $_SESSION['user_name']; 
																													?></th>
						</tr>
					</table>
				</th>
			</tr>
			</thead>
			<tbody>
				<tr>
					<th style="padding: 0; border: 0; display: inline-block; table-layout: fixed;" valign="top" width="50%">
						<!-- L -->
						<table width="100%" border="1" cellspacing="0" cellpadding="5" style="margin: 0; border-right: 0; table-layout: fixed">
							<tr>
								<td rowspan="2"><i><u>เวลาที่อบ</u><i></td>
								<td rowspan="2">ลำดับ</td>
								<td colspan="4">L</td>
							</tr>
							<tr>
								<td>Serial L</td>
								<td><i><u>ID Barcode</u></i></td>
								<td><i><u>FinalReceive</u></i></td>
								<td><i><u>Build Shift</u></i></td>

							</tr>
							<?php $LNo = 1;
							foreach ($SPL['L20'] as $v) { ?>
								<tr>
									<td width="150"><?php echo date('d-m-Y H:i', strtotime($v->CuringDate)); ?></td>
									<td width="50"><?php echo $LNo; ?></td>
									<td width="100"><?php echo $v->TemplateSerialNo; ?></td>
									<td width="100"><?php echo $v->Barcode; ?></td>
									<td width="100"><?php echo $v->CHECK_Mustache; ?></td>
									<td width="100"><?php echo $v->Shift; ?></td>

								</tr>
							<?php $LNo++;
							} ?>
							<?php
							if (compareLR($L, $R)[1] === 'L') {
								$number = compareLR($SPL['L20'], $SPR['R20'])[0];
								for ($i = 0; $i < $number; $i++) { ?>
									<tr>
										<td width="150"><br></td>
										<td width="50"><br></td>
										<td width="100"><br></td>
										<td width="100"><br></td>
										<td width="100"><br></td>
									</tr>
							<?php
								}
							} ?>
							<tr>
								<td colspan="7" style="padding: 10px;"><i><u>รวม L : <?php echo $LNo - 1; ?></u></i></td>
							</tr>
						</table>
					</th>
					<th style="padding: 0; border: 0; display: inline-block; table-layout: fixed;" valign="top" width="50%">
						<!-- R -->
						<table width="100%" border="1" cellspacing="0" cellpadding="5" style="margin: 0; border-left: 0; table-layout: fixed;">
							<tr>
								<td rowspan="2"><i><u>เวลาที่อบ</u></i></td>
								<td rowspan="2"><i><u>ลำดับ</u></i></td>
								<td colspan="4">R</td>
								<td rowspan="2"><i><u>หมายเหตุ</u></i></td>
							</tr>
							<tr>
								<td>Serial R</td>
								<td><i><u>ID Barcode</u></i></td>
								<td><i><u>FinalReceive</u></i></td>
								<td><i><u>Build Shift</u></i></td>

								<!-- <td><i><u>Build Shift</u></i></td> -->


							</tr>
							<?php $RNo = 1;
							foreach ($SPR['R20'] as $v) { ?>
								<tr>
									<td width="150"><?php echo date('d-m-Y H:i', strtotime($v->CuringDate)); ?></td>
									<td width="50"><?php echo $RNo; ?></td>
									<td width="100"><?php echo $v->TemplateSerialNo; ?></td>
									<td width="100"><?php echo $v->Barcode; ?></td>
									<td width="100"><?php echo $v->CHECK_Mustache; ?></td>
									<td width="100"><?php echo $v->Shift; ?></td>
									<td width="400"><?php echo getRemark($v->CuringCode, $RNo - 1, $L); ?></td>

								</tr>
							<?php $RNo++;
							} ?>
							<?php
							if (compareLR($L, $R)[1] === 'R') {
								$number = compareLR($SPL['L20'], $SPR['R20'])[0];
								for ($i = 0; $i < $number; $i++) { ?>
									<tr>
										<td width="150"><br></td>
										<td width="50"><br></td>
										<td width="100"><br></td>
										<td width="100"><br></td>
										<td width="100"><br></td>
										<td width="400"><br></td>
									</tr>
							<?php
								}
							} ?>
							<tr>
								<td colspan="7" style="padding: 10px;"><i><u>รวม R : <?php echo $RNo - 1; ?></u></i></td>
							</tr>
						</table>
					</th>
				</tr>
				<tr>
					<td colspan="11" style="padding: 10px; text-align: center;"><i><u>รวมยอดทั้งหมด <?php echo (int) ($LNo + $RNo) - 2; ?> เส้น </u></i></td>
				</tr>
			</tbody>
		</table>
	<?php endif; ?>
	<br><br />
	<br>
	<br>
	<br>


	<!-- 40 -->
	<?php if (count($SPL['L40']) !== 0 || count($SPR['R40']) !== 0) : ?>
		<table width="100%" border="1" cellspacing="0">

			<tr>
				<th width="50%"><img src="./assets/images/str.jpg" width="200" alt=""></th>
				<th style="text-align: center; padding: 30px;" width="50%">
					<div style="font-size: 2em; font-weight: bold;"><?php  echo $txthead; ?></div>
				</th>
			</tr>
			<tr>
				<th colspan="2">
					<table width="100%" border="0" cellpadding="3">
						<tr>
							<th style="text-align: left;">Date : <?php echo date('d-m-Y', strtotime($date_curing)); ?></th>
							<th style="text-align: left;">Press : <?php echo $press_no; ?> L / <?php echo trim($LCuringCode, ', '); ?></th>
							<th style="text-align: left;">Press : <?php echo $press_no; ?> R / <?php echo trim($RCuringCode, ', '); ?></th>
							<th style="text-align: right;">กลุ่ม : ________<?php //echo getShift(); 
																			?> / กะ : <?php echo $shift; ?></th>
						</tr>
						<tr>
							<th style="text-align: left;">GT Code(L) : <?php echo trim($LGTCode, ', '); ?></th>
							<th style="text-align: left;">GT Code(R) : <?php echo trim($RGTCode, ', '); ?></th>
							<th style="text-align: left;">Weekly : <?php echo $weekly; ?></th>
							<th style="text-align: right;">ผู้บันทึก : ____________________________________________<?php //echo $_SESSION['user_name']; 
																													?></th>
						</tr>
					</table>
				</th>
			</tr>
			</thead>
			<tbody>
				<tr>
					<th style="padding: 0; border: 0; display: inline-block; table-layout: fixed;" valign="top" width="50%">
						<!-- L -->
						<table width="100%" border="1" cellspacing="0" cellpadding="5" style="margin: 0; border-right: 0; table-layout: fixed">
							<tr>
								<td rowspan="2"><i><u>เวลาที่อบ</u><i></td>
								<td rowspan="2">ลำดับ</td>
								<td colspan="4">L</td>
							</tr>
							<tr>
								<td>Serial L</td>
								<td><i><u>ID Barcode</u></i></td>
								<td><i><u>FinalReceive</u></i></td>
								<td><i><u>Build Shift</u></i></td>

							</tr>
							<?php $LNo = 21;
							foreach ($SPL['L40'] as $v) { ?>
								<tr>
									<td width="150"><?php echo date('d-m-Y H:i', strtotime($v->CuringDate)); ?></td>
									<td width="50"><?php echo $LNo; ?></td>
									<td width="100"><?php echo $v->TemplateSerialNo; ?></td>
									<td width="100"><?php echo $v->Barcode; ?></td>
									<td width="100"><?php echo $v->CHECK_Mustache; ?></td>
									<td width="100"><?php echo $v->Shift; ?></td>

								</tr>
							<?php $LNo++;
							} ?>
							<?php
							if (compareLR($L, $R)[1] === 'L') {
								$number = compareLR($SPL['L40'], $SPR['R40'])[0];
								for ($i = 0; $i < $number; $i++) { ?>
									<tr>
										<td width="150"><br></td>
										<td width="50"><br></td>
										<td width="100"><br></td>
										<td width="100"><br></td>
										<td width="100"><br></td>
									</tr>
							<?php
								}
							} ?>
							<tr>
								<td colspan="7" style="padding: 10px;"><i><u>รวม L : <?php echo $LNo - 1; ?></u></i></td>
							</tr>
						</table>
					</th>
					<th style="padding: 0; border: 0; display: inline-block; table-layout: fixed;" valign="top" width="50%">
						<!-- R -->
						<table width="100%" border="1" cellspacing="0" cellpadding="5" style="margin: 0; border-left: 0; table-layout: fixed;">
							<tr>
								<td rowspan="2"><i><u>เวลาที่อบ</u></i></td>
								<td rowspan="2"><i><u>ลำดับ</u></i></td>
								<td colspan="4">R</td>
								<td rowspan="2"><i><u>หมายเหตุ</u></i></td>
							</tr>
							<tr>
								<td>Serial R</td>
								<td><i><u>ID Barcode</u></i></td>
								<td><i><u>FinalReceive</u></i></td>
								<td><i><u>Build Shift</u></i></td>

								<!-- <td><i><u>Build Shift</u></i></td> -->


							</tr>
							<?php $RNo = 21;
							foreach ($SPR['R40'] as $v) { ?>
								<tr>
									<td width="150"><?php echo date('d-m-Y H:i', strtotime($v->CuringDate)); ?></td>
									<td width="50"><?php echo $RNo; ?></td>
									<td width="100"><?php echo $v->TemplateSerialNo; ?></td>
									<td width="100"><?php echo $v->Barcode; ?></td>
									<td width="100"><?php echo $v->CHECK_Mustache; ?></td>
									<td width="100"><?php echo $v->Shift; ?></td>
									<td width="400"><?php echo getRemark($v->CuringCode, $RNo - 1, $L); ?></td>

								</tr>
							<?php $RNo++;
							} ?>
							<?php
							if (compareLR($L, $R)[1] === 'R') {
								$number = compareLR($SPL['L40'], $SPR['R20'])[0];
								for ($i = 0; $i < $number; $i++) { ?>
									<tr>
										<td width="150"><br></td>
										<td width="50"><br></td>
										<td width="100"><br></td>
										<td width="100"><br></td>
										<td width="100"><br></td>
										<td width="400"><br></td>
									</tr>
							<?php
								}
							} ?>
							<tr>
								<td colspan="7" style="padding: 10px;"><i><u>รวม R : <?php echo $RNo - 1; ?></u></i></td>
							</tr>
						</table>
					</th>
				</tr>
				<tr>
					<td colspan="11" style="padding: 10px; text-align: center;"><i><u>รวมยอดทั้งหมด <?php echo (int) ($LNo + $RNo) - 2; ?> เส้น </u></i></td>
				</tr>
			</tbody>
		</table>

	<?php endif; ?>
	<br><br />
	<br>
	<br>
	<br>	
	<!-- 60 -->
	<?php if (count($SPL['L60']) !== 0 || count($SPR['R60']) !== 0) : ?>
		<table width="100%" border="1" cellspacing="0">

			<tr>
				<th width="50%"><img src="./assets/images/str.jpg" width="200" alt=""></th>
				<th style="text-align: center; padding: 30px;" width="50%">
					<div style="font-size: 2em; font-weight: bold;"><?php  echo $txthead; ?></div>
				</th>
			</tr>
			<tr>
				<th colspan="2">
					<table width="100%" border="0" cellpadding="3">
						<tr>
							<th style="text-align: left;">Date : <?php echo date('d-m-Y', strtotime($date_curing)); ?></th>
							<th style="text-align: left;">Press : <?php echo $press_no; ?> L / <?php echo trim($LCuringCode, ', '); ?></th>
							<th style="text-align: left;">Press : <?php echo $press_no; ?> R / <?php echo trim($RCuringCode, ', '); ?></th>
							<th style="text-align: right;">กลุ่ม : ________<?php //echo getShift(); 
																			?> / กะ : <?php echo $shift; ?></th>
						</tr>
						<tr>
							<th style="text-align: left;">GT Code(L) : <?php echo trim($LGTCode, ', '); ?></th>
							<th style="text-align: left;">GT Code(R) : <?php echo trim($RGTCode, ', '); ?></th>
							<th style="text-align: left;">Weekly : <?php echo $weekly; ?></th>
							<th style="text-align: right;">ผู้บันทึก : ____________________________________________<?php //echo $_SESSION['user_name']; 
																													?></th>
						</tr>
					</table>
				</th>
			</tr>
			</thead>
			<tbody>
				<tr>
					<th style="padding: 0; border: 0; display: inline-block; table-layout: fixed;" valign="top" width="50%">
						<!-- L -->
						<table width="100%" border="1" cellspacing="0" cellpadding="5" style="margin: 0; border-right: 0; table-layout: fixed">
							<tr>
								<td rowspan="2"><i><u>เวลาที่อบ</u><i></td>
								<td rowspan="2">ลำดับ</td>
								<td colspan="4">L</td>
							</tr>
							<tr>
								<td>Serial L</td>
								<td><i><u>ID Barcode</u></i></td>
								<td><i><u>FinalReceive</u></i></td>
								<td><i><u>Build Shift</u></i></td>

							</tr>
							<?php $LNo = 41;
							foreach ($SPL['L60'] as $v) { ?>
								<tr>
									<td width="150"><?php echo date('d-m-Y H:i', strtotime($v->CuringDate)); ?></td>
									<td width="50"><?php echo $LNo; ?></td>
									<td width="100"><?php echo $v->TemplateSerialNo; ?></td>
									<td width="100"><?php echo $v->Barcode; ?></td>
									<td width="100"><?php echo $v->CHECK_Mustache; ?></td>
									<td width="100"><?php echo $v->Shift; ?></td>

								</tr>
							<?php $LNo++;
							} ?>
							<?php
							if (compareLR($L, $R)[1] === 'L') {
								$number = compareLR($SPL['L60'], $SPR['R60'])[0];
								for ($i = 0; $i < $number; $i++) { ?>
									<tr>
										<td width="150"><br></td>
										<td width="50"><br></td>
										<td width="100"><br></td>
										<td width="100"><br></td>
										<td width="100"><br></td>
									</tr>
							<?php
								}
							} ?>
							<tr>
								<td colspan="7" style="padding: 10px;"><i><u>รวม L : <?php echo $LNo - 1; ?></u></i></td>
							</tr>
						</table>
					</th>
					<th style="padding: 0; border: 0; display: inline-block; table-layout: fixed;" valign="top" width="50%">
						<!-- R -->
						<table width="100%" border="1" cellspacing="0" cellpadding="5" style="margin: 0; border-left: 0; table-layout: fixed;">
							<tr>
								<td rowspan="2"><i><u>เวลาที่อบ</u></i></td>
								<td rowspan="2"><i><u>ลำดับ</u></i></td>
								<td colspan="4">R</td>
								<td rowspan="2"><i><u>หมายเหตุ</u></i></td>
							</tr>
							<tr>
								<td>Serial R</td>
								<td><i><u>ID Barcode</u></i></td>
								<td><i><u>FinalReceive</u></i></td>
								<td><i><u>Build Shift</u></i></td>

								<!-- <td><i><u>Build Shift</u></i></td> -->


							</tr>
							<?php $RNo = 1;
							foreach ($SPR['R60'] as $v) { ?>
								<tr>
									<td width="150"><?php echo date('d-m-Y H:i', strtotime($v->CuringDate)); ?></td>
									<td width="50"><?php echo $RNo; ?></td>
									<td width="100"><?php echo $v->TemplateSerialNo; ?></td>
									<td width="100"><?php echo $v->Barcode; ?></td>
									<td width="100"><?php echo $v->CHECK_Mustache; ?></td>
									<td width="100"><?php echo $v->Shift; ?></td>
									<td width="400"><?php echo getRemark($v->CuringCode, $RNo - 1, $L); ?></td>

								</tr>
							<?php $RNo++;
							} ?>
							<?php
							if (compareLR($L, $R)[1] === 'R') {
								$number = compareLR($SPL['L60'], $SPR['R60'])[0];
								for ($i = 0; $i < $number; $i++) { ?>
									<tr>
										<td width="150"><br></td>
										<td width="50"><br></td>
										<td width="100"><br></td>
										<td width="100"><br></td>
										<td width="100"><br></td>
										<td width="400"><br></td>
									</tr>
							<?php
								}
							} ?>
							<tr>
								<td colspan="7" style="padding: 10px;"><i><u>รวม R : <?php echo $RNo - 1; ?></u></i></td>
							</tr>
						</table>
					</th>
				</tr>
				<tr>
					<td colspan="11" style="padding: 10px; text-align: center;"><i><u>รวมยอดทั้งหมด <?php echo (int) ($LNo + $RNo) - 2; ?> เส้น </u></i></td>
				</tr>
			</tbody>
		</table>
	<?php endif; ?>
</body>

</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf = new mPDF('th', 'A4-L', 12, '', 2, 2, 2, 2, 9, 9, 'L');
if($txtfooter == '2'){
	$mpdf->SetHTMLFooter('
	<table width="100%">
	<tr>
	    <td class="footer-left">
	        Ref.WI-PP-2.12
	    </td>
	    <td class="footer-right">
	        FM-PP-2.12.1,Issued #2
	    </td>
	</tr>
	</table>
');
}else{
	$mpdf->SetHTMLFooter('
	<table width="100%">
	<tr>
	    <td class="footer-left">
	        Ref.WI-PP-2.12
	    </td>
	    <td class="footer-right">
	        FM-PP-2.12.1,Issued #3
	    </td>
	</tr>
	</table>
');
}
$mpdf->WriteHTML($html);
$mpdf->Output();
