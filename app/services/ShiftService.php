<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class ShiftService
{
	public function getAll()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM ShiftMaster"
		);
	}
}