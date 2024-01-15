<?php
ob_start();

use App\Components\Utils as U;
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Building Report</title>
	<!-- <link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" /> -->

	<style type="text/css">
		table {
			border-collapse: collapse;
			width: 100%;
			font-size: 10px;
		}

		td,
		tr {
			border: 1px solid;
			padding: 4px 0px;
		}

		.f14 {
			font-size: 14px;
			font-family: "Angsana New";
		}

		.f10 {
			font-size: 10px;
			font-family: "Angsana New";
		}

		.pad {
			padding: 10px;
		}
	</style>
</head>

<body>

	<div class="container">
		<table border="1">
			<thead>
				<tr>
					<th colspan="2" align="center">
						<a class="navbar-brand"><img src="./assets/images/STR.jpg" style="padding-left:10px;height:50px; width:auto;" /></a>
					</th>
					<th align="center" colspan="8" class="f14">
						<b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>BUILDING AX REPORT</b>
					</th>
				</tr>
				<tr>
					<th style="padding: 10px; border: none;" colspan="3">
						Date : <?php echo $date_building; ?>
					</th>
					<th style="padding: 10px; border: none;" colspan="2">
						Week : <?php echo U::getWeek($date_building); ?>
					</th>
					<th style="padding: 10px; border: none;" colspan="2">
						Shift : <?php if ($shift == "day") {
									echo "กลางวัน";
								} else {
									echo "กลางคืน";
								} ?>

					</th>
					<th style="padding: 10px; border: none;" colspan="2">
						Export Date: <?php echo date("Y-m-d H:i:s"); ?>

					</th>

					<th style="padding: 10px; border: none;" colspan="1">
						BOI: <?php echo $BOIName; ?>

					</th>

				</tr>
				<tr style="background: #eeeeee;">
					<td align="center">
						<br><b>MC</b>
					</td>
					<td align="center">
						<br><b>GT.Code</b>
					</td>
					<?php if ($shift == "day") { ?>
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
					<?php } else { ?>
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
					<td align="center" width="10%">
						<br>Diff
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
							$rows = array($value->Q1, $value->Q2, $value->Q3, $value->Q4, $value->Q5, $value->Q6);
							$rowsall = array_sum($rows);
							if ($rowsall == 0) {
								echo "<br>";
							} else {
								echo $rowsall;
							}
							?>
						</td>
						<td align="center">
							<?php

							if ($value->Total == 0 || $value->Total == NULL) {
								$sumdiffall = 0;
							} else {
								$sumdiffall =    $rowsall - $value->Total;
							}

							if ($sumdiffall == 0) {
								echo "";
							} else {
								echo $sumdiffall;
							}
							$sumrowsdiff += $sumdiffall;

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
						if ($sum == 0) {
							echo "";
						} else {
							echo number_format((int) $sum);
						}
						?>
					</td>
					<td align="center" valign="middle">

						<?php
						$sum = 0;
						foreach ($datajson as $value) {
							$sum += $value->Q2;
						}
						if ($sum == 0) {
							echo "";
						} else {
							echo number_format((int) $sum);
						}
						?>
					</td>
					<td align="center" valign="middle">

						<?php
						$sum = 0;
						foreach ($datajson as $value) {
							$sum += $value->Q3;
						}
						if ($sum == 0) {
							echo "";
						} else {
							echo number_format((int) $sum);
						}
						?>
					</td>
					<td align="center" valign="middle">

						<?php
						$sum = 0;
						foreach ($datajson as $value) {
							$sum += $value->Q4;
						}
						if ($sum == 0) {
							echo "";
						} else {
							echo number_format((int) $sum);
						}
						?>
					</td>
					<td align="center" valign="middle">

						<?php
						$sum = 0;
						foreach ($datajson as $value) {
							$sum += $value->Q5;
						}
						if ($sum == 0) {
							echo "";
						} else {
							echo number_format((int) $sum);
						}
						?>
					</td>
					<td align="center" valign="middle">

						<?php
						$sum = 0;
						foreach ($datajson as $value) {
							$sum += $value->Q6;
						}
						if ($sum == 0) {
							echo "";
						} else {
							echo number_format((int) $sum);
						}
						?>
					</td>
					<td align="center" valign="middle">
						<?php
						$sum = 0;
						$sumrows = 0;
						foreach ($datajson as $value) {
							$rows = array($value->Q1, $value->Q2, $value->Q3, $value->Q4, $value->Q5, $value->Q6);
							$QQ = array_sum($rows);
							$sumrows += $QQ;
						}
						if ($sumrows == 0) {
							echo "";
						} else {
							echo number_format((int) $sumrows);
						}

						?>
					</td>
					<td align="center" valign="middle">

						<?php
						// $sumdifd = 0;
						// $sumrowsdiff = 0;
						// foreach ($datajson as $value) {
						// 	$rows2 = array($value->Total);
						// 	$QQ2 = array_sum($rows2);
						// 	$sumrowsdiff += $QQ2;
						// 	//$sumdiff +=  $sumrowsdiff;
						// }
						if ($sumrowsdiff == 0 || $sumrowsdiff == NULL) {
							$totaldiff = 0;
						} else {
							$totaldiff =    $sumrows - $sumrowsdiff;
						}

						echo number_format((int) $totaldiff);

						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

</body>

</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th', 'A4-L', 0, '', 3, 3, 3, 3);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
?>