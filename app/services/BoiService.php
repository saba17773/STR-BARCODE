<?php

namespace App\Services;

use App\Components\Database as DB;
use App\Components\Utils;
use Wattanar\Sqlsrv;
use App\Libs\InventTable;
use App\Libs\Onhand;

class BoiService
{
	public function all()
	{
		$conn = DB::connect();
		return Sqlsrv::queryJson($conn, "SELECT * FROM BOITable");
	}

	public function boi()
	{
		$conn = DB::connect();
		return Sqlsrv::queryJson($conn, "SELECT * FROM BOITable");
	}

	public function create($boi, $desc)
	{
		// if (self::isExist($id) === true) {
		// 	return false;
		// }
		$checkBOI = Sqlsrv::queryArray(
			$conn,
			"SELECT  ID FROM BOITable
			WHERE ID = ?",
			[$boi]
		);
		if ($checkBOI) {
			return false;
		}

		$conn = DB::connect();
		$query =  Sqlsrv::insert(
			$conn,
			"INSERT INTO BOITable(ID, Description, Company) VALUES (?, ?, ?)",
			[$boi, $desc, $_SESSION["user_company"]]
		);

		if ($query) {
			return [
				"status" => true

			];
		} else {
			return [
				"status" => false,
				"message" => $boi . " มีอยู่แล้วในระบบ"

			];
		}
	}

	public function update($boi, $desc)
	{


		$conn = DB::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE BOITable
				SET	Description = ?
						WHERE ID = ?",
			[
				$desc,
				$boi,

			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}



	public function delete($id)
	{
		$conn = DB::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return false;
		}

		$hasRowPress = Sqlsrv::hasRows(
			$conn,
			'SELECT BOI
			FROM PressMaster
			WHERE BOI = ?',
			[$id]
		);

		if ($hasRowPress === true) {
			return false;
		}
		$hasRowBuild = Sqlsrv::hasRows(
			$conn,
			'SELECT BOI
			FROM BuildingMaster
			WHERE BOI = ?',
			[$id]
		);

		if ($hasRowBuild === true) {
			return false;
		}



		$q = Sqlsrv::delete(
			$conn,
			"DELETE FROM BOITable WHERE ID = ?",
			[$id]
		);

		if ($q) {
			sqlsrv_commit($conn);
			return true;
		} else {
			sqlsrv_rollback($conn);
			return false;
		}
	}

	public function allBOI()
	{
		$conn = DB::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM BOITable"
		);
		return $query;
	}

	public function BOIName($_id)
	{
		$conn = DB::connect();
		$NameBOI = Sqlsrv::queryArray(
			$conn,
			"SELECT Description FROM BOITable
			 WHERE ID =?",
			[$_id]
		);
		return  $NameBOI[0]["Description"];
	}

	public function allGT()
	{
		$conn = DB::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM GreentireCodeMaster"
		);
		return $query;
	}
	//truck
	public function alltruck()
	{
		$conn = DB::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM TruckMaster WHERE SendToWh  <> 0"
		);
		return $query;
	}

	public function allround()
	{
		$conn = DB::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM SendToWhRound"
		);
		return $query;
	}

	public function createround($desc)
	{

		// $checkBOI = Sqlsrv::queryArray(
		// 	$conn,
		// 	"SELECT  ID FROM BOITable
		// 	WHERE ID = ?",
		// 	[$boi]
		// );
		// if ($checkBOI) {
		// 	return false;
		// }

		$conn = DB::connect();
		$query =  Sqlsrv::insert(
			$conn,
			"INSERT INTO SendToWhRound([Description], CreateDate) VALUES (?,getdate())",
			[$desc]
		);

		if ($query) {
			return [
				"status" => true

			];
		} else {
			return [
				"status" => false,
				"message" => "มีอยู่แล้วในระบบ"

			];
		}
	}

	public function updateround($id, $desc)
	{


		$conn = DB::connect();
		$query = Sqlsrv::update(
			$conn,
			"UPDATE SendToWhRound
				SET	[Description] = ?
						WHERE ID = ?",
			[
				$desc,
				$id,

			]
		);

		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function deleteround($id)
	{
		$conn = DB::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return false;
		}

		// $hasRowPress = Sqlsrv::hasRows(
		// 	$conn,
		// 	'SELECT BOI
		// 	FROM PressMaster
		// 	WHERE BOI = ?',
		// 	[$id]
		// );

		// if ($hasRowPress === true) {
		// 	return false;
		// }
		// $hasRowBuild = Sqlsrv::hasRows(
		// 	$conn,
		// 	'SELECT BOI
		// 	FROM BuildingMaster
		// 	WHERE BOI = ?',
		// 	[$id]
		// );

		// if ($hasRowBuild === true) {
		// 	return false;
		// }



		$q = Sqlsrv::delete(
			$conn,
			"DELETE FROM SendToWhRound WHERE Id = ?",
			[$id]
		);

		if ($q) {
			sqlsrv_commit($conn);
			return true;
		} else {
			sqlsrv_rollback($conn);
			return false;
		}
	}

	public function alltmobiletruck()
	{
		$conn = DB::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM SendToWHTable WHERE Complete = 0"
		);
		return $query;
	}


	public function truckcheck($id)
	{
		$conn = DB::connect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT * FROM TruckMaster WHERE SendToWh  = 1 AND PlateNumber = '$id'"
		);
		return $query[0]["ID"];
	}


	public function truckchecksendtable($id)
	{
		$conn = DB::connect();
		$date = date("Y-m-d H:i:s");
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT Id,JournalID,TruckID + ' ' + JournalDescription AS roundcar
			 FROM SendToWHTable  
			 WHERE TruckID = '$id' AND Complete = 0 AND convert(date,CreateDate)  = '$date' "
		);
		return

			[
				"Id" => $query[0]["Id"],
				"JournalID" => $query[0]["JournalID"],
				"roundcar" => $query[0]["roundcar"]


			];
	}
}
