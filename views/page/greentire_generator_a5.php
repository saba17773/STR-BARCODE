<?php ob_start();
$barcodeGreentrie = explode(",",$barcode);


 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" />
  <style>
p.ex1 {

     position: fixed;
     top: 500px;




}
</style>
	<title>Greentire Barcode</title>

</head>
<body >
  <br><br><br>
  <div style="font-size: 3.5em; font-weight: bold;" class="text-center">GREEN TIRE CODE</div>
	<table border="0" width="100%">
		<!-- <tr>
			<td align="center" valign="top">
				<br>
				<br>
				<br>
				<br>
				<div style="font-size: 3.5em; font-weight: bold;" class="text-center">GREEN TIRE CODE</div>
			</td>
		</tr> -->
		<tr>
			<td align="center" valign="top">
				<br><br><br><br><br><br><br><br><br><br>
				<div style="font-size: 5em; text-align: center; margin-top: 80px; font-weight: bold;"><?php echo  urldecode($barcodeGreentrie[1]); ?></div>
				<!-- <br><br><br><br><br><br><br><br><br><br> -->
			</td>
		</tr>
		<!-- <tr>
			<td align="center" valign="top">

			</td>
		</tr> -->
	</table>

 <p class="ex1"  ><div style="margin-left:24%;"><?php
    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    echo '<img width="250" height="60" src="data:image/png;base64,' . base64_encode($generator->getBarcode(urldecode($barcodeGreentrie[0]), $generator::TYPE_CODE_128)) . '"><br />';
  ?></div></p>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf=new mPDF('th','A5');
$mpdf->WriteHTML($html);
$mpdf->Output();
