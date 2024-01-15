<?php $this->layout("layouts/base", [ "title" => "Change Password" ]); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
	<div class="panel-heading">Change Password</div>
	<div class="panel-body">
		<form id="form_change_password">
			<div class="form-group">
				<label>รหัสผ่านเดิม</label>
				<input type="password" name="old_password" data-validation="required" class="form-control">
			</div>

			<div class="form-group">
				<label>รหัสผ่านใหม่</label>
				<input type="password" name="new_password" data-validation="required" class="form-control">
			</div>

			<div class="form-group">
				<label>ยืนยันรหัสผ่านใหม่</label>
				<input type="password" name="confirm_new_password" data-validation="required" class="form-control">
			</div>

			<button class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Confirm </button>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('input[name=old_password]').focus();

		$('#form_change_password').submit(function(e) {
			e.preventDefault();
			$.validate();

			gojax('post', '/api/v1/user/change_password', {
				old_password: $('input[name=old_password]').val(),
				password: $('input[name=new_password]').val(),
				confirm_new_password: $('input[name=confirm_new_password]').val()
			}).done(function(data) {
				if (data.result === true) {
					alert(data.message);
					$('#form_change_password').trigger('reset');
					// $('input[name=old_password]').focus();
					window.location = "/user/logout";
				} else {
					alert(data.message);
				}
			});
		});
	});
</script>