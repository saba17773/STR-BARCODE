<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=DailyFinalHoldReport".Date("Ymd_His").".xls");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Daily Final Hold</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		body {
			font-size: 10px;
		}
	</style>
</head>
<body>
	<table width="100%" cellspacing="0" border="1" cellpadding="5">
		<thead>
			<tr>
				<th colspan="3" style="text-align: center;">

				</th>
				<th colspan="11" style="text-align: center; padding: 30px;">
					<div style="font-size: 2em;">SIAMTRUCK RADIAL CO. LTD.</div>
					<div style="font-size: 1.6em;">
						<?php 
							if($HoldType  != "Normal"){
								echo $HoldType; 
							}else{
								echo "Daily Final Hold Report";
							}
						?>
					</div>
				</th>
			</tr>
			<tr>
				<th colspan="4" style="border-right: 0;"><span>Date : <?php echo $date; ?></span></th>
				<th colspan="4" style="border-right: 0; border-left: 0;"><span>Shift : <?php echo $shift; ?></span></th>
				<th colspan="3" style="border-right: 0; border-left: 0;"><span>Type : <?php echo $type; ?></span></th>
				<th colspan="3" style="border-left: 0;"><span>BOI : <?php echo $BOIName; ?></span></th>
			</tr>
			<tr style="background: #eeeeee;">
				<th>ลำดับ</th>
				<th><?php echo $type === 'TBR' ? 'Serial' : 'Barcode'; ?></th>
				<th>Cure Code</th>
				<th>GT Code</th>
				<th>Item Name</th>
				<th>Defect Description</th>
				<th>Press</th>
				<th>Curing Date</th>
				<th>Operator</th>
				<th>Weekly</th>
				<th>Date/Time</th>
				<th>Build MC</th>
				<th>Date Build</th>
				<th>Shift</th>
				<th>Build Operator</th>
			</tr>
		</thead>
		<?php $x = 1; ?>
		<?php foreach ($data as $v) { ?>
		<tr>
			<td><?php echo $x; $x++;?></td>
			<??>
			<td style="font-size: 0.8em;"><?php echo $type === 'TBR' ? $v['TemplateSerialNo'] : $v['Barcode']; ?></td>
			<td style="font-size: 0.8em;"><?php echo $v['CuringCode']; ?></td>
			<td style="font-size: 0.8em;"><?php echo $v['GT_Code']; ?></td>
			<td style="font-size: 0.8em;"><?php echo $v['ItemName']; ?></td>
			<td style="font-size: 0.8em;"><?php echo $v['Defect']; ?></td>
			<td style="font-size: 0.8em;"><?php echo $v['PressNo'] . $v['PressSide']; ?></td>
			<td style="font-size: 0.8em;"><?php echo date('Y-m-d H:i', strtotime($v['CuringDate'])); ?></td>
			<td style="font-size: 0.8em;"><?php echo $v['CreateBy']; ?></td>
			<td style="font-size: 0.8em;"><?php echo $v['Batch']; ?> </td>
			<td style="font-size: 0.8em;"><?php echo date('Y-m-d H:i', strtotime($v['CreateDate'])); ?></td>
			<td style="font-size: 0.8em;"><?php echo $v['BuildNo']; ?></td>
			<td style="font-size: 0.8em;"><?php echo date('Y-m-d H:i', strtotime($v['BuildDate'])); ?></td>
			<td style="font-size: 0.8em;"><?php echo $v['Shift']; ?></td>
			<td style="font-size: 0.8em;"><?php echo $v['Operator']; ?></td>
		</tr>
		<?php } ?>
	</table>
</body>
</html>
