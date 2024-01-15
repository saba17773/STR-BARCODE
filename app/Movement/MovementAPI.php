<?php

namespace App\Movement;

use App\V2\Database\Connector;
use Wattanar\SqlsrvHelper;

class MovementAPI
{
  public function __construct() {
    $this->conn = Connector::getInstance('str_barcode');
  }

  public function getItemAvailable($journal_id) {

    $type = self::getWarehouseLocationByJournal($journal_id);
    if ($type === null) return [];

    if ($type['JournalTypeID'] === 'MOV') { $WH = 2; }
    else if ($type['JournalTypeID'] === 'MOVWH'|| $type['JournalTypeID'] === 'MOVWHRTN') { $WH = 3; }
    else { $WH = 2; }


    $sql ="SELECT
      IT.ItemID AS ITEM,
      IM.NameTH as ITEM_NAME,
      SUM(IT.QTY) AS TOTAL
      from InventTable IT
      left join ItemMaster IM ON IM.ID = IT.ItemID
      where IT.WarehouseID = ?
      and IT.Status = 1
      group by IT.ItemID, IM.NameTH
      order by IT.ItemID ASC
      ";

    $rows = (new SqlsrvHelper)->getRows(sqlsrv_query(
      $this->conn,
      $sql,
      [
        $WH
      ]
    ));

    return $rows;
  }

  public function getBatchAvailable($journal_id, $item) {

    $type = self::getWarehouseLocationByJournal($journal_id);
    if ($type === null) return [];

    if ($type['JournalTypeID'] === 'MOV') { $WH = 2; }
    else if ($type['JournalTypeID'] === 'MOVWH' || $type['JournalTypeID'] === 'MOVWHRTN') { $WH = 3; }
    else { $WH = 2; }

    $sql ="SELECT
      IT.ItemID AS ITEM,
      IM.NameTH as ITEM_NAME,
      IT.Batch as BATCH,
      SUM(IT.QTY) AS TOTAL
      from InventTable IT
      left join ItemMaster IM ON IM.ID = IT.ItemID
      where IT.WarehouseID = ?
      and IT.Status = 1
      and IT.ItemID = ?
      group by IT.ItemID, IT.Batch, IM.NameTH
      order by IT.BATCH ASC
      ";

    $rows = (new SqlsrvHelper)->getRows(sqlsrv_query(
      $this->conn,
      $sql,
      [
        $WH,
        $item
      ]
    ));

    return $rows;
  }

  public function getWarehouseLocationByJournal($journal_id) {
    try {
      $row = (new SqlsrvHelper)->getRow(sqlsrv_query(
        $this->conn,
        "SELECT JournalTypeID
        FROM InventJournalTable
        WHERE InventJournalID = ?",
        [
          $journal_id
        ]
      ));

      return $row;
    } catch (\Exception $e) {
      return null;
    }
  }
  public function checkcounbatch($journal_id, $item,$batch) {

    $type = self::getWarehouseLocationByJournal($journal_id);
    if ($type === null) return [];

    if ($type['JournalTypeID'] === 'MOV') { $WH = 2; }
    else if ($type['JournalTypeID'] === 'MOVWH') { $WH = 3; }
    else { $WH = 2; }


    $row = (new SqlsrvHelper)->getRow(sqlsrv_query(
      $this->conn,
      "SELECT

        SUM(IT.QTY) AS TOTAL
        from InventTable IT
        left join ItemMaster IM ON IM.ID = IT.ItemID
        where IT.WarehouseID = ?
        and IT.Status = 1
        and IT.ItemID = ?
        and IT.Batch =?
        group by IT.ItemID, IT.Batch, IM.NameTH
        order by IT.BATCH ASC",
      [
        $WH,
        $item,
        $batch

      ]
        ));

      if (!$row) {
      				sqlsrv_rollback($conn);
      				return [
      					"status" => 404,
      					"message" => "Serial ไม่มีในระบบ"
      				];
      			}
      else {
        return [
					"status" => 200,
					"message" => $row["TOTAL"]
				];
      }
  }
}
