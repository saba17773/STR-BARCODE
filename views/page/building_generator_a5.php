<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" />
	<title>Building MC.</title>
</head>
<body >
	<table border="0" width="100%">
		<tr>
			<td align="center" valign="top">
				<br>
				<br>
				<br>
				<br>
				<div style="font-size: 3.5em; font-weight: bold;" class="text-center">BUILDING MC.</div>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top">
				<br><br><br><br><br><br><br>
				<div style="font-size: 9em; text-align: center; margin-top: 80px; font-weight: bold;"><?php echo $barcode; ?></div>
				<br><br><br><br><br><br><br><br><br>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top">
				<?php 
					$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
					echo '<img width="250" height="60" src="data:image/png;base64,' . base64_encode($generator->getBarcode($barcode, $generator::TYPE_CODE_128)) . '"><br />';
				?>
			</td>
		</tr>
	</table>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf=new mPDF('th','A5');
$mpdf->WriteHTML($html);
$mpdf->Output();