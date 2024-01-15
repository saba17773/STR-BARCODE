<?php

namespace App\Services;

use App\Components\Database as DB;
use App\Services\BarcodeService;
use Wattanar\Sqlsrv;

class RepairIncomeService
{
    public function saveRepairIncome($barcode) 
    {
        $conn = DB::connect();

        $create_inventtrans_moveout = sqlsrv_query(
            $conn,
            "INSERT INTO Repaircheck(
                Barcode,
                Type,
                CreatedDate,
                CreatedBy
            ) VALUES(
                ?, ?, ?, ?
            )",
            [
                $barcode,
                'IN',
                date('Y-m-d H:i:s'),
                $_SESSION["user_login"],
            ]
        );

        if (!$create_inventtrans_moveout) {
            sqlsrv_rollback($conn);
            return false;
        }else{
            // INSERT INTO SUCCESS
            return true;
        }
    }

    public function isBarcodeRepairIncome($barcode)
    {
        $conn = DB::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT Barcode 
            FROM Repaircheck
            WHERE Barcode = ?
            AND Type = 'IN'",
            [
                $barcode
            ]
        );
    }
}