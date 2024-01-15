<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=stock_onhand_" . Date("Ymd_His") . ".xls");
?>
<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">

  <title>Report Onhand Diff</title>
  <style>
    body {
      font-size: 0.8em;
    }
  </style>
</head>

<body>

  <table border="1" cellspacing="0" width="100%">
    <thead>
      <tr>
        <td align="center" colspan="10" class="f14">
          <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>Stock Counting Report</b>
        </td>
      </tr>
      <tr>
        <td colspan="10">
          Date : <?php echo date("Y-m-d H:i:s"); ?>
        </td>
      </tr>

      <tr style="background: #f5f5f5;">
        <th>No.</th>
        <th>Item</th>
        <th>Size</th>
        <th>Brand</th>
        <th>Batch</th>        
        <th>QTY</th>
        <th>Count</th>
        <th>Diff</th>
        <th>Year</th>
        <th>Type</th>
      </tr>
    </thead>
    <tbody>

      <?php
      $i = 1;
      $total = 0;
      $counted = 0;
      $diff = 0;
      foreach ($data as $value) { ?>
        <tr>
          <td><?php echo $i; ?></td>
          <td><?php echo $value["ItemID"]; ?></td>
          <td><?php echo $value["NameTH"]; ?></td>
          <td><?php echo $value["Brand"]; ?></td>
          <td><?php echo ($value["Batch"]); ?></td>          
          <td><?php echo number_format($value["QTY"]); ?></td>
          <td><?php echo number_format($value["Counted"]); ?></td>
          <td><?php echo number_format($value["QTY"] - $value["Counted"]); ?></td>
          <td><?php echo substr($value["Batch"],0,4); ?></td>
          <td style="text-align:right"><?php if($value["ProductGroup"] == 'RDT'){echo 'PCR';}else{echo $value["ProductGroup"];} ?></td>
        </tr>
      <?php
        $i++;

        $total += (int) $value["QTY"];
        $counted += (int) $value["Counted"];
        $diff += (int) $value["QTY"] - (int) $value["Counted"];
      } ?>
      <tr style="background: #f5f5f5;">
        <td colspan="5" align="center">
          Total
        </td>
        <td>
          <?php echo number_format($total); ?>
        </td>
        <td>
          <?php echo number_format($counted); ?>
        </td>
        <td>
          <?php echo number_format($diff); ?>
        </td>
      </tr>
    </tbody>
  </table>
</body>

</html>
<?
$html = ob_get_contents();
ob_end_clean();
