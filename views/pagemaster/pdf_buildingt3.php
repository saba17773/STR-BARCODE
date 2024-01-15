<?php ob_start();

// function cmp($a, $b)
// {
//   return strcmp($a->name, $b->name);
// }

// $json_sorted = [];

// foreach ($datajson as $v) {
// 	$json_sorted[$v->BuildingNo][] = $v;
// }

 //echo '<pre>' . print_r($_REQUEST) . '</pre>';

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Building New Report</title>
	<!-- <link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" /> -->

	<style type="text/css">
		table {
	    border-collapse: collapse;
	    width: 100%;
	    font-size: 10px;
	}

	td, tr {
	    border: 1px solid #000000
	;
	    text-align: left;
	    padding: 4px;
	}
	.f14{
	        font-size:14px;
	        font-family:"Angsana New";
	    }
	.f10{
	        font-size:10px;
	        font-family:"Angsana New";
	    }
	.pad{
			padding:10px;
	}
	</style>
</head>
<body>

<div class="container">
	<table>
		<thead>
		<tr>
			<td colspan="1" align="center">
                <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
                style="padding-left:10px;height:50px; width:auto;" /></a>
            </td>
			<td align="center" colspan="7" class="f14">
				<b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>BUILDING NEW REPORT</b>
			</td>
		</tr>
		<tr>
			<td align="left" colspan="8" class="f10">
				<br><b>DATE: <?php echo $_REQUEST['date_building']; ?></b>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<b>Week :
        <?php echo $Batch ?></b>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<b>SHIFT : <?php if($_REQUEST['shift']=="day"){ echo "กลางวัน"; }else{ echo "กลางคืน";} ?></b>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<b>BOI : <?php echo $BOIName;  ?></b>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<b>Export Date: <?php echo date("Y-m-d H:i:s"); ?></b>
				<!-- <b>GROUP:</b> <?php echo $group; ?> -->
			</td>
		</tr>
		<tr>
			<td align="center" width="13%">
				<br><b>MC</b>
			</td>
			<td align="center" width="13%">
				<br><b>GT.Code</b>
			</td>
			<?php if ($_REQUEST['shift'] =="day") {?>
			<td align="center" width="12%">
				<br>8.00-11.00
			</td>
			<td align="center" width="12%">
				<br>11.00-14.00
			</td>
			<td align="center" width="12%">
				<br>14.00-17.00
			</td>
			<td align="center" width="12%">
				<br>17.00-20.00
			</td>
			<!-- <td align="center" width="10%">
				<br>Curing Date
			</td> -->
			<!-- <td align="center" width="10%">
				<br>18.00-20.00
			</td> -->
			<?php }else{?>
			<td align="center" width="10%">
				<br>20.00-23.00
			</td>
			<td align="center" width="10%">
				<br>23.00-02.00
			</td>
			<td align="center" width="10%">
				<br>02.00-05.00
			</td>
			<td align="center" width="10%">
				<br>05.00-08.00
			</td>
			<!-- <td align="center" width="10%">
				<br>Curing Date
			</td> -->
			<!-- <td align="center" width="10%">
				<br>06.00-08.00
			</td> -->
			<?php } ?>
			<td align="center" width="10%">
				<br>Total
			</td>
			<td align="center" width="10%">
				<br>Curing Date
			</td>

		</tr>
		</thead>
		<?php

				foreach ($datajson as  $value) {
		?>
		<tr>
			<td align="center">
				<?php echo $value->BuildingNo ?>
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
				<?php
				$rows=array($value->Q1,$value->Q2,$value->Q3,$value->Q4);
				$rowsall = array_sum($rows);
				if ($rowsall==0) {
					echo "<br>";
				}else{
					echo $rowsall;
				}
				?>
			</td>
			<?php if ($value->RECEIVED_ALL ===1) : ?>
					<td align="center">
							<img src="<?php echo APP_ROOT; ?>/assets/images/check.png"width="15" height="10">
					</td>
			<?php else : ?>
					<td style="width: 120px; font-weight: bold;"></td>
			<?php endif; ?>
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
				$sumrows = 0;
				foreach ($datajson as $value) {
				$rows = array($value->Q1,$value->Q2,$value->Q3,$value->Q4);
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
			<td></td>
		</tr>
	</table>
</div>

</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4-L', 0, '', 3, 3, 3, 3);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
?>
