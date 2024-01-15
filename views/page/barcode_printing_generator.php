<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Barcode Printing</title>
	<link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" />
	<style>
		.barcode {
			padding: 0;
			margin: 0;
			vertical-align: top;
			color: #000000;
			height: 20px;
			font-weight: normal;
		}

		.barcodecell {
			text-align: center;
			vertical-align: middle;
			height: 20px;
		}
	</style>
</head>

<body>
	<?php
	$i = 1;

	use App\Components\Security;

	$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
	// $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	foreach ($barcode as $v) {
	?>

		<img src="data:image/png;base64,<?php echo  base64_encode($generator->getBarcode($v, $generator::TYPE_CODE_128, 2, 47)) ?>">
		<div style="padding: 0; margin: 0; text-align: center; font-size: 6px; font-weight: bold;"><?php echo (new Security)->_decode($v); ?></div>

		<!-- <div class="col-xs-3" style="width:135px; margin-bottom: 40px;">
			<img width="140" height="20" src="data:image/png;base64,<?php echo  base64_encode($generator->getBarcode($v, $generator::TYPE_CODE_128)) ?>">
			<div style="padding: 0; margin: auto; text-align: center; font-size: 10px;">
				<?php echo (new Security)->_decode($v); ?>
			</div>
		</div>
 -->
	<?php
	}
	?>

</body>

</html <?php

		$html = ob_get_contents();
		ob_end_clean();
		$mpdf = new mPDF('th', [40, 11], 0, '', 2, 2, 0, 0);
		// $mpdf=new mPDF('th','A4');
		$mpdf->WriteHTML($html);
		$mpdf->Output();
