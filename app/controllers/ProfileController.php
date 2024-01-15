<?php

namespace App\Controllers;

use App\Models\User;

class ProfileController
{
	public function changePassword()
	{
		renderView("page/change_password");
	}

	public function updatePassword()
	{
		$old_password = filter_input(INPUT_POST, "old_password");
		$password = filter_input(INPUT_POST, "password");
		$confirm_new_password = filter_input(INPUT_POST, "confirm_new_password");
		$uid = $_SESSION["user_login"];	

		$u = new User;
		$u->password = $password;
		$u->id = $uid;

		if ($u->isPasswordMatch($uid, $old_password) === false) {
			$result = false;
			$message = "รหัสผ่านไม่ตรงกับในระบบ";
		} else if ($confirm_new_password !== $password) {
			$result = false;
			$message = "รหัสผ่านไม่ตรงกัน";
		} else {
			if ($u->updatePassword()) {
				$result = true;
				$message = "เปลี่ยนรหัสผ่านสำเร็จ";
			} else {
				$result = false;
				$message = "เปลี่ยนรหัสผ่านไม่สำเร็จ";
			}
		}

		return json_encode(["result" => $result, "message" => $message]);
	}
}