<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class GateService
{

	public function all()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM Gate"
		);
	}

	public function save($description)
	{
		$conn = Database::connect();

		$q = Sqlsrv::insert(
			$conn,
			"INSERT INTO Gate(Description)
			VALUES(?)",
			[$description]
		);

		if ($q) {
			return 200;
		} else {
			return "ทำรายการไม่สำเร็จ";
		}

	}
}