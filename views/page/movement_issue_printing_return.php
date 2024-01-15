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
					 <br> <b>'.$toppic.'</b>
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
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>

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
					<th <?php if ($check == 1) { ?>colspan="4" <?php } else { ?>colspan="3" <?php } ?> align="center">
						<a class="navbar-brand"><img src="./assets/images/header_logo_report.png" style="padding-left:50px;height:30px; width:200px;" /></a>
					</th>
					<th align="center" colspan="5" class="f12" style="border-right: 0;">
						<span style="font-size: 1.5em;"><b><?php echo $title; ?></b></span>
						<br> <b><?php echo $toppic; ?></b>
					</th>
                    <th>
                    <a class="navbar-brand"><img src="./assets/images/STR.jpg" style="padding-left:10px;height:50px; width:auto;" /></a>
                    </th>
					<th colspan="2" style="border-left: 0;">
					<div style="padding-right:500px;">	<span style="font-size: 1.1em;"><b>Withdrawal No.<BR></b></span> <?php echo $journalId; ?></div>
					</th>
				</tr>
				<tr>
					<th align="left" <?php if ($check == 1) { ?>colspan="12" <?php } else { ?>colspan="11" <?php } ?>>
						<b>
							DATE:
							<?php
							echo date('d-m-Y', strtotime($create_date));
							?>
						</b>
					</th>
				</tr>
				<tr>
					<th align="center" width="2%" style="border: 1px solid #000000;">
						<b>No.</b>
					</th>
					<th align="center" width="6%" style="border: 1px solid #000000;">
						<b>Curing Code</b>
					</th>
					<th align="center" width="6%" style="border: 1px solid #000000;">
						<b>Item</b>
					</th>
					<th align="center" width="7%" style="border: 1px solid #000000;">
						<b>Serial</b>
					</th>
					<th align="center" width="28%" style="border: 1px solid #000000;">
						<b>Size</b>
					</th>
					<th align="center" width="6%" style="border: 1px solid #000000;">
						<b>Batch</b>
					</th>
					<th align="center" width="9%" style="border: 1px solid #000000;">
						<b>Causes</b>
					</th>
					<th align="center" width="5%" style="border: 1px solid #000000;">
						<b>Quantity</b>
					</th>
					<?php if ($check == 1) { ?>
						<th align="center" width="5%" style="border: 1px solid #000000;">
							<b>Quantity</b>
						</th>
					<?php } ?>
					<th align="center" width="10%" style="border: 1px solid #000000;">
						<b>ผู้เบิก</b>
					</th>
					<th align="center" width="7%" style="border: 1px solid #000000;">
						<b>แผนก</b>
					</th>
					<th align="center" width="5%" style="border: 1px solid #000000;">
						<b>ผู้จ่าย</b>
					</th>
				</tr>
			</thead>
			<?php
			if ($check == 1) {
				$i = 1;
				foreach ($datajson as  $value) {
					echo "<tr>";
					echo " <td align='center'>" . $i . " </td> ";
					echo "<td align='center'>" . $value->CuringCode . "</td>";
					echo "<td align='center'>" .  $value->ItemID . "</td>";
					echo "<td align='center'>" . $value->TemplateSerialNo . "</td>";
					echo "<td align='left'>" . $value->NameTH . "</td>";
					echo "<td align='center'>" . $value->Batch . "</td>";
					echo "<td align='center'>" . $value->Note . "</td>";
					echo "<td align='center'>" . $value->qty . "</td>";
					echo "<td align='center'>" . $value->BOI . "</td>";
					echo "<td align='center'>" . $value->FirstName . " " . $value->LastName . "</td>";
					echo "<td align='center'>" . $value->Department . "</td>";
					echo "<td align='center'>" . $value->Name . "</td>";

					echo "</tr>";

					$pdf->SetHTMLFooter($footer);
					$i++;
				}
				echo "<tr>";
				echo "<td colspan='6'>";
				echo "</td>";
				echo "<td colspan='6'>";
				echo "<b>Total : " . $totalqty . " เส้น</b>";
				echo "</td>";
				echo "</tr>";
			} else {
				$i = 1;
				foreach ($datajson as  $value) {

					echo "<tr>";
					echo " <td align='center'>" . $i . " </td> ";
					echo "<td align='center'>" . $value->CuringCode . "</td>";
					echo "<td align='center'>" .  $value->ItemID . "</td>";
					echo "<td align='center'>" . $value->TemplateSerialNo . "</td>";
					echo "<td align='left'>" . $value->NameTH . "</td>";
					echo "<td align='center'>" . $value->Batch . "</td>";
					echo "<td align='center'>" . $value->Note . "</td>";
					echo "<td align='center'>" . $value->qty . "</td>";
					echo "<td align='center'>" . $value->FirstName . " " . $value->LastName . "</td>";
					echo "<td align='center'>" . $value->Department . "</td>";
					echo "<td align='center'>" . $value->Name . "</td>";

					echo "</tr>";

					$pdf->SetHTMLFooter($footer);
					$i++;
				}
				echo "<tr>";
				echo "<td colspan='6'>";
				echo "</td>";
				echo "<td colspan='5'>";
				echo "<b>Total : " . $totalqty . " เส้น</b>";
				echo "</td>";
				echo "</tr>";
			}

			// echo "<pre>" . print_r($datajson, true) . "</pre>"; exit;			
			?>
		</table>

	</div>
</body>

</html>
<?php

$html = ob_get_contents();
ob_end_clean();
$pdf->WriteHTML($html);
$pdf->WriteHTML($stylesheet, 1);

if ($a > 12) {
	$pdf->AddPage();
	$pdf->WriteHTML($header__);
}


if ($check == 2) {
	$pdf->WriteHTML('
		<br>
			<table border="0" cellpadding="10" autosize="2.4">

	       	<tr style="border: 0;">
					<td align="left">
					Approve by : _________________________________________
				 			</td>
						<td style="border: 0;">

						</td>
						<td align="left">
					 	Receipt by : _________________________________________
					 			</td>

	       	</tr>
	    </table>');
} else {
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
						
					</td>
					<td width="33%" style="border: 0;">
						_________________________________________<br><br>Production Division Head
					</td>
       	</tr>
       	<tr style="border: 0;">
					<td style="border: 0;">
						_________________________________________<br><br>Plan Q-Tech Manager
					</td>
					<td style="border: 0;">
						_________________________________________<br><br>Plant Manager
					</td>
					<td style="border: 0;">
						_________________________________________<br><br>CMMO
					</td>
       	</tr>
    </table>');
}

$pdf->Output();
