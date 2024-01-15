<?php
 header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=LoadingDesktop".Date("Ymd_His").".xls");
// echo "<pree>";
// print_r($data);
// print_r($_SESSION);
// echo "</pree>";
?>
<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>Loading Report</title>
    <style type="text/css">
	table {
	    border-collapse: collapse;
	    width: 90%;
	    font-size: 16px;
	    text-align: center;
	}

	td, tr {
	    text-align: left;
	    padding: 6px;
	    text-align: center;
	}

	.td, .tr {
	    border: 1px solid #000000;
	    text-align: left;
	    padding: 2px;
	    font-size:18px;
	    text-align: left;
	}

	.f12{
		font-size:20px;
	    font-family:"Angsana New";
	}

</style>
</head>
<body>

<table border="1">
		<tr>
			<td  style="font-size: 18px">
				<b>	Sales Order </b>
			</td>
			<td  style="font-size: 18px">
				<b> Barcode </b> 
			</td>

			<td  style="font-size: 18px">
				<b> Serial No </b> 
			</td>
			<td  style="font-size: 18px">
				<b>	Item </b>
			</td>
			<td  style="font-size: 18px">
				<b>	Size </b>
			</td>
			<td  style="font-size: 18px">
				<b>	Batch No. </b>
			</td>
			<td  style="font-size: 18px">
				<b>	Qty </b>
			</td>
			<td  style="font-size: 18px">
				<b>	Create Date </b>
			</td>
		</tr>
		</thead>
		<?php foreach ($dataloading as $value) {
		?>
		<tr>
			<td>
				<?php echo $value->OrderId; ?>
			</td>
			<td>
				<?php echo $value->Barcode; ?>
			</td>
			<td>
				<?php echo $value->SerialName; ?>
			</td>
			<td>
				<?php echo $value->ItemId; ?>
			</td>
			<td>
				<?php echo $value->NameTH; ?>
			</td>
			<td>
				<?php echo $value->BatchNo; ?>
			</td>
			<td>
				<?php echo $value->Qty; ?>
			</td>
			<td>
				<?php 
				$datecreate = date_create($value->CreatedDate);
				echo date_format($datecreate,"Y/m/d H:i:s");
				 ?>
			</td>
		</tr>
		<?php } ?>
	</table>
</body>
</html>
<?
$html = ob_get_contents();
ob_end_clean();
