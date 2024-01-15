<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class MoldService
{
	public function all()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
				$conn,
				"SELECT * FROM MoldMaster"
			);
		return $query;
	}

	public function create($desc, $id)
	{
		$id = trim($id);
		$conn = Database::connect();
		if (self::checkWhExist($id) === false) {
			$query = Sqlsrv::insert(
					$conn,
					"INSERT INTO MoldMaster(
						ID, Description, Company
					) VALUES (
						?, ?, ?
					)",
					[$id, $desc, $_SESSION["user_company"]]
				);
			if (!$query) {
				return false;
			}

			return true;
		} else {
			return false;
		}
	}

	public function update($desc, $id)
	{
		$id = trim($id);
		$conn = Database::connect();

		$query = Sqlsrv::update(
				$conn,
				"UPDATE MoldMaster 
				SET Description = ?,
				Company = ?
				WHERE ID =?",
				[$desc, $_SESSION["user_company"], $id]
			);
		if (!$query) {
			return false;
		} else {
			return true;
		}
	}

	public function checkWhExist($id)
	{
		$id = trim($id);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM MoldMaster
				WHERE ID = ?",
				[$id]
			);
	}
}