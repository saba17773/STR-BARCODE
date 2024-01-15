<?php

namespace App\Services;

use App\Components\Database as DB;
use App\Components\Utils;
use Wattanar\Sqlsrv;
use App\Services\BarcodeService;
use App\V2\Database\Connector;

class BatchService
{
    public function saveNewBatch($batch_chang, $barcode)
    {
        $conn = (new Connector)->dbConnect();
        $batch = substr($batch_chang,0,7);
        if (sqlsrv_begin_transaction($conn) === false) {
            return false;
        }

        // if ((int)date("Y") >= 2021) {
        //     $dsc = BATCH_DSC;
        // } else {
        //     $dsc = "";
        // }

        $barcodeInfo = BarcodeService::getBarcodeInfoV2($barcode);
         if ((int)date("Y") >= 2021) {
            $dsc = substr($barcodeInfo[0]['Batch'],7);
        } else {
            $dsc = "";
        }

        $updateInventTable = sqlsrv_query(
            $conn,
            "UPDATE InventTable 
			SET Batch = ?,
			DisposalID = ?
			WHERE Barcode = ?",
            [
                $batch . $dsc,
                19, // Change Batch
                $barcode
            ]
        );

        if (!$updateInventTable) {
            sqlsrv_rollback($conn);
            return 'Update Invent Table Failed!';
        }

        $moveOutInventTrans = sqlsrv_query(
            $conn,
            "INSERT INTO InventTrans(
                TransID,
                Barcode,
                CodeID,
                Batch,
                DisposalID,
                DefectID,
                WarehouseID,
                LocationID,
                QTY,
                UnitID,
                DocumentTypeID,
                Company,
                CreateBy,
                CreateDate,
                Shift
            ) VALUES(
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?
            )",
            [
                Utils::genTransId($barcode) . 1,
                $barcode,
                $barcodeInfo[0]['ItemID'],
                $barcodeInfo[0]['Batch'],
                $barcodeInfo[0]['DisposalID'],
                null,
                $barcodeInfo[0]['WarehouseID'],
                $barcodeInfo[0]['LocationID'],
                -1,
                1,
                2,
                $_SESSION['user_company'],
                $_SESSION['user_login'],
                date('Y-m-d H:i:s'),
                $_SESSION['Shift']
            ]
        );

        if (!$moveOutInventTrans) {
            sqlsrv_rollback($conn);
            return 'Move Out Invent Trans Failed!';
        }

        $moveInInventTrans = sqlsrv_query(
            $conn,
            "INSERT INTO InventTrans(
                TransID,
                Barcode,
                CodeID,
                Batch,
                DisposalID,
                DefectID,
                WarehouseID,
                LocationID,
                QTY,
                UnitID,
                DocumentTypeID,
                Company,
                CreateBy,
                CreateDate,
                Shift
            ) VALUES(
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?
            )",
            [
                Utils::genTransId($barcode) . 2,
                $barcode,
                $barcodeInfo[0]['ItemID'],
                $batch . $dsc,
                19, // Change Batch
                null,
                $barcodeInfo[0]['WarehouseID'],
                $barcodeInfo[0]['LocationID'],
                1,
                1,
                1,
                $_SESSION['user_company'],
                $_SESSION['user_login'],
                date('Y-m-d H:i:s'),
                $_SESSION['Shift']
            ]
        );

        if (!$moveInInventTrans) {
            sqlsrv_rollback($conn);
            return 'Move In Invent Trans Failed!';
        }

        // $moveOutOnhand = sqlsrv_query(
        //     $conn,
        //     "UPDATE Onhand 
        //     SET QTY += ?
        //     WHERE CodeID = ?
        //     AND WarehouseID = ?
        //     AND LocationID = ?
        //     AND Batch = ?
        //     AND Company = ?
        //     AND QTY > 0",
        //     [
        //         -1,
        //         $barcodeInfo[0]["ItemID"],
        //         $barcodeInfo[0]["WarehouseID"],
        //         $barcodeInfo[0]["LocationID"],
        //         $barcodeInfo[0]["Batch"],
        //         $barcodeInfo[0]["Company"]
        //     ]
        // );

        // if (!$moveOutOnhand) {
        //     sqlsrv_rollback($conn);
        //     return "Move Out Onhand Failed!";
        // }

        // $moveInOnhand = sqlsrv_query(
        //     $conn,
        //     "UPDATE Onhand SET QTY += 1
        //     WHERE CodeID = ?
        //     AND WarehouseID = ?
        //     AND LocationID = ?
        //     AND Batch = ?
        //     AND Company =?
        //     IF @@ROWCOUNT = 0
        //     INSERT INTO Onhand 
        //     VALUES (?, ?, ?, ?, ?, ?)",
        //     [
        //         $barcodeInfo[0]["ItemID"],
        //         $barcodeInfo[0]["WarehouseID"],
        //         $barcodeInfo[0]["LocationID"],
        //         $batch,
        //         $barcodeInfo[0]["Company"],
        //         $barcodeInfo[0]["ItemID"],
        //         $barcodeInfo[0]["WarehouseID"],
        //         $barcodeInfo[0]["LocationID"],
        //         $batch,
        //         1, // qty
        //         $barcodeInfo[0]["Company"]
        //     ]
        // );

        // if (!$moveInOnhand) {
        //     sqlsrv_rollback($conn);
        //     return "move in onhand error.";
        // }

        sqlsrv_commit($conn);
        return true;
    }
}
