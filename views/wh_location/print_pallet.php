<?php 

function getBarcodePallet($pallet_no) {
	$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
	return "
		<img width='300' height='60' src='data:image/png;base64," . 
			base64_encode($generator->getBarcode($pallet_no, $generator::TYPE_CODE_128)) . 
		"'/>
		<br />
		<div>" . $pallet_no . "</div>";
}

$mpdf = new mPDF(
  'th', // mode
  'A4-L', // format,
  0, // font size,
  '', // default font
  3, // margin left
  3, // margin right
  3, // margin top
  3, // margin bottom
  9, // margin header ?
  9, // margin footer ?
  'P' // orientation
);

$mpdf->WriteHTML("
	<table width='100%'>
		<tr>
			<td style='text-align: center;'>
				" . getBarcodePallet($pallet_no) . "
			</td>
			<td style='text-align: center;'>
				" . getBarcodePallet($pallet_no) . "
			</td>
		</tr>
	</table>
");

$mpdf->Output();