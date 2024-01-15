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

  <table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
      <tr>
        <td colspan="3">
          <a class="navbar-brand"><img src="assets/images/STR.jpg" style="padding-left:40px;height:40px; width:auto;" /></a>
        </td>
        <td align="center" colspan="4" class="f14">
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
        <th style="width: 40px;">No.</th>
        <th>Date</th>
        <th style="width: 120px;">Item</th>
        <th style="width: 400px;">Size</th>
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
          <td align="center"><?php echo $i; ?></td>
          <td align="center"><?php echo $value["CountingDate"]; ?></td>
          <td align="center"><?php echo $value["ItemID"]; ?></td>
          <td><?php echo $value["NameTH"]; ?></td>
          <td align="center"><?php echo $value["Brand"]; ?></td>
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
<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th', 'A4', 0, '', 3, 3, 3, 3);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
