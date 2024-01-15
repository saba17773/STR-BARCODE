<?php

namespace App\compound;

use App\Components\Database;
use Wattanar\Sqlsrv;
use App\Components\Utils;
use App\Components\Security;
use App\V2\Database\Handler;
use App\V2\Batch\BatchAPI;
use App\V2\Database\Connector;

class CompoundAPI
{


	public function allMovementType()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
		$conn,
		"SELECT	CH.ID,
						CH.McID,
						CH.CompoundCodeID,
						CT.Type,
						CH.CompoundCodeTrans,
						CH.Weight_kg,
						CH.Pallet ,
						CH.Use_Pallet,
						ST.Description,
						UM.Name,
						CH.SCH_Plan,
						CH.createdate,
						CH.Remark,
						Palletcal =ISNULL(CH.Pallet, 0 ),
						Type1 =ISNULL(CT.Type, 'none' )
						FROM CompoundSchedule CH
						LEFT JOIN Status ST ON CH.Status = ST.ID
						LEFT JOIN UserMaster UM ON CH.Operator = UM.ID
						LEFT JOIN CompoundTable CT ON CH.Type = CT.Compound_Code"
		);
	}
	public function tb($McID)
	{
		$conn = Database::connect();
		$data = Sqlsrv::queryArray(
		$conn,
		"SELECT top 1 McID,CompoundCodeID,Pallet,Use_Pallet,Status,createdate
		FROM CompoundSchedule
		WHERE  McID = ? and (Status = ? or Status = ?) order by createdate asc",
		[$McID,1,2]
		);
		if ($data) {
		return
		[
			"status" => 200,
			"McID" => $data[0]["McID"],
			"CompoundCodeID" => $data[0]["CompoundCodeID"],
			"total_Pallet" => $data[0]["Pallet"] -$data[0]["Use_Pallet"] ,
			"datecreadte" => $data[0]["createdate"]
		];
			} else {
		return
		[
			"status" => 404,
			"message" => 'กรุณาเลือกรายการ',
		];
			}
		}

	public function remillandMix($datase)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
		$conn,
		"SELECT * FROM CompoundTable
		WHERE Type =? and Status=?",
		[$datase,5]
		);
	}

	public function Mc()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
		$conn,
		"SELECT ID,Description FROM McMaster"
		);
	}

	public function Compound_Code($MC)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
		$conn,
		"SELECT	FC.ItemCompound,
						IC.ItemID
		FROM CompoundFormula FC
		LEFT JOIN ItemCompound  IC ON FC.ItemCompound = IC.ID WHERE McID =?",
		[$MC]
		);
	}

	public function save($id, $Compound_Code,$Mix,$Weight,$idid)
	{
      $date = date("m-d-Y H:i:s");
			$conn = Database::connect();
			$calpallet = Sqlsrv::queryArray(
			$conn,
			"SELECT * from CompoundFormula WHERE ItemCompound = ? AND McID = ?",
			[$Compound_Code,$id]
			);
			$total =  $Weight/ $calpallet[0]["Weight"];
			$Pallet = ceil($total);
			if($idid == 0)
			{
					$query = Sqlsrv::update(
					$conn,
					"INSERT INTO CompoundSchedule(McID,CompoundCodeID,Type,Weight_kg,Pallet,Status,Operator,createdate)
					VALUES (?, ?, ?,?, ?, ?, ?, ? )",
					[
						$id,
						$Compound_Code,
						$Mix,
        		$Weight,
						$Pallet,
        		1,
        		$_SESSION["user_login"],
						$date
					]
				);
			}
			if($idid !== 0)
			{
					$query = Sqlsrv::update(
					$conn,
					"UPDATE CompoundSchedule SET McID = ?,CompoundCodeID = ?,Type = ?,Weight_kg = ?,Status = ?,Operator = ?,createdate = ?
					WHERE ID = ?",
					[
						$id,
						$Compound_Code,
						$Mix,
        		$Weight,
        		1,
        		$_SESSION["user_login"],
						$date,
						$idid
					]
				);
			}

			if ($query) {
			return[
							"status" => 200,
							"message" =>$Pallet
						];
			} else {
			return[

						"status" => 404,
						"message" => "กรุกรุณากรอกข้อมูลให้ครบถ้วน"
						];
					}
				}

	public function savePallet($Mc, $Compound_Code,$createdate,$Pallet_ID,$Weight,$statuscheck)
	{
		$conn = Database::connect();
		$date = date("m-d-Y H:i:s");
		$Pallet_check = Sqlsrv::queryArray(
		$conn,
		"SELECT *FROM CompoundSchedule  WHERE McID = ? and CompoundCodeID = ? and createdate = ?",
		[
			$Mc,
			$Compound_Code,
			$createdate
		]
		);
		if($statuscheck ==1)
		{
				$query = Sqlsrv::update(
				$conn,
				"UPDATE CompoundSchedule SET SCH_Plan = ?,Status = ?
				WHERE ID = ?",
				[
					$_SESSION["user_login"],
					3,
					$Pallet_check[0]["ID"]
				]
				);
		}
		if($statuscheck == 0)
		{
			if($Pallet_check[0]["Pallet"] == $Pallet_check[0]["Use_Pallet"])
			{
			return
			[
				"status" => 404,
				"message" => "จำนวนเกินกำหนด",
			];
		}
		else {
		$sum_UserPallet = $Pallet_check[0]["Use_Pallet"] +1 ;
		}
		$totolPallet = $Pallet_check[0]["Pallet"] - $sum_UserPallet;
		if(($Pallet_check[0]["Pallet"] - $Pallet_check[0]["Use_Pallet"]) !== 0)
		{
		$query = Sqlsrv::update(
		$conn,
		"UPDATE CompoundSchedule SET Use_Pallet = ?,SCH_Plan = ?,Status = ?
		WHERE ID = ?",
		[
			$sum_UserPallet,
			$_SESSION["user_login"],
			2,
			$Pallet_check[0]["ID"]
		]
		);
		// insert CompoundTable
		$wherehouse = Sqlsrv::queryArray(
		$conn,
		"SELECT UM.Username,
						UM.Warehouse,
						UM.Location,
						LL.WarehouseID,
						LL.ReceiveLocation,
						LL.Description,
						LL.DisposalID,
						LL.ID
	 					FROM UserMaster UM
	 					LEFT JOIN Location LL ON UM.Warehouse = LL.WarehouseID AND UM.Location = LL.ReceiveLocation
	 					WHERE UM.ID =?",
						[$_SESSION["user_login"]]);
						$strNewDate = date ("m-d-Y H:i:s", strtotime("+14 day"));
						$insertCompoundTable = Sqlsrv::update(
						$conn,
						"INSERT INTO CompoundTable(Pallet_ID,Compound_Code,Type,Compound_Code1,Mc,Weight,Wherehouse,Location,Disposition,proudction_Date,Expire_date,ProductionBy,Status)
						VALUES (?,?,?,?,?,?,?,?,?,?,?,?,? )",
						[
							$Pallet_ID,
							$Pallet_check[0]["CompoundCodeID"],
							$Pallet_check[0]["Type"],
			       	$Pallet_check[0]["CompoundCodeTrans"],
							$Pallet_check[0]["McID"],
							$Pallet_check[0]["Weight_kg"],
			       	$wherehouse[0]["WarehouseID"],
							$wherehouse[0]["ID"],
							$wherehouse[0]["DisposalID"],
							$date,
							$strNewDate,
							$_SESSION["user_login"],
							1
						]
					);
				}
		if($totolPallet == 0)
		{
		$query1 = Sqlsrv::update(
		$conn,
		"UPDATE CompoundSchedule SET SCH_Plan = ?,Status = ?
		WHERE ID = ?",
		[
			$_SESSION["user_login"],
			3,
			$Pallet_check[0]["ID"]
		]
		);
			}
		}
		if ($Pallet_check) {
		return
		[
			"status" => 200,
			"message" => $sum_UserPallet,
			"totalPallet" => $totolPallet
		];
		} else {
		return
		[
			"status" => 404,
			"message" => 'กรุณาเลือกรายการ',
		];
		}
		}

	public function deleteCompound($data)
	{
		$conn = Database::connect();
		$query = Sqlsrv::update(
		$conn,
		"DELETE FROM CompoundSchedule
		WHERE ID =?",
		[$data]
		);
		if ($query)
		{
		return 200;
		} else {
		return
		[
			"status" => 404,
			"message" => 'กรุณาเลือกรายการ'
		];
		}
		}

	public function updatestatus($data,$remark)
	{
		$conn = Database::connect();
		$query = Sqlsrv::update(
		$conn,
		"UPDATE CompoundSchedule SET Status = ?,Remark = ?
		WHERE ID = ?",
		[
			4,
			$remark,
      $data
		]
		);
		if ($query) {
		return 200;
		} else {
		return
		[
			"status" => 404,
			"message" => sqlsrv_errors()
		];
		}
	}
}	// End
