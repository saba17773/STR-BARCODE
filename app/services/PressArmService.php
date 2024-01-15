<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class PressArmService
{
	public function all()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
				$conn,
				"SELECT * FROM PressArmMaster"
			);
		return $query;
	} 
}