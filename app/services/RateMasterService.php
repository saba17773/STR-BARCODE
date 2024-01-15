<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class RateMasterService
{
    public function RateGroup()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT ID,Description
            FROM RateGroupMaster	
			"
		);
		return $query;
    }
    
    public function bindGridBuild1($group,$buildtype)
    {
        $conn = Database::connect();
		$query = Sqlsrv::queryJson(
            $conn,
            "SELECT R.id,R.Machine,R.Qty1,R.Qty2,R.Qty3,R.RatePrice1,
			R.RatePrice2,R.RatePrice3,R.BuildTypeId,
			R.RateType,R.CreateBy,R.CreateDate,R.UpdateBy,
			R.UpdateDate,R.RateGroupID,R.Remark,
			CASE WHEN CONVERT(VARCHAR,R.PLY) = 0 THEN ''
			ELSE CONVERT(VARCHAR,R.PLY) END AS PLY,
			B.Description BuildType,
			G.Description RateGroup,
			CONVERT(varchar,Qty1) + '-' + CONVERT(varchar,Qty2) AS S2,
			CASE R.RateType 
			WHEN 'TBR' THEN '>= '+CONVERT(varchar,Qty3) 
			WHEN 'PCR' THEN CONVERT(varchar,Qty3)
			END AS S3
			FROM RateMaster R JOIN
			 BuildTypeMaster B ON R.BuildTypeId = B.BuildTypeId JOIN
			RateGroupMaster G ON R.RateGroupID = G.ID
			WHERE R.RateGroupID = ? AND R.BuildTypeId = ?
			ORDER BY R.Machine
            ",
            [
                $group,
                $buildtype
            ]
        );
            return $query;
            
    }

    public function bindGridBuild2($group,$buildtype)
    {

        $conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT R.id,R.Machine,R.Qty1,R.Qty2,R.Qty3
			,R.RatePrice1,R.RatePrice2,R.RatePrice3,R.BuildTypeId
			,R.RateType,R.CreateBy,R.CreateDate,R.UpdateBy,R.UpdateDate,
			R.RateGroupID,R.Remark,
			CASE WHEN CONVERT(VARCHAR,R.PLY) = 0 THEN ''
			ELSE CONVERT(VARCHAR,R.PLY) END AS PLY,
			B.Description BuildType,G.Description RateGroup
			FROM RateMaster R JOIN
			BuildTypeMaster B ON R.BuildTypeId = B.BuildTypeId JOIN
			RateGroupMaster G ON R.RateGroupID = G.ID
			WHERE R.RateGroupID = ? AND R.BuildTypeId = ?
			ORDER BY R.Machine
			",
			[
                $group,
                $buildtype
			]
		);
		return $query;    
    }

    public function getMachine($buildtype)
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			// "SELECT ID AS Machine,Type
            // FROM BuildingMaster  
            // WHERE ID NOT IN (SELECT Machine FROM RateMaster 
            // WHERE BuildTypeId = ? ) 	
			// ",
			"SELECT ID AS Machine,Type
			FROM BuildingMaster  
			WHERE Type IS NOT NULL AND ID NOT IN 
			(
				SELECT T3.Machine
				FROM 
				(
					SELECT T2.Machine,
					CASE T2.Type2 WHEN 1 THEN T2.Machine
					ELSE 
					 CASE WHEN T2.COUNT_Mac >= 3 THEN T2.Machine
					 ELSE NULL END
			
					END AS MAC
					FROM (
						SELECT T1.Machine,T1.Type2,
						COUNT(T1.Machine)OVER(PARTITION BY T1.Machine) AS COUNT_Mac
						FROM (
							SELECT Machine,BuildTypeId,PLY,
							CASE WHEN RateType =  'TBR' THEN '1' 
							WHEN RateType = 'PCR' AND (Machine = 'VMI01' OR Machine = 'VMI02') THEN '1' 
							ELSE '2' END AS Type2
							FROM RateMaster
							WHERE BuildTypeId = ? 
						)T1
					)T2
				)T3
				WHERE T3.MAC IS NOT NULL
				GROUP BY T3.Machine
			) 
			",
			[
                $buildtype
			]
		);
		return $query;
	}

	public function bindGridPLY($machine)
    {

        $conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT PLY
			FROM PlyMaster 
			WHERE Machine = ? AND 
			PLY NOT IN (SELECT PLY FROM RateMaster WHERE Machine=? AND BuildTypeId = 1)
			GROUP BY PLY
			",
			[
				$machine,
				$machine
			]
		);
		return $query;    
	}

	public function bindGridPLY2($machine)
    {

        $conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT PLY AS PLY2
			FROM PlyMaster 
			WHERE Machine = ? AND 
			PLY NOT IN (SELECT PLY FROM RateMaster WHERE Machine=? AND BuildTypeId = 2)
			GROUP BY PLY
			",
			[
				$machine,
				$machine
			]
		);
		return $query;    
	}

	public function getMachineType($machine)
    {
        $conn = Database::connect();
	    $query = Sqlsrv::queryArray(
			$conn,
			"SELECT Type 
			FROM BuildingMaster 
			WHERE ID = ?
			",
			[
				$machine
			]
		);
		
		if($query[0]["Type"] == 'TBR')
		{
			sqlsrv_commit($conn);
			return[
				"status" => 200
			];
        } 
        else
        {
			sqlsrv_rollback($conn);
			return[
			    "status" => 404
		    ];
		}   
    }

    public function getRateType($machine)
	{
        $conn = Database::connect();
        $query = Sqlsrv::queryArray(
            $conn,
            "SELECT Type
            FROM BuildingMaster
            WHERE ID = ?",
            [
                $machine
            ]
        );
        return $query[0]["Type"] ;
    }
    
    public function insertBuild_Builder($machine,$qty1,$qty2,$qty3,$ratep1,$ratep2,$ratep3,
                                        $remark,$createby,$RateType,$ply)
	{
		$conn = Database::connect();
        $date = date("Y-m-d H:i:s");

		$insert = Sqlsrv::insert(
			$conn,
			"INSERT INTO RateMaster(
				Machine,
				Qty1, 
				Qty2,
				Qty3, 
				RatePrice1,
				RatePrice2,
                RatePrice3,
                BuildTypeId,
                RateType,
				CreateBy,
				CreateDate,
                UpdateBy,
                UpdateDate,
                RateGroupID,
                Remark,
				PLY
				)VALUES(
				?, ?, ?, ?, 
				?, ?, ?, ?,
				?, ?, ?, ?,
                ?, ?, ?, ?)
			",
			[
                $machine,
                $qty1,
                $qty2,
                $qty3,
                $ratep1,
                $ratep2,
                $ratep3,
                1,
                $RateType,
                $createby,
                $date,
                $createby,
                $date,
                1,
				$remark,
				$ply
			]
		);

		if ($insert) 
		{
			sqlsrv_commit($conn);
			return[
				"status" => 200
		    ];
		} 
		else 
		{
			sqlsrv_rollback($conn);
			return[
				"status" => 404
		    ];
		}
    }

    public function insertBuild_ChangeCode($machine,$ratep1,$RateType,$createby,$remark,$cqty1,$ply2)
	{
		$conn = Database::connect();
        $date = date("Y-m-d H:i:s");

		$insert = Sqlsrv::insert(
			$conn,
			"INSERT INTO RateMaster(
				Machine,
				Qty1, 
				Qty2,
				Qty3, 
				RatePrice1,
				RatePrice2,
                RatePrice3,
                BuildTypeId,
                RateType,
				CreateBy,
				CreateDate,
                UpdateBy,
                UpdateDate,
                RateGroupID,
                Remark,
				PLY
				)VALUES(
				?, ?, ?, ?, 
				?, ?, ?, ?,
				?, ?, ?, ?,
                ?, ?, ?, ?) 
			",
			[
                $machine,
                $cqty1,
                1,
                1,
                $ratep1,
                0,
                0,
                2,
                $RateType,
                $createby,
                $date,
                $createby,
                $date,
                1,
				$remark,
				$ply2
			]
		);

		if ($insert) 
		{
			sqlsrv_commit($conn);
			return[
				"status" => 200
		    ];
		} 
		else 
		{
			sqlsrv_rollback($conn);
			return[
				"status" => 404
		    ];
		}
    }

    public function updateBuild_Builder($id,$qty1,$qty2,$qty3,$ratep1,$ratep2,$ratep3,
                                        $remark,$updateby)
	{
		$conn = Database::connect();
        $date = date("Y-m-d H:i:s");

		$insert = Sqlsrv::insert(
			$conn,
			"UPDATE RateMaster SET
				Qty1 = ?, 
				Qty2 = ?,
				Qty3 = ?,
				RatePrice1 = ?,
				RatePrice2 = ?,
                RatePrice3 = ?,
                UpdateBy = ?,
                UpdateDate = ?,
                Remark = ?
				WHERE id = ?
			",
			[
                $qty1,
                $qty2,
                $qty3,
                $ratep1,
                $ratep2,
                $ratep3,
                $updateby,
                $date,
                $remark,
                $id
			]
		);

		if ($insert) 
		{
			sqlsrv_commit($conn);
			return[
				"status" => 200
		    ];
		} 
		else 
		{
			sqlsrv_rollback($conn);
			return[
				"status" => 404
		    ];
		}
    }

    public function updateBuild_ChangeCode($id,$ratep1,$updateby,$remark,$ecqty1)
	{
		$conn = Database::connect();
        $date = date("Y-m-d H:i:s");

		$insert = Sqlsrv::insert(
			$conn,
			"UPDATE RateMaster SET
				Qty1 = ?,
				RatePrice1 = ?,
                UpdateBy = ?,
                UpdateDate = ?,
                Remark = ?
                WHERE id = ?
			",
			[
				$ecqty1,
                $ratep1,
                $updateby,
                $date,
                $remark,
                $id
			]
		);

		if ($insert) 
		{
			sqlsrv_commit($conn);
			return[
				"status" => 200
		    ];
		} 
		else 
		{
			sqlsrv_rollback($conn);
			return[
				"status" => 404
		    ];
		}
	}
	
	public function getMac()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT LEFT(ID,1) Line
			FROM PressMaster
			GROUP BY LEFT(ID,1)
			"
		);
		return $query;
	}

	public function insertCure($line,$cureprice,$createby,$type)
	{
		$conn = Database::connect();
        $date = date("Y-m-d H:i:s");

		$insert = Sqlsrv::insert(
			$conn,
			"INSERT INTO RateMaster (
				Machine,
				Qty1, 
				Qty2,
				Qty3, 
				RatePrice1,
				RatePrice2,
				RatePrice3,
				BuildTypeId,
				RateType,
				CreateBy,
				CreateDate,
				UpdateBy,
				UpdateDate,
				RateGroupID,
				Remark,
				PLY)
			SELECT ID, 
				0, 0, 0, ?, 
				0, 0, 0, ?, 
				?, ?, ?, ?, 
				2, '', 0
				FROM PressMaster
				WHERE LEFT(ID,1) = ?
			",
			[
				$cureprice,
				$type, 
				$createby,
				$date , 
				$createby,
				$date,
				$line 
			]
		);

		if ($insert) 
		{
			sqlsrv_commit($conn);
			return[
				"status" => 200
		    ];
		} 
		else 
		{
			sqlsrv_rollback($conn);
			return[
				"status" => 404
		    ];
		}
	}
	
	public function bindGridCure()
    {
        $conn = Database::connect();
		$query = Sqlsrv::queryJson(
            $conn,
            "SELECT Machine,RatePrice1,RateType
			FROM RateMaster
			WHERE RateGroupID = 2
			ORDER BY Machine
            "
        );
            return $query;
            
	}

	public function chkMacInsert($line)
	{
		$conn = Database::connect();
	    $query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(Machine) Mac
			FROM RateMaster
			WHERE RateGroupID = 2 AND 
			LEFT(Machine,1) = ?
			",
			[
				$line
			]
		);

		if($query[0]["Mac"] == 0)
		{
			sqlsrv_commit($conn);
			return[
				"status" => 200
			];
        } 
        else
        {
			sqlsrv_rollback($conn);
			return[
				"status" => 404
			];
		}   
		
	}

	public function updateCure($line,$cureprice,$createby,$type)
	{
		$conn = Database::connect();
        $date = date("Y-m-d H:i:s");

		$update = Sqlsrv::insert(
			$conn,
			"UPDATE RateMaster SET 
				RatePrice1 = ?,
				UpdateBy = ?,
				UpdateDate = ?
			WHERE RateGroupID = 2 AND LEFT(Machine,1) = ?
			",
			[
				$cureprice,
				$createby,
				$date,
				$line 
			]
		);

		if ($update) 
		{
			sqlsrv_commit($conn);
			return[
				"status" => 200
		    ];
		} 
		else 
		{
			sqlsrv_rollback($conn);
			return[
				"status" => 404
		    ];
		}
	}

	public function updateCureByMachine($machine,$cureprice,$updateBy,$type)
	{
		$conn = Database::connect();
        $date = date("Y-m-d H:i:s");

		$update = Sqlsrv::insert(
			$conn,
			"UPDATE RateMaster SET 
				RatePrice1 = ?,
				UpdateBy = ?,
				UpdateDate = ?,
				RateType = ?
			WHERE RateGroupID = 2 AND Machine = ?
			",
			[
				$cureprice,
				$updateBy,
				$date,
				$type,
				$machine
			]
		);

		if ($update) 
		{
			sqlsrv_commit($conn);
			return[
				"status" => 200
		    ];
		} 
		else 
		{
			sqlsrv_rollback($conn);
			return[
				"status" => 404
		    ];
		}
	}

	//getPayment
	public function getPayment()
    {
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT ID,Description
			FROM PaymentMaster	
			"
		);
		return $query;
		
	}
	
	//bindGrid_SEQ
	public function bindGrid_SEQ()
    {
        $conn = Database::connect();
		$query = Sqlsrv::queryJson(
            $conn,
            "SELECT *
			FROM RATEMASTER_SEQ 
			ORDER BY SeqGrpID
            "
        );
            return $query;
            
	}
	


}
