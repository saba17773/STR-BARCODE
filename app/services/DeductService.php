<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class DeductService
{
    public function machine_TBR()
	{
        $conn = Database::connect();
        $sql = "SELECT ID
				FROM BuildingMaster
				WHERE Type = 'TBR' AND 
				ID NOT IN ('S4','ZS4')
				
				ORDER BY ID";        
        $query = Sqlsrv::queryJson(
            $conn,
            $sql
        );
        return $query;
	}
	
	public function machine_PCR()
	{
        $conn = Database::connect();
        $sql = "SELECT ID
				FROM BuildingMaster
				WHERE Type = 'PCR'AND 
				ID NOT IN ('S4','ZS4')				
				ORDER BY ID";        
        $query = Sqlsrv::queryJson(
            $conn,
            $sql
        );
        return $query;
    }

    public function bindGrid($date,$shift,$machine)
    {

		if ($shift == "day")
		{
			$tstart = $date. " 08:00:00";
			$tend = $date. " 19:59:59";
		}
		else 
		{
			$subdate= str_replace('-', '/', $date);
			$tomorrow = date('Y-m-d',strtotime($subdate . "+1 days"));

			$tstart = $date. " 20:00:00";
			$tend = $tomorrow. " 07:59:59";
		}

        $conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT T5.*,
			T5.P1+T5.P2+T5.P3+T5.P4 AS Total,
			D.Charge,D.Remark,D.Id,D.DeductDate
			FROM
			(
				SELECT T4.*,
				CASE WHEN T4.RateType = 'PCR' AND (T4.COUNT_MAC < 3 )
				THEN (T4.P1+T4.P2+T4.P3)/2 ELSE 0
				END AS P4
				FROM
				(
					SELECT T3.*,R.RateType,
					CASE WHEN T3.Act >= R.Qty1 THEN R.RatePrice1 ELSE 0 END AS 'P1',
					CASE 
						WHEN (T3.Act-R.Qty1) >= (R.Qty2-R.Qty1) THEN (R.Qty2-R.Qty1)*R.RatePrice2 
						WHEN  T3.Act > R.Qty1 AND ((T3.Act-R.Qty1)<=(R.Qty2-R.Qty1)) THEN (T3.Act-R.Qty1)*R.RatePrice2 
					ELSE 0
					END AS 'P2',
					CASE R.RateType
					WHEN 'TBR' THEN
						CASE WHEN T3.Act >= R.Qty3 THEN (T3.Act-R.Qty2) * R.RatePrice3 ELSE 0 END
					WHEN 'PCR' THEN
						CASE WHEN  T3.Act >= R.Qty2 THEN ((T3.Act-R.Qty2) /R.Qty3)* R.RatePrice3 ELSE 0 END
					ELSE 0
					END AS 'P3',
					COUNT(T3.Machine) OVER (PARTITION BY T3.Machine) COUNT_MAC
					FROM
					(
						SELECT T1.CreateBy,T1.Machine,T1.LoginDate,T1.LogoutDate,
						T1.Shift,T1.BuildTypeId,T1.EmployeeID,T1.Name,T1.BuildType,
						T1.Row,T1.DAAY,T2.Act
						FROM
						(
							SELECT B.CreateBy,B.Machine,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,R.BuildTypeId
							,ROW_NUMBER() OVER(partition by B.CreateBy ORDER BY R.LoginDate DESC) AS Row,
							U.EmployeeID,REPLACE(U.Name,'null','') Name,T.Description BuildType,
							CASE 
								WHEN CONVERT(INT,LEFT(CONVERT(VARCHAR, R.LoginDate, 8),2)) <= 7 
									THEN DATEADD(DAY,-1,CONVERT(DATE,R.LoginDate))
								ELSE CONVERT(DATE,R.LoginDate)
							END AS DAAY
							FROM BuildTrans B JOIN
							RateTrans R ON B.CreateBy = R.UserId AND (B.CreateDate BETWEEN R.LoginDate AND R.LogoutDate)
							JOIN UserMaster U ON B.CreateBy = U.ID JOIN
							BuildTypeMaster T ON R.BuildTypeId = T.BuildTypeId 
							WHERE B.CreateDate BETWEEN ? AND ?
							AND R.LoginDate BETWEEN ? AND ?
							AND R.LogoutDate BETWEEN ? AND ?
							AND R.RateGroupID = 1 
							GROUP BY B.CreateBy,B.Machine ,R.UserId,R.LoginDate,R.LogoutDate,R.Shift,U.EmployeeID,
							REPLACE(U.Name,'null',''),T.Description,R.BuildTypeId
						)T1 JOIN
						(
							SELECT B.CreateBy,COUNT(B.Barcode) 'Act',BM.Type
							FROM BuildTrans B JOIN
							InventTable I ON B.Barcode = I.Barcode JOIN
							BuildingMaster BM ON B.Machine = BM.ID
							WHERE B.CreateDate BETWEEN ? AND ?
							AND I.CheckBuild = 1 
							AND B.Barcode NOT IN (SELECT Barcode FROM ExceptBuildRate)
							GROUP BY B.CreateBy,BM.Type
						)T2 ON T1.CreateBy = T2.CreateBy
						WHERE T1.Row = 1 AND T1.Machine = ?
					)T3 JOIN RateMaster R ON T3.Machine = R.Machine AND T3.BuildTypeId = R.BuildTypeId
					WHERE T3.Act >= R.Qty1 
				)T4
			)T5 LEFT OUTER JOIN 
			DeductRateBuild D ON T5.CreateBy = D.UserId AND T5.Machine = D.Machine
			AND CONVERT(DATE,D.DeductDate) = T5.DAAY
			ORDER BY T5.BuildTypeId,T5.LoginDate			
			",
			[
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$tstart,
					$tend,
					$machine
			]
		);
		return $query;    
    }

    public function checkLog($date,$machine,$userid,$shift)
    {
        $conn = Database::connect();
	    $query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(UserId) UserId
			FROM DeductRateBuild
			WHERE DeductDate = ? 
			AND Machine = ? AND UserId = ? 
			AND Shift = ?
			",
			[
				$date,
				$machine,
				$userid,
				$shift
			]
		);
		
		if($query[0]["UserId"] == 0)
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

    public function insertDeduct($deductDate,$machine,$userid,$charge,$remark,$createby,$shift,$buildtypeid)
	{
		$conn = Database::connect();
        $date = date("Y-m-d H:i:s");
                
		$insert = Sqlsrv::insert(
			$conn,
			"INSERT INTO DeductRateBuild(
				DeductDate,
				Machine, 
				UserId,
				Shift, 
				Charge,
				Remark,
				CreateBy,
				CreateDate,
                UpdateBy,
                UpdateDate,
                BuildTypeId
				)VALUES(
				?, ?, ?, ?, 
				?, ?, ?, ?,
				?, ?, ?)
			",
			[
				$deductDate,
				$machine, 
				$userid,
				$shift, 
				$charge,
				$remark, 
				$createby,
				$date, 
				$createby,
				$date,
				$buildtypeid
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
        
    public function updateDeduct($charge,$remark, $updateby,$id)
    {
        $conn = Database::connect();
        $updatedate = date("Y-m-d H:i:s");
		
		$update = Sqlsrv::insert(
            $conn,
			"UPDATE DeductRateBuild SET 
			Charge = ?,
			Remark = ? ,
			UpdateBy = ? ,
			UpdateDate = ?
			WHERE Id = ?
			",
			[
                $charge,
				$remark, 
                $updateby,
                $updatedate,
                $id
			]
		);

        if ($update) 
        {
			sqlsrv_commit($conn);
            return
            [
				"status" => 200
		    ];
		} 
		else 
        {
			sqlsrv_rollback($conn);
            return
            [
				"status" => 404
			];
		}
    }
	
	public function bindGridDeduct($userid)
    {

        $conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT Id,
			CONVERT(varchar, DeductDate, 103) DeductDate,
			Machine,UserId,
			CASE Shift WHEN 1 THEN 'A' 
			ELSE 'B' END AS Shift,
			Charge,Remark,CreateBy,CreateDate,
			UpdateBy,UpdateDate
			FROM DeductRateBuild
			WHERE UserId = ?
			",
			[
					$userid
			]
		);
		return $query;    
	}
	
	public function Type_TBR()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT '- ALL TBR -' Machine
			UNION ALL
			SELECT ID AS Machine
			FROM BuildingMaster
			WHERE Type = 'TBR'		
			"
		);
		return $query;
	}

	public function Type_PCR()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT '- ALL PCR -' Machine
			UNION ALL
			SELECT ID AS Machine
			FROM BuildingMaster
			WHERE Type = 'PCR'		
			"
		);
		return $query;
	}

	public function bindGridEmp()
    {

        $conn = Database::connect();
		$query = Sqlsrv::queryJson(
			$conn,
			"SELECT ID,EmployeeID,REPLACE(NAME,'null','') Name,
			Status
			FROM UserMaster
			WHERE Warehouse = 1 AND Location = 1
			ORDER BY EmployeeID
			"
		);
		return $query;    
	}

}
