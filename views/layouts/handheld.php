<?php

header('Content-Type: text/html; charset=utf-8');

$detect = new \Mobile_Detect;

if (!$detect->isMobile()) {
	exit("กรุณาเข้าผ่านอุปกรณ์ Handheld เท่านั้น");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta charset="UTF-8">
	<title>Barcode STR</title>

	<!-- CSS -->
	<link rel="stylesheet" href="/assets/css/normalize.css">

	<style>
		.hide {
			display: none;
		}

		#show-error {
			display: none;
			position: absolute;
			top: 0;
			left: 0;
			background: red;
			text-align: center;
			color: #ffffff;
			width: 90%;
			height: 90%;
			padding: 20px;
		}
	</style>

	<!-- JS -->
	<script>
		var base_url = '';
	</script>
	<script src="/assets/js/jquery-1.12.0.min.js"></script>
	<script src="/assets/js/fastclick.js"></script>
	<script src="/assets/js/gojax.min.js"></script>
	<script src="/assets/js/app.js"></script>

	<!--[if lt IE 9]>
      <script src="/assets/js/html5shiv.js"></script>
      <script src="/assets/js/respond.js"></script>
    <![endif]-->
</head>

<body>

	<div id="show-error">
		<table border="0" width="100%">
			<tr>
				<td valign="top" align="center">
					<img data-dismiss="modal" width="70" height="70" src="/assets/images/error01.png" alt="">
				</td>
			</tr>
			<tr>
				<td valign="top" align="center">
					<b id="show-error-text" style="color: white;"></b>
				</td>
			</tr>
		</table>
	</div>
	<?php echo $this->section("content"); ?>
	<script>
		function close_window() {
			window.open('', '_self', '');
			window.close();
		}
	</script>
</body>

</html>