<?php

namespace App\Components;

class Authentication
{
	public function isLogin()
	{
		if(isset($_SESSION["user_login"])){
			return true;
		} else {
			return false;
		}
	}
}