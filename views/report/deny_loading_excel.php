<?php
 header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=deny_loading_".Date("Ymd_His").".xls");
?>
<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>Deny Loading Setup Report</title>
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

<h1> Deny Loading Setup </h1>

<table border="1">
		<tr>
			<td  style="font-size: 18px">
				<b>	Item ID </b>
			</td>
			<td  style="font-size: 18px">
				<b> Item Name </b> 
			</td>

			<td  style="font-size: 18px">
				<b> Batch </b> 
			</td>
			<td  style="font-size: 18px">
				<b>	Brand </b>
			</td>
		</tr>
		</thead>
		<?php foreach ($data as $value) {
		?>
		<tr>
			<td>
				<?php echo $value->ItemId; ?>
			</td>
			<td>
				<?php echo $value->NameTH; ?>
			</td>
			<td>
				<?php echo $value->Batch; ?>
			</td>
			<td>
				<?php echo $value->BrandDescription; ?>
			</td>
		</tr>
		<?php } ?>
	</table>
</body>
</html>
<?
$html = ob_get_contents();
ob_end_clean();
