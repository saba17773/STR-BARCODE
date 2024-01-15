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
  <?php foreach($data as $v) {?>
  <h1 align="center" style="font-size: 4em;"><?php echo $v['item_id'];?></h1>
  <div align="center" style="font-size: 1.8em; font-weight: bold;"><?php echo $v['item_name'];?></div>
  <div align="center" style="font-family: Arial; font-size: 8.7em; font-weight: bold; padding: 100px 0px;"><?php echo $v['batch'];?></div>
  <p align="center">
    <?php 
      $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
      echo '<img width="300" height="60" src="data:image/png;base64,' . base64_encode($generator->getBarcode($v['lpn'], $generator::TYPE_CODE_128)) . '"><br />';
    ?>
    <span align="center"><?php echo $v['lpn'];?></span>
  </p>
<?php } ?>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf=new mPDF(
  'th', // mode
  'A5', // format,
  0, // font size,
  '', // default font
  3, // margin left
  3, // margin right
  3, // margin top
  1, // margin bottom
  0, // margin header ?
  0, // margin footer ?
  'P' // orientation
);
$mpdf->WriteHTML($html);
$mpdf->Output();