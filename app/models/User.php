<?php

namespace App\Models;

use Wattanar\Sqlsrv;
use App\Components\Database as DB;

class User
{
	public $id = null;
	public $username = null;
	public $password = null;
	public $name = null;
	public $department = null;
	public $warehouse = null;
	public $location = null;
	public $authorize = null;
	public $employeeid = null;
	public $barcode = null;
	public $shift = null;
	public $status = null;
	public $permissionid = null;
	public $company = null;
	public $directto = null;
	public $directtomobile = null;

	public function updatePassword()
	{
		$conn = DB::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE UserMaster 
			SET Password = ?
			WHERE ID = ?",
			[
				$this->password,
				$this->id
			]
		);

		if ($query) {
			return true;		
		} else {
			return false;
		}
	}

	public function isPasswordMatch($uid, $password)
	{
		$conn = DB::connect();
		return Sqlsrv::hasRows(
			$conn, 
			"SELECT ID FROM UserMaster WHERE ID = ? AND Password = ?",
			[$uid, $password]
		);
	}

	public function getAuthorizeId($username)
	{
		$conn = DB::connect();
		$field_id = Sqlsrv::queryArray(
			$conn,
			"SELECT u.Authorize FROM UserMaster u
			WHERE u.Username = ? ",
			[
				$username
			]
		);

		return $field_id; // int
	}

	public function isLogin()
	{
		$conn = DB::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT Username, Password
			FROM UserMaster
			WHERE Username COLLATE Latin1_General_CS_AS = ?
			AND Password COLLATE Latin1_General_CS_AS = ?
			",
			[$this->username, $this->password]
		);
	}
}