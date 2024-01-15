<?php
 header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=Movement_issue".Date("Ymd_His").".xls");
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
	}

</style>
</head>
<body>
<table border="1">
		<tr align="center" >
			<td>
				<b class="heading"> Withdraw No. </b>	 
			</td>
			<td>
				<b class="heading"> Barcode </b> 
			</td>
			<td>
				<b class="heading"> Item </b>
			</td>
			<td>
				<b class="heading"> Size </b>
			</td>
			<td>
				<b class="heading"> Movement Date </b>
			</td>
			<td>
				<b class="heading"> Batch </b>
			</td>
			<td>
				<b class="heading"> Qty </b>
			</td>
		</tr>
		</thead>
		<?php foreach ($rowdata as $value) {
		?>
		<tr  align="center">
		<td width="130">
				<?php echo $value->InventJournalID; ?>
			</td>
			<td width="120">
				<?php echo $value->BarcodeID; ?>
			</td>
			<td width="120">
				<?php echo $value->ItemID; ?>
			</td>
			<td width="550" align="left">
				<?php echo $value->NameTH; ?>
			</td>
			<td width="200">
			<?php echo date('d-m-Y H:i:s', strtotime($value->CreateDate)); ?>

			</td>
			<td width="120">
				<?php echo $value->Batch; ?>
			</td>
			<td width="50">
				<?php echo $value->QTY; ?>
			</td>
		</tr>
		<?php } ?>
		<tr align="right">
            <td colspan="6">
                <b class="heading">Total</b>
            </td>
            <td align="center">
                <?php
                $sum = 0;
                foreach ($rowdata as $value) {
                    $sum += $value->QTY;
                }
                echo number_format($sum);
                ?>
            </td>
        </tr>
		
	</table>

<b>Export Time : </b><?php echo date("d-m-Y H:i:s"); ?>

</body>
</html>
<?
$html = ob_get_contents();
ob_end_clean();
