<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=BUILDING_REPORT_".Date("Ymd_His").".xls");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Building master Report </title>
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
			<td colspan="2" align="center">

            </td>
			<td align="center" colspan="7" class="f14">
				<b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>BUILDING REPORT</b>
			</td>
		</tr>
		<tr>
			<td align="left" colspan="9" class="f10">
				<br><b>DATE:</b> <?php echo $_REQUEST['date_building']; ?>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<b>SHIFT : <?php if($_REQUEST['shift']=="day"){ echo "กลางวัน"; }else{ echo "กลางคืน";} ?></b>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<b>BOI : <?php echo $BOIName;  ?></b>
				<!-- <b>GROUP:</b> <?php echo $group; ?> -->
			</td>
		</tr>
		<tr>
			<td align="center">
				<br><b>MC</b>
			</td>
			<td align="center">
				<br><b>GT.Code</b>
			</td>
			<?php if ($_REQUEST['shift']=="day") {?>
			<td align="center" width="10%">
				<br>8.00-10.00
			</td>
			<td align="center" width="10%">
				<br>10.00-12.00
			</td>
			<td align="center" width="10%">
				<br>12.00-14.00
			</td>
			<td align="center" width="10%">
				<br>14.00-16.00
			</td>
			<td align="center" width="10%">
				<br>16.00-18.00
			</td>
			<td align="center" width="10%">
				<br>18.00-20.00
			</td>
			<?php }else{?>
			<td align="center" width="10%">
				<br>20.00-22.00
			</td>
			<td align="center" width="10%">
				<br>22.00-24.00
			</td>
			<td align="center" width="10%">
				<br>24.00-02.00
			</td>
			<td align="center" width="10%">
				<br>02.00-04.00
			</td>
			<td align="center" width="10%">
				<br>04.00-06.00
			</td>
			<td align="center" width="10%">
				<br>06.00-08.00
			</td>
			<?php } ?>
			<td align="center" width="10%">
				<br>Total
			</td>
		</tr>
		</thead>
		<tbody>
      <?php

  				foreach ($datajson as  $value) {
  		?>
  		<tr>
  			<td align="center">
  				<?php echo $value->BuildingNo; ?>
  			</td>
  			<td align="center">
  				<?php echo $value->GT_Code;  ?>
  			</td>
  			<td align="center">
  				<?php echo $value->Q1; ?>
  			</td>
  			<td align="center">
  				<?php echo $value->Q2; ?>
  			</td>
  			<td align="center">
  				<?php echo $value->Q3; ?>
  			</td>
  			<td align="center">
  				<?php echo $value->Q4; ?>
  			</td>
  			<td align="center">
  				<?php echo $value->Q5; ?>
  			</td>
  			<td align="center">
  				<?php echo $value->Q6; ?>
  			</td>
  			<td align="center">
  				<?php
  				$rows=array($value->Q1,$value->Q2,$value->Q3,$value->Q4,$value->Q5,$value->Q6);
  				$rowsall = array_sum($rows);
  				if ($rowsall==0) {
  					echo "<br>";
  				}else{
  					echo $rowsall;
  				}
  				?>
  			</td>
  		</tr>
  		<?php
  		}
  		?>
      <tr>
  			<td align="center" valign="middle" class="pad">
  				Total
  			</td>
  			<td align="center" valign="middle">

  			</td>
  			<td align="center" valign="middle">

  				<?php
  					$sum = 0;
      				foreach ($datajson as $value) {
      					$sum += $value->Q1;
      				}
      				if ($sum==0) {
      					echo "";
      				}else{
      					echo $sum;
      				}
  				?>
  			</td>
  			<td align="center" valign="middle">

  				<?php
  					$sum = 0;
      				foreach ($datajson as $value) {
      					$sum += $value->Q2;
      				}
      				if ($sum==0) {
      					echo "";
      				}else{
      					echo $sum;
      				}
  				?>
  			</td>
  			<td align="center" valign="middle">

  				<?php
  					$sum = 0;
      				foreach ($datajson as $value) {
      					$sum += $value->Q3;
      				}
      				if ($sum==0) {
      					echo "";
      				}else{
      					echo $sum;
      				}
  				?>
  			</td>
  			<td align="center" valign="middle">

  				<?php
  					$sum = 0;
      				foreach ($datajson as $value) {
      					$sum += $value->Q4;
      				}
      				if ($sum==0) {
      					echo "";
      				}else{
      					echo $sum;
      				}
  				?>
  			</td>
  			<td align="center" valign="middle">

  				<?php
  					$sum = 0;
      				foreach ($datajson as $value) {
      					$sum += $value->Q5;
      				}
      				if ($sum==0) {
      					echo "";
      				}else{
      					echo $sum;
      				}
  				?>
  			</td>
  			<td align="center" valign="middle">

  				<?php
  					$sum = 0;
      				foreach ($datajson as $value) {
      					$sum += $value->Q6;
      				}
      				if ($sum==0) {
      					echo "";
      				}else{
      					echo $sum;
      				}
  				?>
  			</td>
  			<td align="center" valign="middle">
  			<?php
  				$sum = 0;
  				$sumrows = 0;
  				foreach ($datajson as $value) {
  				$rows = array($value->Q1,$value->Q2,$value->Q3,$value->Q4,$value->Q5,$value->Q6);
  				$QQ = array_sum($rows);
  				$sumrows += $QQ;
  				}
  					if ($sumrows==0) {
      					echo "";
      				}else{
      					echo $sumrows;
      				}

  			?>
  			</td>
  		</tr>
		</tbody>
	</table>
</body>
</html>
