<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;

class ScheduleService
{
    public function bindGrid($shift, $date)
    {
        $conn = Database::connect();
        $query = Sqlsrv::queryJson(
            $conn,
            "SELECT R.Machine,CONVERT(date, R.DateRateBuild) DateRateBuild,
            R.Shift,R.CreateBy,MAX(R.CreateDate) CreateDate,
            REPLACE(U.Name,'null','') Name
            FROM RateBuildSchedule R JOIN
            UserMaster U ON R.CreateBy = U.ID
            WHERE R.Active = 1 AND R.Shift = ?
            AND CONVERT(DATE,R.DateRateBuild) = ?
            AND CreateDate = (SELECT MAX(CreateDate) FROM RateBuildSchedule 
            WHERE Active = 1 AND Shift = R.Shift
            AND CONVERT(DATE,DateRateBuild) = CONVERT(DATE,R.DateRateBuild) AND Machine = R.Machine)
            GROUP BY  R.Machine,CONVERT(date, R.DateRateBuild),
            R.Shift,R.CreateBy,
            REPLACE(U.Name,'null','') 
            ORDER BY CONVERT(date, R.DateRateBuild),R.Machine
            ",
            [
                $shift,
                $date
            ]
        );
        return $query;
    }

    public function bindGridLine($machine, $dateinter, $shift)
    {
        $conn = Database::connect();
        $query = Sqlsrv::queryJson(
            $conn,
            "SELECT R.Machine,R.Code,
            CONVERT(varchar, R.DateRateBuild, 103) DateRateBuild,
            R.Shift,R.Total,R.CreateBy,
            R.CreateDate,
            REPLACE(U.Name,'null','') Name,
            REPLACE(U2.Name,'null','') UpdateName,
            R.UpdateBy,R.Active,
            R.UpdateDate
            FROM RateBuildSchedule R JOIN
            UserMaster U ON R.CreateBy = U.ID JOIN
            UserMaster U2 ON R.UpdateBy = U2.ID
            WHERE R.Machine= ? AND 
            CONVERT(date,R.DateRateBuild) = ? AND 
            R.Shift = ? AND R.Active = 1
			",
            [
                $machine,
                $dateinter,
                $shift
            ]
        );
        return $query;
    }

    public function getMachine()
    {
        $conn = Database::connect();

        $query = Sqlsrv::queryArray(
            $conn,
            "SELECT ID AS Machine
            FROM BuildingMaster
            WHERE Type IS NOT NULL AND ID NOT IN ('ZS4','S4')"
        );
        return $query;
    }

    public function CountMachine()
    {
        $conn = Database::connect();

        $query = Sqlsrv::queryArray(
            $conn,
            "SELECT Count(ID) C
            FROM BuildingMaster
            WHERE Type IS NOT NULL AND ID NOT IN ('ZS4','S4')"
        );
        return $query[0]['C'];
    }

    public function getPressMaster()
    {
        $conn = Database::connect();

        $query = Sqlsrv::queryArray(
            $conn,
            "SELECT ID 
            FROM PressMaster 
            ORDER BY ID ASC"
        );
        return $query;
    }

    public function cureGrid($shift, $date)
    {
        $conn = Database::connect();
        $query = Sqlsrv::queryJson(
            $conn,
            "SELECT CM.*,UM.Name FROM CureSchedule CM
            LEFT JOIN UserMaster UM ON CM.CreateBy = UM.ID WHERE CM.SchDate = ?",
            [$date]

        );
        return $query;
    }
}
