<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>User Barcode</title>
</head>
<body>
	<?php
		$username  =str_replace("%20"," ",$usernamedata);
		$text =  $username.$empCode;
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
	?>
	<table border="0" width="100%">
		<tr valign="top" align="center">
			<td align="center" valign="top">
				<?php

					// $new_username = str_replace("_", "@", $username);
					//echo '<img alt="'.$new_username.'" width="100" height="30" src="./barcode.php?text="'.$new_username.' />';
					echo '<img width="120" height="40" src="data:image/png;base64,' . base64_encode($generator->getBarcode($username, $generator::TYPE_CODE_128_B)) . '"><br />';

					
				?>

			</td>
		</tr>
		<tr valign="top" align="center">
			<td align="center" valign="top">
				<?php echo urldecode($name); ?>
			</td>
		</tr>
		<tr valign="top" align="center">
			<td align="center" valign="top">
				<br>
				<br>
				<br>
				<br>
				<?php
					echo '<img width="120" height="40" src="data:image/png;base64,' . base64_encode($generator->getBarcode($password, $generator::TYPE_CODE_128_B)) . '"><br />';
				?>
			</td>
		</tr>
		<tr valign="top" align="center">
			<td align="center" valign="top">
				Password
			</td>
		</tr>
	</table>
	<?php

		// echo '<img width="200" height="40" src="data:image/png;base64,' . base64_encode($generator->getBarcode($username, $generator::TYPE_CODE_128)) . '"><br />';
		// echo '<div style="text-align: left;">'.$text.'</div>';
	?>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf=new mPDF('th','A6');
$mpdf->autoScriptToLang = true;
$mpdf->WriteHTML($html);
$mpdf->Output();
