<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class ImportService
{
	public function isCureTireExist($curetire_code)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT ID
			FROM CureCodeMaster
			WHERE ID = ?",
			[
				$curetire_code
			]
		);
	}

	public function isTopTurnChange($curetire_code, $rate12, $rate24)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT ID
			FROM CureCodeMaster
			WHERE ID = ?
			AND rate12 = ?
			AND rate24 = ?",
			[
				$curetire_code,
				$rate12,
				$rate24
			]
		);
	}

	public function updateTopTurn($curetire_code, $rate12, $rate24)
	{
		$conn = Database::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE CureCodeMaster
			SET rate12 = ?,
			rate24 = ?
			WHERE ID = ?
			",
			[
				$rate12,
				$rate24,
				$curetire_code
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function insertTopTurnLog($curetire_code, $rate12, $rate24)
	{
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");
		$query = Sqlsrv::update(
			$conn,
			"INSERT INTO TopturnLog(CureCode,Name,rate12,rate24,CreateDate,CreateBy)
			VALUES(?, ?, ?, ?, ?, ?)
			",
			[
				$curetire_code,
				$curetire_code,
				$rate12,
				$rate24,
				$date,
				$_SESSION["user_login"]
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function createNewCureCode($curetire_code, $description, $item, $greentire)
	{
		$conn = Database::connect();
		$query = Sqlsrv::insert(
			$conn,
			"INSERT INTO CureCodeMaster(ID,Name,ItemID,GreentireID,Company,rate12,rate24)
			VALUES(?, ?, ?, ?, ?, ?, ?)",
			[
				strtoupper($curetire_code),
				$description,
				$item,
				$greentire,
				$_SESSION["user_company"],
				0,
				0
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function updateCureCode($curetire_code, $description, $item, $greentire)
	{
		$conn = Database::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE CureCodeMaster
			SET Name = ?,
			ItemID = ?,
			GreentireID = ?
			WHERE ID = ?
			",
			[
				$description,
				strtoupper($item),
				strtoupper($greentire),
				strtoupper($curetire_code)
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	//rate build
	public function isSchBuildExist($machine, $datebuild, $shift, $code)
	{
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT ID 
			FROM RateBuildSchedule 
			WHERE Machine = ? AND 
			CONVERT(date,DateRateBuild) = ? AND 
			Shift = ? AND Active = 1 ",
			[
				$machine,
				$datebuild,
				$shift
			]
		);
	}

	public function createNewBuildSch($machine, $datebuild, $shift, $code, $total)
	{
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");
		$query = Sqlsrv::insert(
			$conn,
			"INSERT INTO RateBuildSchedule(Machine,Code,DateRateBuild,
				Shift,Total,CreateBy,CreateDate,UpdateBy,UpdateDate,Active)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
			[
				$machine,
				$code,
				$datebuild,
				$shift,
				$total,
				$_SESSION["user_login"],
				$date,
				$_SESSION["user_login"],
				$date,
				1
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function updateBuildSch($machine, $datebuild, $shift, $code)
	{
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");
		$query = Sqlsrv::update(
			$conn,
			"UPDATE RateBuildSchedule 
			SET UpdateBy = ?,
			UpdateDate = ?,
			Active = ?
			WHERE Machine = ? AND CONVERT(DATE,DateRateBuild) = ? AND Shift = ?
			AND Active = 1 AND CreateDate <> ?
			",
			[
				$_SESSION["user_login"],
				$date,
				0,
				$machine,
				$datebuild,
				$shift,
				$date
			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function createNewCureSch($datesch, $Prno, $Psside, $Curecode)
	{
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");
		if ($Psside == 'r') {
			$Psside = 'R';
		}
		if ($Psside == 'l') {
			$Psside = 'L';
		}
		if ($Psside == 'all') {
			$Psside = 'ALL';
		}
		$query = Sqlsrv::insert(
			$conn,
			"INSERT INTO CureSchedule(SchDate,PressNo,Presside,
				Curecode,CreateBy,CreateDate)
			VALUES(?, ?, ?, ?, ?, ?)",
			[
				$datesch,
				$Prno,
				$Psside,
				$Curecode,
				$_SESSION["user_login"],
				$date

			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function isSchCheckCureExist($data1, $data2, $data3, $data4)
	{

		// if ($data4 == NULL || $data4 == "") {
		// 	$data4 = "AP01";
		// }
		$conn = Database::connect();
		$check1 =	Sqlsrv::hasRows(
			$conn,
			"SELECT ID
			FROM CureCodeMaster
			WHERE ID = ?",
			[
				$data4
			]
		);

		$conn = Database::connect();
		$check2 = Sqlsrv::hasRows(
			$conn,
			"SELECT ID
			FROM PressMaster
			WHERE ID = ?",
			[
				$data2
			]
		);

		// if ($data3 !== "R" || $data3 !== "L" || $data3 !== "ALL") {
		// 	return false;
		// }


		if ($check1 == false || $check2 == false) {
			return false;
		} else {
			if ($data3 == "R" || $data3 == "L" || $data3 == "ALL" || $data3 == "all" || $data3 == "All" || $data3 == "r" || $data3 == "l") {
				return true;
			} else {
				return false;
			}
		}
	}
}
