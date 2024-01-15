<?php ob_start();  ?>
<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Loading Report</title>
<style type="text/css">
	table {
	    border-collapse: collapse;
	    width: 100%;
	    font-size: 10px;
	    text-align: center;
	}

	td, tr {
	    border: 1px solid #000000;
	    text-align: left;
	    padding: 6px;
	    text-align: center;
	}

	.td, .tr {
	    border: 0px solid #000000;
	    text-align: left;
	    padding: 2px;
	    font-size:14px;
	    text-align: left;
	}

	.f12{
		font-size:14px;
	    font-family:"Angsana New";
	}

</style>
</head>
<body>
	<table>
		<thead>
		<tr class="tr">	
			<td colspan="3" class="td">
				<h1>Loading Report</h1>
			</td>
		</tr>
		<tr class="tr"><td></td></tr>
		<tr class="tr">
			<td class="td" width="25%">
				Order ID : <?php echo $orderId; ?>
			</td>
			<td class="td" width="30%">
				Picking List ID : <?php echo $pickingListId; ?>
			</td>
			<td class="td">
				Date : <?php echo $createDate; ?>
			</td>
		</tr>
		<tr class="tr">
			<td class="td" colspan="3">
				Customer Name : <?php echo $custName; ?>
			</td>
		</tr>
		
	</table>
	<br>
	<table>
		<tr bgcolor="#9999FF">
			<td  style="font-size: 12px">
				Item number 
			</td>
			<td  style="font-size: 12px">
				Thai item description 
			</td>
			<td  style="font-size: 12px">
				Warehouse
			</td>
			<td  style="font-size: 12px">
				Location
			</td>
			<td  style="font-size: 12px">
				Batch number
			</td>
			<td  style="font-size: 12px" width="5%">
				Bc
			</td>
			<td  style="font-size: 12px" width="5%">
				Ax
			</td>
			<td  style="font-size: 12px" width="5%">
				Diff
			</td>
		</tr>
		</thead>
		<?php foreach ($dataloading as $value) {
		?>
		<tr>
			<td>
				<?php echo $value->ItemId; ?>
			</td>
			<td style="text-align:left;">
				<?php echo $value->NameTH; ?>
			</td>
			<td>
				<?php echo $value->warehouse_desc; ?>
			</td>
			<td>
				<?php echo $value->location_desc; ?>
			</td>
			<td>
				<?php echo $value->BatchNo; ?>
			</td>
			<td>
				<?php echo $value->STR_QTY; ?>
			</td>
			<td>
				<?php echo $value->AX_QTY; ?>
			</td>
			<td>
				<?php echo ($value->STR_QTY-$value->AX_QTY); ?>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td colspan="5">
				<b>Total</b>
			</td>
			<td>
				<?php 
	            	$sum_str = 0;
					foreach ($dataloading as $value) {
						$sum_str += $value->STR_QTY;
					}
					if ($sum_str==0) {
						echo "";
					}else{
						echo $sum_str;
					}
	            ?>
	        </td>
	        <td>
				<?php 
	            	$sum_ax = 0;
					foreach ($dataloading as $value) {
						$sum_ax += $value->AX_QTY;
					}
					if ($sum_ax==0) {
						echo "";
					}else{
						echo $sum_ax;
					}
	            ?>
	        </td>
	        <td>
	        	<?php echo ($sum_str-$sum_ax); ?>
	        </td>
		</tr>
	</table>
</body>
</html>

<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf = new mPDF('th','A4', 0, '', 5, 5, 5, 5);  
$mpdf->WriteHTML($html);
$mpdf->Output();