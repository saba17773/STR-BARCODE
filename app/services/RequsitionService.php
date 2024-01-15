<?php

namespace App\Services;

use Wattanar\Sqlsrv;
use App\Components\Database;
use App\Components\Utils;

class RequsitionService
{

	public function all()
	{
		$conn = Database::connect();

		$getUserWarehouseType = Sqlsrv::queryArray(
			$conn,
			"SELECT Type
			FROM WarehouseMaster
			WHERE ID = ?",
			[$_SESSION["user_warehouse"]]
		);

		$userWarehouseType = $getUserWarehouseType[0]["Type"];

		if ($_SESSION["user_name"] === "admin") {

			$sql = "SELECT * FROM RequsitionNote";

		} else if ($userWarehouseType === 2) { // Final

			$sql = "SELECT * FROM RequsitionNote WHERE Final = 1";

		} else if ($userWarehouseType === 3) { // FG

			$sql = "SELECT * FROM RequsitionNote WHERE FinishGood = 1";

		}

		return Sqlsrv::queryJson(
			$conn,
			$sql
		);
	}

	public function saveRequsitionNote($id, $description, $warehouse)
	{
		$conn = Database::connect();

		$date = Date('Y-m-d H:i:s');

		$insert = Sqlsrv::insert(
			$conn,
			"UPDATE RequsitionNote
			SET Description = ?,
			Final = ?,
			FinishGood = ?,
			UpdateBy = ?,
			UpdateDate = ?
			WHERE ID = ?
			IF @@ROWCOUNT = 0
			INSERT INTO RequsitionNote(
				Description,
				Final,
				FinishGood,
				CreateBy,
				CreateDate,
				Company,
				UpdateBy,
				UpdateDate
			) VALUES(?, ?, ?, ?, ?, ?, ?, ?)",
			[
				$description,
				$warehouse[0],
				$warehouse[1],
				$_SESSION["user_login"],
				$date,
				$id,

				$description,
				$warehouse[0],
				$warehouse[1],
				$_SESSION["user_login"],
				$date,
				$_SESSION["user_company"],
				$_SESSION["user_login"],
				$date
			]
		);

		if ($insert) {
			return 200;
		} else {
			return 400;
		}
	}


}
