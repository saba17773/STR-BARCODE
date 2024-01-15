<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Report Curing</title>
</head>
<body>
	<table width="100%" border="1" cellspacing="0">
		<tr>
			<td style="text-align: center;">
				<img src="<?php echo APP_ROOT; ?>/assets/images/str.jpg" width="150" alt="">
			</td>
			<td style="text-align: center; padding: 30px;">
				<div>SIAMTRUCK RADIAL CO. LTD.</div>
				<div>Curing Report</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding: 10px; border-bottom: 0px;">
				<div class="text-center">Date : 23-01-2017 Weekly : GOLF Shift : กลางวัน </div>
			</td>
		</tr>
	</table>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf=new mPDF('th','A4-L', 0, '', 2, 2, 2, 2);
$mpdf->WriteHTML($html);
$mpdf->Output();