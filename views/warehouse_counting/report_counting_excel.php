<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=stock_counting_" . Date("Ymd_His") . ".xls");
?>
<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">

  <title>Report Counting</title>
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
        <td align="center" colspan="7" class="f14">
          <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>Stock Counting Report</b>
        </td>
      </tr>
      <tr>
        <td colspan="7">
          From Date: <?php
                      echo $counting_from_date;
                      ?>
          To Date: <?php
                    echo $counting_to_date;
                    ?>
        </td>
      </tr>

      <tr style="background: #f5f5f5;">
        <th>No.</th>
        <th>Date</th>
        <th>Item</th>
        <th>Size</th>
        <th>Brand</th>
        <th>Batch</th>        
        <th>QTY</th>
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
          <td><?php echo $value["CountingDate"]; ?></td>
          <td><?php echo $value["ItemID"]; ?></td>
          <td><?php echo $value["NameTH"]; ?></td>
          <td><?php echo $value["Brand"]; ?></td>
          <td><?php echo $value["Batch"]; ?></td>          
          <td><?php echo number_format($value["QTY"]); ?></td>
        </tr>
      <?php
        $i++;

        $total += (int) $value["QTY"];
      } ?>
      <tr style="background: #f5f5f5;">
        <td colspan="6" align="center">
          Total
        </td>
        <td>
          <?php echo number_format($total); ?>
        </td>
      </tr>
    </tbody>
  </table>
</body>

</html>
<?
$html = ob_get_contents();
ob_end_clean();
