<?php ob_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Internal Withdrawal Report</title>
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
    padding: 5px;
}
.f12{
	font-size:14px;
    font-family:"Angsana New";
}

</style>
</head>
<body>

<div class="container">
	<table>
		<thead>
		<tr>
			<td colspan="4" align="center">
                <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
                style="padding-left:10px;height:50px; width:auto;" /></a>
            </td>
			<td align="center" colspan="9" class="f12">
				<h2><b>Internal Withdrawal Report</b></h2> <br> <b>รายงานการเบิกยาง</b>
			</td>
		</tr>
		<tr>
			<td align="left" colspan="3" style="border-right: 0; border-left: 0;">
				<b>DATE: <?php echo $dateinter; ?></b>
			</td>
			<td align="left" colspan="10" style="border-right: 0; border-left: 0;">
				<b>BOI: <?php echo $BOIName; ?></b>
			</td>
		</tr>
		<tr>
			<td align="center" width="3%">
				<b>No.</b>
			</td>
			<td align="center" width="6%">
				<b>CuringCode</b>
			</td>
			<td align="center" width="5%">
				<b>Time</b>
			</td>
			<td align="center" width="7%">
				<b>Serial</b>
			</td>
			<td align="center">
				<b>Size</b>
			</td>
			<td align="center" width="6%">
				<b>Batch</b>
			</td>
			<td align="center" width="13%">
				<b>Causes</b>
			</td>
			<td align="center" width="3%">
				<b>Qty</b>
			</td>
			<td align="center" width="10%">
				<b>ผู้เบิก</b>
			</td>
			<td align="center" width="5%">
				<b>แผนก</b>
			</td>
			<td align="center" width="10%">
				<b>ผู้จ่าย</b>
			</td>
			<td align="center" width="7%">
				<b>Withdrawal No.</b>
			</td>
			<td align="center">
				<b>Status</b>
			</td>
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
				<?php if (isset($value->CuringCode)) {
					echo $value->CuringCode;
				}?>
			</td>
			<td align="center">
				<?php
					//echo $value->time_create;
					echo substr($value->time_create, 0,5);
				?>
			</td>
			<td align="center">
				<?php echo $value->TemplateSerialNo; ?>
			</td>
			<td align="left">
				<?php echo $value->NameTH; ?>
			</td>
			<td align="center">
				<?php if ($value->Batch !== '' && $value->Batch !== null) {
					echo $value->Batch;
				} ?>
			</td>
			<td align="center">
				<?php echo $value->Note; ?>
			</td>
			<td align="center">
				<?php if ($value->qty!='') {
						echo $value->qty;
						}else{
						echo "<br>";
						}
				?>
			</td>
			<td align="center">
				<?php if (isset($value->FirstName)&&isset($value->LastName)) {
					echo $value->FirstName." ".$value->LastName;
				}?>
			</td>
			<td align="center">
				<?php echo $value->Department; ?>
			</td>
			<td align="center">
				<?php echo $value->Name; ?>
			</td>
			<td align="center">
				<?php if (isset($value->InventJournalID)) {
					echo $value->InventJournalID;
				}?>
			</td>
			<td align="center">
				<?php if (isset($value->Description)) {
					echo $value->Description;
				}?>
			</td>
		</tr>
		<?php
			$i++;
			}
		?>
		<tr>
			<td colspan="6">

			</td>
			<td colspan="7">
				<b>Total : </b>
				<?php
					$sum = 0;
    				foreach ($datajson as $value) {
    					$sum += $value->qty;
    				}
    				echo $sum."  <b>เส้น</b>";
				?>
			</td>
		</tr>
	</table>
</div>
</body>
</html>
<?php
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
	$footer = "<table>
	           	<tr>
		            <td align='left'>
						Request by : _________________________________________
		            </td>
	           	</tr>
	           	<tr>
		            <td align='left'>
						Approve by : _________________________________________
		            </td>
	           	</tr>
	           	<tr>
					<td width='34%'>
						<br>_________________________________________<br><br>Plant Q-tech Division Head
					</td>
					<td width='33%'>
						_________________________________________<br><br>Warehouse Division Head
					</td>
					<td width='33%'>
						_________________________________________<br><br>Production Division Head
					</td>
	           	</tr>
	           	<tr>
					<td>
						_________________________________________<br><br>Plant Q-tech Manager
					</td>
					<td>
						_________________________________________<br><br>Warehouse Manager
					</td>
					<td>
						_________________________________________<br><br>Production Manager
					</td>
	           	</tr>
	        </table>";

$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4-L', 0, '', 3, 3, 3, 19);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->WriteHTML($stylesheet, 1);
$pdf->SetHTMLFooter($footer);
$pdf->Output();
?>
