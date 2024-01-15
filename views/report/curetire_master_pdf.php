<?php ob_start(); ?>
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
			<tr style="padding: 20px; border: 0 0 0 0;">
				<td style="border: 0;" colspan="1">
					<a class="navbar-brand" style="float: left;">
						<img  src="./assets/images/STR.jpg" style="padding-left:10px;height:50px; width:auto;" />
					</a> 
				</td>
				<td style="border: 0; padding: 20px 20px 20px 300px;" colspan="5">
					<span style="font-size: 2em; font-weight: bold;">Curetire Code Master Report</span>
				</td>
			</tr>
			<tr>
				<th style="padding: 10px;" colspan="6">
					Date : <?php echo Date("d-m-Y"); ?> Time : <?php echo Date("H:i:s"); ?>
				</th>
			</tr>
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
					<td style="padding: 5px;" width="11%"><?php echo $v->ITEMID; ?></td>
					<td style="padding: 5px;" width="49%"><?php echo $v->ITEMNAME; ?></td>
					<td style="padding: 5px; text-align: center;" width="10%"><?php echo $v->PATTERN; ?></td>
					<td style="padding: 5px; text-align: center;" width="10%"><?php echo $v->BRAND; ?></td>
					<td style="padding: 5px; text-align: center;" width="10%"><?php echo $v->GTCODE; ?></td>
					<td style="padding: 5px; text-align: center;" width="10%"><?php echo $v->CURECODE; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4', 0, '', 3, 3, 3, 3);  
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output(); 