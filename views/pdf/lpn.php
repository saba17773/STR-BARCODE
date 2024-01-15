<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>LPN</title>
</head>
<body>
  <h1 align="center" style="font-size: 3em;"><?php echo $item_id;?></h1>
  <h2 align="center"><?php echo $item_name;?></h2>
  <h1 align="center" style="font-size: 3em; padding-bottom: -10px;"><?php echo $batch;?></h1>
  <p align="center">
    <?php 
      $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
      echo '<img width="300" height="60" src="data:image/png;base64,' . base64_encode($generator->getBarcode($lpn, $generator::TYPE_CODE_128)) . '"><br />';
    ?>
    <span align="center"><?php echo $lpn;?></span>
  </p>
  
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf=new mPDF(
  'th', // mode
  'A6', // format,
  0, // font size,
  '', // default font
  5, // margin left
  5, // margin right
  3, // margin top
  5, // margin bottom
  9, // margin header ?
  9, // margin footer ?
  'P' // orientation
);
$mpdf->WriteHTML($html);
$mpdf->Output();