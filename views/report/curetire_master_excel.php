<?php
header("Content-type: application/vnd.ms-excel; charset=utf-8");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=cure_code_master_".Date("Ymd_His").".xls");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Cure Tire Code Master Report PDF</title>
	<style>
		body {
			font-size: 0.8em;
		}
	</style>
</head>
<body>
	<table border="1" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th style="padding: 5px; background: #eeeeee;">Item number</th>
				<th style="padding: 5px; background: #eeeeee;">Thai item description</th>
				<th style="padding: 5px; background: #eeeeee;">Pattern</th>
				<th style="padding: 5px; background: #eeeeee;">Brand</th>
				<th style="padding: 5px; background: #eeeeee;">Code GT</th>
				<th style="padding: 5px; background: #eeeeee;">Code Cure</th>
			</tr>
		</thead>
		<tbody>
		<?php $data = json_decode($data); $id = 1;?>
			<?php foreach ($data as $v) { ?>
				<tr>
					<td style="padding: 5px;"><?php echo $v->ITEMID; ?></td>
					<td style="padding: 5px;"><?php echo $v->ITEMNAME; ?></td>
					<td style="padding: 5px;"><?php echo $v->PATTERN; ?></td>
					<td style="padding: 5px;"><?php echo $v->BRAND; ?></td>
					<td style="padding: 5px;"><?php echo $v->GTCODE; ?></td>
					<td style="padding: 5px;"><?php echo $v->CURECODE; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</body>
</html>