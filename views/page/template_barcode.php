<?php 
// exit('Under construction');
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Template Barcode</title>
	<link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/bootstrap.min.css" />
</head>
<body>
	<?php 
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		$txt = substr($from, 0, 3);
		$from = substr($from, 3);
		$to = substr($to, 3);
		$i = 0;
		for ($i=$from; $i <= $to; $i++) { 
			$txt_show = $txt . str_pad($i, 6, "0", STR_PAD_LEFT);  ?>
			
			<!-- <div class="col-xs-3" style="width:135px; margin-bottom: 7px;">
				<img width="140px" height="20px" src="data:image/png;base64,<?php echo  base64_encode($generator->getBarcode($txt_show, $generator::TYPE_CODE_128))?>">
				<div style="padding: 0; margin: auto; text-align: center; font-size: 10px;">
					<?php echo $txt_show; ?>
				</div>
			</div> -->
	
			<!-- <div class="col-xs-3" style="width:135px; margin-bottom: 30px;">
				<img width="140px" height="20px" src="data:image/png;base64,<?php echo  base64_encode($generator->getBarcode($txt_show, $generator::TYPE_CODE_128))?>">
				<div style="padding: 0; margin: auto; text-align: center; font-size: 10px;">
					<?php echo $txt_show; ?>
				</div>
			</div> -->

			<div class="col-xs-3" style="width:240px; margin-bottom: 30px; padding-bottom: 60px;">
				<img width="140px" height="20px" src="data:image/png;base64,<?php echo  base64_encode($generator->getBarcode($txt_show, $generator::TYPE_CODE_128))?>">
				<div style="padding-left: 40px; margin: auto; text-align: left; font-size: 10px;">
					<?php echo $txt_show; ?>
				</div>
			</div>

	<?php } ?>
</body>
</html

<?php

$html = ob_get_contents();
ob_end_clean();
// $mpdf=new mPDF('th',[40,9], 0, '', 0, 0, 0.5, 0, 0, 0);
// $mpdf=new mPDF('th', 'A4');
$mpdf=new mPDF('th', 'A4-L', 0, '', 10, 0, 7, 5, 10, 5);
$mpdf->WriteHTML($html);
$mpdf->Output();