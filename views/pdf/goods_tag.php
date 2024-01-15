<?php 
ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Goods Tag</title>
</head>
<body style="height: 100%;">
  <table border="1" width="100%" height="100%" cellspacing="0" cellpadding="10">
    <tr>
      <td width="25%" style="padding-left: 20px; font-size: 2em;"><?php echo $lpn; ?></td>
      <td colspan="2" align="center" style="padding: 10px; font-size: 4em;">Goods tags</td>
    </tr>
    <tr>
      <td valign="top">
        WH
        <div style="font-size: 4em;">
          STRFG1
        </div>  
      </td>
      <td colspan="2" valign="middle" style="font-size: 3em;">Item : <?php echo $item_id;?></td>
    </tr>
    <tr>
      <td height="150" valign="top">
        Location
        <div style="font-size: 4em;">
           <?php echo $location;?>
        </div>
      </td>
      <td colspan="2" rowspan="2" valign="top" style="font-size: 4em;" height="300">
        <?php echo $item_name;?>
      </td>
    </tr>
    <tr>
      <td height="150" valign="top">
         Batch No.
        <div style="font-size: 4em;">
          <?php echo $batch;?>
        </div>
      </td>
    </tr>
    <tr>
      <td rowspan="2" valign="top">
        Shift
        <div style="font-size: 10em;">
          <?php echo $shift;?>
        </div>
      </td>
      <td rowspan="2" valign="top">
         <div style="font-size: 3em;">
           Receive : <?php echo date('d-m-Y H:i', strtotime($receive));?>
         </div>

         <table width="100%" cellpadding="10" style="margin-top: 10px;">
           <tr>
             <td style="font-size: 2em;" align="center">
               QTY per Pallet <br> <br>
               7
             </td>
             <td width="50%">
              <?php 
                $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                echo '<img width="300" height="60" src="data:image/png;base64,' . base64_encode($generator->getBarcode($lpn, $generator::TYPE_CODE_128)) . '"><br />';
              ?>
             </td>
           </tr>
           <tr>
             <td style="font-size: 2em;">
               Remark : 
             </td>
           </tr>
         </table>
      </td>
      <td width="15%" style="font-size: 3em; border-bottom: 1px;" align="center" valign="top">QTY</td>
    </tr>
    <tr>
      <td width="15%" style="font-size: 4em;" align="center">
        <?php echo $qty;?>
      </td>
    </tr>
  </table>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf=new mPDF(
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
  'L' // orientation
);
$mpdf->WriteHTML($html);
$mpdf->Output();