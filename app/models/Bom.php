<?php

namespace App\Models;

use Wattanar\Sqlsrv;
use App\Components\Database as DB;
use App\Models\ItemSet as IS;
use App\Models\Barcode as B;
use App\Models\InventTable as IT;
use App\Components\Utils as U;
use App\Models\Item;
use App\Services\OnhandService;

class Bom
{
    public $id = null;
    public $barcode = null;
    public $item_id = null;
    public $item_set_id = null;
    public $create_by = null;
    public $create_date = null;

    // public function save() 
    // {
    //     $conn = DB::connect();
    //     $save = Sqlsrv::insert(
    //         $conn,
    //         "INSERT INTO Bom(
    //             barcode,
    //             item_id,
    //             item_set_id,
    //             create_by,
    //             create_date
    //         ) VALUES (?, ?, ?, ?, ?)",
    //         [
    //             $this->barcode,
    //             $this->item_id,
    //             $this->item_set_id,
    //             $this->create_by,
    //             $this->create_date
    //         ]
    //     );

    //     if ( $save ) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    // public function delete()
    // {
    //     $conn = DB::connect();
    //     $delete = Sqlsrv::delete(
    //         $conn,
    //         "DELETE FROM Bom
    //         WHERE barcode = ?",
    //         [
    //             $this->barcode
    //         ]
    //     );

    //     if ($delete) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    // public function isBarcodeBomExist($barcode)
    // {
    //     $conn = DB::connect();
    //     return Sqlsrv::hasRows(
    //         $conn,
    //         "SELECT barcode 
    //         FROM Bom
    //         WHERE barcode = ?",
    //         [
    //             $barcode
    //         ]
    //     );
    // }

    // public function saveBom($item_id, $barcode)
    // {
    //     $conn = DB::connect();

    //     // if ((new IS)->isItemSetExists($item_id) === false) {
    //     //     return "Item Set ไม่มีอยู่ในระบบ";
    //     // }
        
    //     $item_id_by_item_set = (new IS)->getItemIdV2($item_id);

    //     // if ((new B)->inRange($barcode) === false) {
    //     //     return "Barcode ไม่ถูกต้อง";
    //     // }

    //     // if ((new IT)->isBarcodeExist($barcode) === false) {
    //     //     return "ไม่พบ Barcode";
    //     // }

    //     $barcode_detail = (new IT)->getBarcodeDetailV2($barcode);

    //     // if ((new IT)->isStatusReceiveV2($barcode) === false) {
    //     //     return (new IT)->getBarcodeStatusV2($barcode);
    //     // }

    //     // if ((new IT)->isWarehouseReceiveDateNullV2($barcode)) {
    //     //     return "Barcode ยังไม่ได้รับเข้า Warehouse";
    //     // }

    //     // if (self::isBarcodeBomExist($barcode)) {
    //     //     return "Barcode Number มีการ BOM ไปแล้ว";
    //     // }

    //     // if ($item_id_by_item_set[0]['item_id'] !== $barcode_detail[0]['ItemID']) {
    //     //     return "Item ID ไม่ตรงกัน";
    //     // }

    //     if (sqlsrv_begin_transaction($conn) === false) {
    //         return "Transaction ล้มเหลว.";
    //     }

    //     $updateBomInventTable = sqlsrv_query(
    //         $conn,
    //         "UPDATE InventTable
    //         SET Unit = ?,
    //         ItemID = ?,
    //         RefItemId = ?,
    //         DisposalID = ?,
    //         WarehouseID = ?,
    //         LocationID = ?,
    //         UpdateBy = ?,
    //         UpdateDate = ?
    //         WHERE Barcode = ?",
    //         [
    //             2,
    //            $item_id,
    //            $barcode_detail[0]["ItemID"],
    //            6, // finish goof
    //            $barcode_detail[0]["WarehouseID"],
    //            $barcode_detail[0]["LocationID"],
    //            $_SESSION["user_login"],
    //            Date('Y-m-d H:i:s'),
    //            $barcode
    //         ]
    //     );

    //     if (!$updateBomInventTable) {
    //         sqlsrv_rollback($conn);
            
    //         return "Update invent table failed.";
    //     }

    //     $create_inventtrans_moveout = sqlsrv_query(
    //         $conn,
    //         "INSERT INTO InventTrans(
    //             TransID,
    //             Barcode,
    //             CodeID,
    //             Batch,
    //             DisposalID,
    //             WarehouseID,
    //             LocationID,
    //             QTY,
    //             UnitID,
    //             DocumentTypeID,
    //             Company,
    //             CreateBy,
    //             CreateDate,
    //             Shift
    //         ) VALUES(
    //             ?, ?, ?, ?, ?,
    //             ?, ?, ?, ?, ?,
    //             ?, ?, ?, ?
    //         )",
    //         [
    //             (new U)->genTransId($barcode) . 1,
    //             $barcode,
    //             $barcode_detail[0]["ItemID"],
    //             $barcode_detail[0]["Batch"],
    //             6,  // fg
    //             3, // warehouse
    //             7, // location
    //             -1, // qty
    //             1, // unit
    //             2, // doc type
    //             $_SESSION["user_company"],
    //             $_SESSION["user_login"],
    //             date('Y-m-d H:i:s'),
    //             $_SESSION["Shift"]
    //         ]
    //     );

    //     if (!$create_inventtrans_moveout) {
    //         sqlsrv_rollback($conn);
    //         return "Update invent trans move out Failed.";
    //     }

    //     $create_inventtrans_movein = sqlsrv_query(
    //         $conn,
    //         "INSERT INTO InventTrans(
    //             TransID,
    //             Barcode,
    //             CodeID,
    //             Batch,
    //             DisposalID,
    //             WarehouseID,
    //             LocationID,
    //             QTY,
    //             UnitID,
    //             DocumentTypeID,
    //             Company,
    //             CreateBy,
    //             CreateDate,
    //             Shift
    //         ) VALUES(
    //             ?, ?, ?, ?, ?,
    //             ?, ?, ?, ?, ?,
    //             ?, ?, ?, ?
    //         )",
    //         [
    //             (new U)->genTransId($barcode) . 2,
    //             $barcode,
    //             $item_id,
    //             $barcode_detail[0]["Batch"],
    //             6,  // fg
    //             3, // warehouse
    //             7, // location
    //             1, // qty
    //             2, // unit
    //             1, // doc type
    //             $_SESSION["user_company"],
    //             $_SESSION["user_login"],
    //             date('Y-m-d H:i:s'),
    //             $_SESSION["Shift"]
    //         ]
    //     );

    //     if (!$create_inventtrans_movein) {
    //         sqlsrv_rollback($conn);
    //         // sqlsrv_free_stmt($create_inventtrans_movein);
    //         return "Update invent trans move out Failed.";
    //     }

    //     $stmt = sqlsrv_query(
    //         $conn,
    //         "UPDATE Onhand 
    //         SET QTY += ?
    //         WHERE CodeID = ?
    //         AND WarehouseID = ?
    //         AND LocationID = ?
    //         AND Batch = ?
    //         AND Company = ?",
    //         [
    //             -1,
    //             $barcode_detail[0]["ItemID"],
    //             $barcode_detail[0]["WarehouseID"],
    //             $barcode_detail[0]["LocationID"],
    //             $barcode_detail[0]["Batch"],
    //             $barcode_detail[0]["Company"]
    //         ]
    //     );

    //     if (!$stmt) {
    //         sqlsrv_rollback($conn);
    //         return "Update onhand failed.";
    //     }

    //     $isItemSetExists = sqlsrv_has_rows(sqlsrv_query(
    //         $conn,
    //         "SELECT CodeID 
    //         FROM Onhand
    //         WHERE WarehouseID = ?
    //         AND LocationID = ?
    //         AND Batch = ?
    //         AND Company = ?
    //         AND CodeID  = ?
    //         AND QTY >= 0",
    //         [
    //             $barcode_detail[0]["WarehouseID"],
    //             $barcode_detail[0]["LocationID"],
    //             $barcode_detail[0]["Batch"],
    //             $barcode_detail[0]["Company"],
    //             $item_id
    //         ]
    //     ));

    //     if ($isItemSetExists === true) {
           
    //         $stmt = sqlsrv_query(
    //             $conn,
    //             "UPDATE Onhand 
    //             SET QTY += ?
    //             WHERE CodeID = ?
    //             AND WarehouseID = ?
    //             AND LocationID = ?
    //             AND Batch = ?
    //             AND Company = ?",
    //             [
    //                 1,
    //                 $item_id,
    //                 $barcode_detail[0]["WarehouseID"],
    //                 $barcode_detail[0]["LocationID"],
    //                 $barcode_detail[0]["Batch"],
    //                 $barcode_detail[0]["Company"]
    //             ]
    //         );

    //         if (!$stmt) {
    //             sqlsrv_rollback($conn);
                
    //             return "Update onhand failed.";
    //         }
    //     } else {

    //         $stmt = sqlsrv_query(
    //             $conn,
    //             "INSERT INTO Onhand(
    //                 CodeID,
    //                 WarehouseID,
    //                 LocationID,
    //                 Batch,
    //                 QTY,
    //                 Company
    //             ) VALUES(?, ?, ?, ?, ?, ?)",
    //             [
    //                 $item_id,
    //                 $barcode_detail[0]["WarehouseID"],
    //                 $barcode_detail[0]["LocationID"],
    //                 $barcode_detail[0]["Batch"],
    //                 1,
    //                 $barcode_detail[0]["Company"]
    //             ]
    //         );

    //         if (!$stmt) {
    //             sqlsrv_rollback($conn);
                
    //             return "Update onhand failed.";
    //         }
    //     }

    //     $stmt = Sqlsrv::insert(
    //         $conn,
    //         "INSERT INTO Bom(
    //             barcode,
    //             item_id,
    //             item_set_id,
    //             create_by,
    //             create_date
    //         ) VALUES (?, ?, ?, ?, ?)",
    //         [
    //             $barcode,
    //             $barcode_detail[0]["ItemID"],
    //             $item_id,
    //             $_SESSION["user_login"],
    //             date("Y-m-d H:i:s")
    //         ]
    //     );

    //     if ($stmt) {
    //         sqlsrv_commit($conn);
    //         return true;
    //     } else {
    //         sqlsrv_rollback($conn);
    //         return "Bom ไม่สำเร็จ";
    //     }
    // }
}