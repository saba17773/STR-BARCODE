<?php

namespace App\Models;

use App\Components\Database as DB;
use Wattanar\Sqlsrv;

class Barcode
{
    public $ID = null;
    public $QTY = null;
    public $StartBarcode = null;
    public $FinishBarcode = null;
    public $Status = null;
    public $CreateBy = null;
    public $CreateDate = null;
    public $Company = null;
    public $UpdateBy = null;
    public $UpdateDate = null;

    public function inRange($barcode)
    {
        $conn = DB::connect();
        $barcode = substr($barcode, 1);

        if (!is_numeric($barcode)) {
            return false;
        }

        return Sqlsrv::hasRows(
            $conn,
            "SELECT TOP 1 * FROM BarcodePrinting
            WHERE CONVERT(nvarchar, SUBSTRING(StartBarcode, 2, 11)) <= ?
            AND CONVERT(nvarchar, SUBSTRING(FinishBarcode, 2, 11)) >= ?",
            [
                $barcode,
                $barcode
            ]
        );
    }

    public function checkcure($barcode)
    {
        $conn = DB::connect();
        $barcodeid = $barcode;
        //$barcode = substr($barcode, 1);

        if (!is_numeric($barcodeid)) {
            return false;
        }

        return Sqlsrv::hasRows(
            $conn,
            "SELECT TOP 1 * FROM InventTable
            WHERE Barcode = ? AND CuringDate is not null",
            [
                $barcodeid

            ]
        );
    }

    public function changeBarcode($old_barcode, $new_barcode)
    {
        $conn = DB::connect();
        $date = date("Y-m-d H:i:s");

        if (sqlsrv_begin_transaction($conn) === false) {
            return false;
        }

        $update_inventtable = Sqlsrv::update(
            $conn,
            'UPDATE InventTable
            SET Barcode = ?
            WHERE Barcode = ?',
            [
                $new_barcode,
                $old_barcode
            ]
        );

        if (!$update_inventtable) {
            sqlsrv_rollback($conn);
            return false;
        }

        $update_inventtrans = Sqlsrv::update(
            $conn,
            'UPDATE InventTrans
            SET Barcode = ?
            WHERE Barcode = ?',
            [
                $new_barcode,
                $old_barcode
            ]
        );

        if (!$update_inventtrans) {
            sqlsrv_rollback($conn);
            return false;
        }

        $update_buildtrans = Sqlsrv::update(
            $conn,
            'UPDATE BuildTrans
            SET Barcode = ?
            WHERE Barcode = ?',
            [
                $new_barcode,
                $old_barcode
            ]
        );

        if (!$update_buildtrans) {
            sqlsrv_rollback($conn);
            return false;
        }

        $update_curetrans = Sqlsrv::update(
            $conn,
            'UPDATE CureTrans
            SET Barcode = ?
            WHERE Barcode = ?',
            [
                $new_barcode,
                $old_barcode
            ]
        );
        if (!$update_curetrans) {
            sqlsrv_rollback($conn);
            return false;
        }

        $update_CuringBOI = Sqlsrv::update(
            $conn,
            'UPDATE CuringBOI
            SET Barcode = ?
            WHERE Barcode = ?',
            [
                $new_barcode,
                $old_barcode
            ]
        );
        if (!$update_CuringBOI) {
            sqlsrv_rollback($conn);
            return false;
        }

        $update_InventJournalTrans = Sqlsrv::update(
            $conn,
            'UPDATE InventJournalTrans
            SET BarcodeID = ?
            WHERE BarcodeID = ?',
            [
                $new_barcode,
                $old_barcode
            ]
        );
        if (!$update_InventJournalTrans) {
            sqlsrv_rollback($conn);
            return false;
        }
        $update_SendToWHLine = Sqlsrv::update(
            $conn,
            'UPDATE SendToWHLine
            SET Barcode = ?
            WHERE Barcode = ?',
            [
                $new_barcode,
                $old_barcode
            ]
        );
        if (!$update_SendToWHLine) {
            sqlsrv_rollback($conn);
            return false;
        }

        $update_TransDefect = Sqlsrv::update(
            $conn,
            'UPDATE TransDefect
            SET Barcode = ?
            WHERE Barcode = ?',
            [
                $new_barcode,
                $old_barcode
            ]
        );
        if (!$update_TransDefect) {
            sqlsrv_rollback($conn);
            return false;
        }
        $insertlogchang = Sqlsrv::insert(
            $conn,
            "INSERT INTO LogChangBarcode(
				BeforeBarcode,
				AfterBarcode,
				CreateBy,
				CreateDate
			
			) VALUES(?, ?, ?, ?)",
            [
                $old_barcode,
                $new_barcode,
                $_SESSION["user_login"],
                $date
            ]
        );

        if (!$insertlogchang) {
            sqlsrv_rollback($conn);
            return false;
        }





        if ($update_inventtable && $update_inventtrans && $update_buildtrans && $update_CuringBOI  && $update_InventJournalTrans && $update_SendToWHLine && $update_TransDefect  && $insertlogchang) {
            sqlsrv_commit($conn);
            return true;
        } else {
            sqlsrv_rollback($conn);
            return false;
        }
    }

    public function checkwh($barcode)
    {
        $conn = DB::connect();
        
        return Sqlsrv::hasRows(
            $conn,
            "SELECT TOP 1 * FROM InventTable
            WHERE Barcode = ?
            AND WarehouseReceiveDate IS NOT NULL",
            [
                $barcode
            ]
        );
    }
}
