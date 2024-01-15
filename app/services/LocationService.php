<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class LocationService
{
	public function all()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn, 
			"SELECT 
			L.ID
			,L.Description
			,L.WarehouseID
			,L.ReceiveLocation
			,L.AutoIssue
			,L.InUse
			,L.Company
			,DP.ID [DisposalID]
			,DP.DisposalDesc
			,WH.Description[DescriptionWH]
			,L.ReverseReceiveLocation
			,L.ReturnReceiveLocation
			,L.UnpickReceiveLocation
	     	FROM Location L 
	     	LEFT JOIN WarehouseMaster WH ON L.WarehouseID = WH.ID
	     	LEFT JOIN DisposalToUseIn DP ON DP.ID = L.DisposalID
	     	ORDER BY L.ID ASC"
     	);
	}

	public function create($loca_name,$wh_name,$auto_issue,$loca_use, $receive_name, $disposal)
	{
		$loca_name = trim($loca_name);
		
		if (isset($loca_use)){
			$loca_use=1;
		}else{
			$loca_use=0;
		}

		if (isset($auto_issue)){
			$auto_issue=1;
		}else{
			$auto_issue=0;
		}

		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$query = Sqlsrv::insert(
			$conn,
			"INSERT INTO Location(
				Description,
				WarehouseID,
				AutoIssue,
				InUse,
				Company, 
				ReceiveLocation, 
				DisposalID
			) VALUES (
				?, ?, ?, ?, ?,
				?, ?
			)",
			[
				$loca_name,
				$wh_name,
				$auto_issue,
				$loca_use,
				$_SESSION["user_company"], 
				$receive_name, 
				$disposal
			]
		);

		if (!$query) {
			sqlsrv_rollback($conn);
			return 404;
		} else {
			sqlsrv_commit($conn);
			return 200;
		}
	}

	public function update($loca_name,$wh_name,$auto_issue,$loca_use,$id, $receive_name, $disposal)
	{

		$loca_name = trim($loca_name);
		if (isset($loca_use)){
			$loca_use=1;
		}else{
			$loca_use=0;
		}

		if (isset($auto_issue)){
			$auto_issue=1;
		}else{
			$auto_issue=0;
		}

		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$query = Sqlsrv::update(
				$conn,
				"UPDATE Location 
				SET Description = ?,
				WarehouseID = ?,
				AutoIssue = ?,
				InUse = ?,
				Company = ?,
				ReceiveLocation = ?,
				DisposalID = ?
				WHERE ID =?",
				[
					$loca_name,
					$wh_name,
					$auto_issue,
					$loca_use,
					$_SESSION["user_company"], 
					$receive_name, 
					$disposal, 
					$id
				]
			);
		if (!$query) {
			sqlsrv_rollback($conn);
			return false;
		} else {
			sqlsrv_commit($conn);
			return true;
		}
	}

	public function checkWhExistUpdate($loca_name,$wh_name,$auto_issue,$loca_use)
	{
		$loca_name = trim($loca_name);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM Location 
				WHERE Description = ? AND WarehouseID =? AND AutoIssue =? AND InUse =?",
				[$loca_name,$wh_name,$auto_issue,$loca_use]
			);
	}

	public function checkWhExist($loca_name)
	{	
		$loca_name = trim($loca_name);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
				$conn,
				"SELECT * FROM Location 
				WHERE Description = ? ",
				[$loca_name]
			);
	}

	public function getLocationByWarehouse($warehouseId)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT * FROM Location WHERE WarehouseID = ?",
			[$warehouseId]
		);
	}

	public function setLocation($locationId, $reverse, $return, $unpick)
	{
		$conn = Database::connect();
		
		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}

		$query = Sqlsrv::update(
			$conn,
			"UPDATE Location 
			SET ReverseReceiveLocation = ?,
			ReturnReceiveLocation = ?,
			UnpickReceiveLocation = ?
			WHERE ID = ?",
			[$reverse, $return, $unpick, $locationId]
		);

		if ($query) {
			sqlsrv_commit($conn);
			return 200;
		} else {
			sqlsrv_rollback($conn);
			return 400;
		}
	}

}