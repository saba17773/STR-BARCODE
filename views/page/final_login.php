<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="">
	<title>STR Barcode Final Login</title>
	<script src="/assets/js/jquery-1.12.0.min.js"></script>
	<script src="/assets/js/jqx_mod.js"></script>
	<script src="/assets/js/gojax.min.js"></script>
	<script src="/assets/js/fastclick.js"></script>
	<script src="/assets/js/app.js"></script>

	<script>var base_url = '<?php echo APP_ROOT;?>';</script>
</head>
<body>

<form id="form_desktop_login" onsubmit="return  form_submit()" >

    <ul style="list-style-type: none;">
        <li><input type="radio" name="shift"  value="1"> กะทำงาน A</li>
        <li><input type="radio" name="shift"  value="2"> กะทำงาน B</li>
        <li><input type="radio" name="shift"  value="3"> กะทำงาน D</li>
    </ul>

    <table cellpadding="3">
        <tr>
            <td>
            Username <br>
            <label><input type="text" class="form-control input-lg" name="username_login" id="username_login" 
        autocomplete="off" placeholder="ชื่อผู้ใช้" required></label></td>
        </tr>
        <tr>
            
            <td>Password<br><label><input type="password" class="form-control input-lg" name="password_login" id="password_login" 
        autocomplete="off" placeholder="รหัสผ่าน" required></label></td>
        </tr>
        <tr>
            <td><button type="submit" style="width: 173px;">เข้าสู่ระบบ</button></td>
        </tr>
    </table>
</form>


</body>
</html>
<script>
	jQuery(document).ready(function($) {
		// reset
		localStorage.removeItem('authorize_unbom');

		// $('#username_login').focus();

		$("input[name=shift]").on('change', function() {
			 $('#username_login').focus();
		});
	});

	function form_submit() {
		var	u = $('#username_login').val();
		var p = $('#password_login').val();
		var s = $("input[name=shift]:checked");

		if (!!u && !!p && !!s.val()) {
			gojax_f('post', base_url+'/api/user/desktop/auth', '#form_desktop_login')
			.done(function(data) {
				if (data.status == 404) {
					alert(data.message);
				} else {
					window.location = base_url + data.redirectTo;
				}

				$('#form_desktop_login').trigger('reset');
				$('#username_login').focus();
			});
		} else {
			alert('กรุณาเลือกข้อมูลให้ครบถ้วน!');
		}

		return false;
	}

	function checkLogin() {
		// gojax('post', base_url+'/api/user/location').done(function(data) {
		// 	if (data.location !== '') {

		// 	}
		// });
		var cookie_login = '<?php if(isset($_SESSION["user_login"])){echo $_SESSION["user_login"];}; ?>';
		if (cookie_login !== '') {

			window.location = base_url;
			
		} else {
			$('form#form_desktop_login').show();
		}
	}
</script>