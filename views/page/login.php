<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="manifest" href="/manifest.json">
	<title><?php echo APP_NAME; ?></title>
	<link rel="stylesheet" href="/assets/css/theme.min.css" />
	<script src="/assets/js/jquery-1.12.0.min.js"></script>
	<script src="/assets/js/jqx_mod.js"></script>
	<script src="/assets/js/gojax.min.js"></script>
	<script src="/assets/js/fastclick.js"></script>
	<script src="/assets/js/app.js"></script>

	<script>
		var base_url = '';
	</script>
</head>

<body>

	<nav class="navbar navbar-default navbar-static-top">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header" style="font-size: 12px;">
				<?php
				$detect = new \Mobile_Detect;
				if ($detect->isMobile()) {
					echo '<a class="navbar-brand" href="#">' . APP_NAME . '</a>';
				} else {
					echo '<a class="navbar-brand" href="/">' . APP_NAME . '</a>';
				}
				?>

			</div>
		</div><!-- /.container-fluid -->
	</nav>



	<div class="head-space"></div>

	<div class="panel panel-default" style="margin: 20px auto; max-width: 500px;">
		<div class="panel-heading">เข้าสู่ระบบ</div>
		<div class="panel-body">
			<form id="form_desktop_login" onsubmit="return  form_submit()">
				<div class="alert alert-danger" role="alert">
					<div style="text-align: center;">
						<label class="radio-inline">
							<input type="radio" name="shift" value="1" style="width: 1.5em; height: 1.5em;">
							<span style="padding-left: 10px; font-size: 1.4em;">กะทำงาน <b>A</b></span>
						</label>
						<label class="radio-inline">
							<input type="radio" name="shift" value="2" style="width: 1.5em; height: 1.5em;">
							<span style="padding-left: 10px; font-size: 1.4em;">กะทำงาน <b>B</b></span>
						</label>
						<label class="radio-inline">
							<input type="radio" name="shift" value="3" style="width: 1.5em; height: 1.5em;">
							<span style="padding-left: 10px; font-size: 1.4em;">กะทำงาน <b>D</b></span>
						</label>
					</div>
				</div>
				<!-- <div class="panel panel-default">
				<div class="panel-body" style="text-align: center;">
					<label class="radio-inline">
					<input type="radio" name="shift"  value="1" style="width: 1.5em; height: 1.5em;">
					<span style="padding-left: 10px; font-size: 1.4em;">กะทำงาน <b>A</b></span>
					</label>
					<label class="radio-inline">
					<input type="radio" name="shift"  value="2" style="width: 1.5em; height: 1.5em;">
					<span style="padding-left: 10px; font-size: 1.4em;">กะทำงาน <b>B</b></span>
					</label>
					<label class="radio-inline">
					<input type="radio" name="shift"  value="3" style="width: 1.5em; height: 1.5em;">
					<span style="padding-left: 10px; font-size: 1.4em;">กะทำงาน <b>D</b></span>
					</label>
				</div>
			</div> -->

				<div class="form-group">
					<label for="username_login">Username</label>
					<input type="text" class="form-control" name="username_login" id="username_login" autocomplete="off" placeholder="ชื่อผู้ใช้" required>
				</div>

				<div class="form-group">
					<label for="password_login">Password</label>
					<input type="password" class="form-control" name="password_login" id="password_login" autocomplete="off" placeholder="รหัสผ่าน" required>
				</div>

				<button type="submit" id="btn_login" class="btn btn-block btn-primary">
					<span class="glyphicon glyphicon-log-in"></span>
					เข้าสู่ระบบ
				</button>
				<div style="margin-top: 10px;">
					<a href="http://lanister.deestonegrp.com:3311/handheld_loginMb">Handheld Login</a>
					<!-- <a href="http://localhost:8040/handheld_loginMb">Handheld Login</a> -->
					<!-- <a href="http://localhost:8801/handheld_loginMb">Handheld Login</a> -->
				</div>
			</form>
		</div>
	</div>


</body>

</html>
<script>
	jQuery(document).ready(function($) {
		close_button();
		checkLogin();

		// reset
		localStorage.removeItem('authorize_unbom');

		// $('#username_login').focus();

		$("input[name=shift]").on('change', function() {
			$('#username_login').focus();
		});
	});

	function form_submit() {
		var u = $('#username_login').val();
		var p = $('#password_login').val();
		var s = $("input[name=shift]:checked");

		$('#btn_login').text('กำลังเข้าสู่ระบบ...').prop('disabled', true);

		if (!!u && !!p && !!s.val()) {
			gojax_f('post', base_url + '/api/user/desktop/auth', '#form_desktop_login')
				.done(function(data) {
					if (data.status == 404) {
						alert(data.message);
						// $('#modal_alert').modal({backdrop: 'static'});
						// $('#modal_alert_message').text(data.message);
						$('#btn_login').text('เข้าสู่ระบบ').prop('disabled', false);
						$('#password_login').val('');
						$('#username_login').val('').focus();
					} else {
						// if (data.user_location == 3) {
						// 	close_button();
						// 	gojax('post', base_url+'/clearsession').done(function() {
						// 		window.location = '?error=ไม่สามารถเข้าสู่ระบบผ่านอุปกรณ์นี้ได้';
						// 		open_button();
						// 	})
						// 	.fail(function() {
						// 		window.location = '?error=ไม่สามารถดำเนินการได้ กรุราลองอีกครั้ง';
						// 		open_button();
						// 	});
						// } else {
						window.location = base_url + data.redirectTo;
						// }



					}

					// $('#form_desktop_login').trigger('reset');
					// $('#username_login').focus();
					// $('#btn_login').prop('disbled', true);
				});


		} else {
			alert('กรุณาเลือกข้อมูลให้ครบถ้วน!');
			// $('#modal_alert').modal({backdrop: 'static'});
			// $('#modal_alert_message').text('กรุณาเลือกข้อมูลให้ครบถ้วน!');
			$('#btn_login').text('เข้าสู่ระบบ').prop('disabled', false);
		}

		return false;
	}

	function checkLogin() {
		// gojax('post', base_url+'/api/user/location').done(function(data) {
		// 	if (data.location !== '') {

		// 	}
		// });
		var cookie_login = '<?php if (isset($_SESSION["user_login"])) {
								echo $_SESSION["user_login"];
							}; ?>';
		if (cookie_login !== '') {

			window.location = base_url;
			// if (user_location == 3) { // curing
			// 	window.location = base_url + '/curing';
			// } else {
			// 	window.location = base_url;
			// }


		} else {
			$('form#form_desktop_login').show();
		}

		open_button();
	}
</script>