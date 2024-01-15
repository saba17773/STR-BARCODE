<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" />
	<title>Barcode Building MC. Barcode</title>
</head>
<body>
	<table border="1">
		<tr>
			<td width="500px" height="1294px" align="center" valign="top" style="padding: 60px;">
				<br><br><br><br><br><br><br><br><br><br><br><br>
				<div style="font-size: 6em; margin-bottom: 50px">BUILDING MC.</div>
				<br><br><br><br><br><br><br><br><br><br><br><br>
				<div style="font-size: 17em; font-weight: bold; text-align: center; margin-top: 80px;">
					<?php echo $barcode; ?>
				</div>
				<br><br><br><br><br><br><br><br><br><br><br><br>
				<?php
					$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
					echo '<img width="100%" height="120" src="data:image/png;base64,' . base64_encode($generator->getBarcode($barcode, $generator::TYPE_CODE_128)) . '"><br />';
				?>
			</td>
			<td width="500px" height="1294px" align="center" valign="top" style="padding: 60px">
				<br><br><br><br><br><br><br><br><br><br><br><br>
				<div style="font-size: 6em; margin-bottom: 50px">BUILDING MC.</div>
				<br><br><br><br><br><br><br><br><br><br><br><br>
				<div style="font-size: 17em; font-weight: bold; text-align: center; margin-top: 80px;">
					<?php echo $barcode; ?>
				</div>
				<br><br><br><br><br><br><br><br><br><br><br><br>
				<?php
					$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
					echo '<img width="100%" height="120" src="data:image/png;base64,' . base64_encode($generator->getBarcode($barcode, $generator::TYPE_CODE_128)) . '"><br />';
				?>
			</td>
		</tr>
		<tr>
			<td width="500px" height="1294px" align="center" valign="top" style="padding: 60px">
				<br><br><br><br><br><br><br><br><br><br><br><br>
				<div style="font-size: 6em; margin-bottom: 50px">BUILDING MC.</div>
				<br><br><br><br><br><br><br><br><br><br><br><br>
				<div style="font-size: 17em; font-weight: bold; text-align: center; margin-top: 80px;">
					<?php echo $barcode; ?>
				</div>
				<br><br><br><br><br><br><br><br><br><br><br><br>
				<?php
					$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
					echo '<img width="100%" height="120" src="data:image/png;base64,' . base64_encode($generator->getBarcode($barcode, $generator::TYPE_CODE_128)) . '"><br />';
				?>
			</td>
			<td width="500px" height="1294px" align="center" valign="top" style="padding: 60px">
				<br><br><br><br><br><br><br><br><br><br><br><br>
				<div style="font-size: 6em; margin-bottom: 50px">BUILDING MC.</div>
				<br><br><br><br><br><br><br><br><br><br><br><br>
				<div style="font-size: 17em; font-weight: bold; text-align: center; margin-top: 80px;">
					<?php echo $barcode; ?>
				</div>
				<br><br><br><br><br><br><br><br><br><br><br><br>
				<?php
					$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
					echo '<img width="100%" height="120" src="data:image/png;base64,' . base64_encode($generator->getBarcode($barcode, $generator::TYPE_CODE_128)) . '"><br />';
				?>
			</td>
		</tr>
	</table>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf=new mPDF('th','A4', 0, 0, 0, 0, 0, 0);
$mpdf->WriteHTML($html);
$mpdf->Output();
