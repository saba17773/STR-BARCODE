<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0">
	<meta charset="UTF-8">
	<link rel="shortcut icon" type="image/png" href="<?php echo APP_ROOT; ?>/logo.png"/>
	<title><?php echo $this->e($title); ?> - Barcode STR</title>
	<link rel="manifest" href="/manifest.json">
	<!-- CSS -->
	<link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" />
	<!-- JS -->
	<script src="<?php echo APP_ROOT; ?>/assets/js/jquery-1.12.0.min.js"></script>
	<script src="<?php echo APP_ROOT; ?>/assets/js/bootstrap.min.js"></script>

	<!--[if lt IE 9]>
      <script src="<?php echo APP_ROOT; ?>/assets/js/html5shiv.js"></script>
      <script src="<?php echo APP_ROOT; ?>/assets/js/respond.js"></script>
    <![endif]-->
</head>
<body>
	
	<div class="container">
		<?php echo $this->section("content"); ?>
	</div>

</body>
</html>