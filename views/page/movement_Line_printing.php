<?php ob_start();
$datajson = json_decode($datajson);

$a = count($datajson) - (floor(count($datajson) / 26) * 26);

$stylesheet = " table{
	                  width: 100%;
	                }
	                tr{
	                  border: 0px;
	                }
	                td{
	               	  border: 0px;
	               	  text-align: center;
	               	  padding: 15px;
	                }";
$footer = "
	        <table class='table' width='100%'>
						<tr class='tr'>
						    <td class='td' align='left'>
						        Ref.WI-MP-1.15
						    </td>
						    <td class='td' align='right'>
						        " . $issue . "
						    </td>
						</tr>
					</table>";
$header__ = '<table border="1"><thead>
			<tr style="border: 1;">
				<th colspan="3" align="center">
	                <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
	                style="padding-left:10px;height:50px; width:auto;" /></a>
	            </th>
				<th align="center" colspan="6" class="f12" style="border-right: 0;">
					<span style="font-size: 1.5em;"><b>' . $title . '</b></span>
					 <br> <b>รายงานการเบิกยาง</b>
				</th>
				<th colspan="2" style="border-left: 0;">
					<span style="font-size: 1.1em;"><b>Withdrawal No.</b></span> ' . $journalId . '
				</th>
			</tr>
			<tr style="border: 1;">
				<th align="left" colspan="11">
					<b>
						DATE: ' . date("d-m-Y", strtotime($create_date)) . '
					</b>
				</th>
			</tr>
		</thead>
	</table>';

$pdf = new mPDF('th', 'A4-L', 0, '', 3, 3, 3, 20);
$pdf->SetDisplayMode('fullpage');
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Report Withdrawl</title>
	<style type="text/css">
		table {
			border-collapse: collapse;
			width: 100%;
			font-size: 10px;
		}

		td,
		tr {
			border: 1px solid #000000;
			text-align: left;
			padding: 5px;
		}

		.f12 {
			font-size: 14px;
			font-family: "Angsana New";
		}

		th {
			padding: 5px;
		}
	</style>
</head>

<body>
	<div class="container">
		<table>
			<thead>
				<tr>
					<th colspan="2" align="left">
						<a class="navbar-brand"><img src="./assets/images/STR.jpg" style="padding-left:10px;height:50px; width:auto;" /></a>
					</th>
					<th align="center" colspan="3" class="f12" style="border-right: 0;">
						<span style="font-size: 1.5em;"><b><?php echo $title; ?></b></span>
						<!-- <br> <b>รายงานการเบิกยาง</b> -->
					</th>
					<th colspan="3" style="border-left: 0;">
						<?php $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
						echo '<img width="20%" height="3%" src="data:image/png;base64,' . base64_encode($generator->getBarcode($journalId, $generator::TYPE_CODE_128)) . '"><br />';

						?>
						<span style="font-size: 1.1em;"><b>Withdrawal No.</b></span> <?php echo $journalId; ?>
					</th>
				</tr>
				<tr>
					<th align="left" colspan="11">
						<b>
							DATE:
							<?php
							echo date('d-m-Y', strtotime($create_date));
							?>
						</b>
					</th>
				</tr>
				<tr>
					<th align="center" width="3%" style="border: 1px solid #000000;">
						<b>No.</b>
					</th>
					<th align="center" width="7%" style="border: 1px solid #000000;">
						<b>Item</b>
					</th>
					<th align="center" width="35%" style="border: 1px solid #000000;">
						<b>Size</b>
					</th>
					<th align="center" width="5%" style="border: 1px solid #000000;">
						<b>Batch</b>
					</th>
					<th align="center" width="20%" style="border: 1px solid #000000;">
						<b>Causes</b>
					</th>
					<th align="center" width="5%" style="border: 1px solid #000000;">
						<b>Quantity</b>
					</th>
					<th align="center" width="10%" style="border: 1px solid #000000;">
						<b>ผู้เบิก</b>
					</th>
					<th align="center" width="15%" style="border: 1px solid #000000;">
						<b>แผนก</b>
					</th>

				</tr>
			</thead>
			<?php
			// echo "<pre>" . print_r($datajson, true) . "</pre>"; exit;
			$i = 1;
			foreach ($datajson as  $value) {

			?>
				<tr>
					<td align="center">
						<?php echo $i; ?>
					</td>
					<td align="center">
						<?php
						echo $value->ItemID;
						?>
					</td>
					<td align="center">
						<?php
						//echo $value->time_create;
						echo $value->NameTH;
						?>
					</td>
					<td align="center">
						<?php if ($value->Batch !== '' && $value->Batch !== null) {
							echo $value->Batch;
						} ?>
					</td>
					<td align="left">
						<?php echo $value->RN; ?>
					</td>
					<td align="center">
						<?php if ($value->QTY != '') {
							echo $value->QTY;
						} else {
							echo "<br>";
						}
						?>
					</td>
					<td align="center">
						<?php echo $value->name; ?>
					</td>
					<td align="center">
						<?php echo $value->Description; ?>
					</td>

				</tr>
			<?php
				$pdf->SetHTMLFooter($footer);
				$i++;
			}
			?>
			<tr>
				<td colspan="4">

				</td>
				<td colspan="4">
					<b>Total : </b>
					<?php
					$sum = 0;
					foreach ($datajson as $value) {
						$sum += $value->QTY;
					}
					echo $sum . "  <b>เส้น</b>";
					?>
				</td>
			</tr>
		</table>

	</div>
</body>

</html>
<?php
$html = ob_get_contents();
ob_end_clean();

$pdf->WriteHTML($html);
$pdf->WriteHTML($stylesheet, 1);
// $a = "{PAGENO}";
//

if ($a > 12) {
	$pdf->AddPage();
	$pdf->WriteHTML($header__);
}


$pdf->WriteHTML('
	<br>
		<table border="0" cellpadding="10" autosize="2.4">
       	<tr style="border: 0;">
          <td align="left">
			Request by : _________________________________________
          </td>
       	</tr>
       	<tr style="border: 0;">
          <td align="left">
			Approve by : _________________________________________
          </td>
       	</tr>
       	<tr style="border: 0;">
					<td width="34%" style="border: 0;">
						<br>_________________________________________<br><br>QA Manager
					</td>
					<td width="33%" style="border: 0;">
						_________________________________________<br><br>Warehouse Division Head
					</td>
					<td width="33%" style="border: 0;">
						_________________________________________<br><br>Plant Q-Tech Manager
					</td>
       	</tr>
       	<tr style="border: 0;">
					<td style="border: 0;">
						_________________________________________<br><br>Warehouse Manager
					</td>
					<td style="border: 0;">
						_________________________________________<br><br>Deputy Plant Manager / Plant Manager
					</td>
					<td style="border: 0;">
						_________________________________________<br><br>DCMMO / CMMO
					</td>
       	</tr>
    </table>');

$pdf->Output();
