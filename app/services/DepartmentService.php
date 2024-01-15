<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class DepartmentService
{
	public function all()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson($conn, "SELECT * FROM DepartmentMaster");
	}

	public function create($dep_name)
	{
		if (self::checkExist($dep_name) === true) {
			return false;
		}

		$conn = Database::connect();
		$query = Sqlsrv::insert(
				$conn,
				"INSERT INTO DepartmentMaster(Description) VALUES (?)",
				[$dep_name]
			);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function update($id, $dep_name)
	{

		$conn = Database::connect();
		$query = Sqlsrv::update(
				$conn,
				"UPDATE DepartmentMaster
				SET	Description = ?
		        WHERE Code = ?",
				[
					$dep_name,
					$id
				]
			);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}


	public function checkExist($dep_name)
	{
		$dep_name = trim($dep_name);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM DepartmentMaster
				WHERE Description = ?",
				[$dep_name]
			);
	}
}