<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class FIFOBatchService
{
	public function all()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
				$conn,
				"SELECT * FROM FIFOBatch ORDER BY ID "
			);
		return $query;
	}
	public function insertData($product_grp, $qty_min , $aging_date)
	{
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}
		$query = Sqlsrv::insert(
				$conn,
				"INSERT INTO FIFOBatch(
					ProductGroup, QTYMin,
					Aging, 
					CreateBy, CreateDate,
					Company, UpdateBy,
					UpdateDate	
				) VALUES(
					?, ?, ?, ?, 
					?, ?, ?, ?
				)",
				[
					$product_grp,
					$qty_min,
					$aging_date,
					$_SESSION["user_login"],
					$date,
					$_SESSION["user_company"],
					$_SESSION["user_login"],
					$date
				]
			);

		if ($query) {
			sqlsrv_commit($conn);
			return[
				 "status" => 200,
				 "message" => "true"
			];
			
			
		} else {
			sqlsrv_rollback($conn);
			return[
				"status" => 404,
				"message" => "false"
			];
		}
		
	}
    public function updateData($product_grp, $qty_min , $aging_date,$id)
	{
		$conn = Database::connect();
		$date = date("Y-m-d H:i:s");

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}
		$query = Sqlsrv::update(
				$conn,
				"UPDATE FIFOBatch SET 
					ProductGroup = ?, 
                    QTYMin = ?,
					Aging = ?, 
					Company = ?, 
                    UpdateBy = ?,
					UpdateDate = ?	
                 WHERE ID = ?
                ",
				[
					$product_grp,
					$qty_min,
					$aging_date,
					$_SESSION["user_company"],
					$_SESSION["user_login"],
                    $date,
                    $id
				]
			);

		if ($query) {
			sqlsrv_commit($conn);
			return[
				 "status" => 200,
				 "message" => "true"
			];
			
			
		} else {
			sqlsrv_rollback($conn);
			return[
				"status" => 404,
				"message" => "false"
			];
		}
		
	}
	public function chkProductGrp($product_grp)
	{
		$conn = Database::connect();

		if (sqlsrv_begin_transaction($conn) === false) {
			return "transaction failed!";
		}
		$qChkGrp = Sqlsrv::queryArray(
				$conn,
				"select Count(ID) as ChkID
				from FIFOBatch
				where ProductGroup = ?",
				[
					$product_grp
				]
			);

		if($qChkGrp[0]["ChkID"] <= 0){
			sqlsrv_commit($conn);
			return[
				 "status" => 200,
				 "message" => "true"
			];
		}elseif($qChkGrp[0]["ChkID"] == 1) {
			sqlsrv_rollback($conn);
			return[
				 "status" => 404,
				 "message" => "false"
			];
		}
		
	}
}