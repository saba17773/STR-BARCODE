<?php

namespace App\V2\Inventory;

use App\V2\Database\Connector;
use Wattanar\Sqlsrv;

class InventoryAPI
{
  public function isBarcodeExists($barcode)
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT Barcode FROM InventTable
      WHERE Barcode = ?",
      [
        $barcode
      ]
    ));
  }

  public function isReceive($barcode)
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT Status FROM InventTable
      WHERE Barcode = ? AND  [Status] = 1", // Receive
      [
        $barcode
      ]
    ));
  }

  public function isWHReceiveDateIsNull($barcode)
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT WarehouseReceiveDate FROM InventTable
      WHERE Barcode = ? AND  WarehouseReceiveDate IS NULL", // Receive
      [
        $barcode
      ]
    ));
  }

  public function isLPNExists($LPN, $barcode)
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT LPNID FROM InventTable
      WHERE LPNID = ? AND Barcode = ?",
      [
        $LPN,
        $barcode
      ]
    ));
  }

  public function mapLPN($item, $batch)
  {
    $conn = (new Connector)->dbConnect();
    return \sqlsrv_has_rows(\sqlsrv_query(
      $conn,
      "SELECT L.ItemID, L.BatchNo FROM InventTable IT 
      LEFT JOIN LPNMaster L ON L.ItemID = IT.ItemID 
      AND L.BatchNo = IT.Batch
      WHERE L.ItemID = ? AND L.BatchNo = ?",
      [
        $item,
        $batch
      ]
    ));
  }

  public function getBarcodeInfo($barcode)
  {
    $conn = (new Connector)->dbConnect();
    $rows = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 * FROM InventTable 
      WHERE Barcode = ?",
      [
        $barcode
      ]
    );

    if (count($rows) !== 0 ) {
      return $rows;
    }
    return [['']];
  }

  public function genTransId($barcode)
  {
    return $barcode . substr(date('YmdHis'), 2).microtime(true) * 10000;
  }

  public function isFinalReceiveDate($barcode) {
    $conn = (new Connector)->dbConnect();

    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT Barcode FROM InventTable 
      WHERE FinalReceiveDate is not null
      AND Barcode = ?",
      [
        $barcode
      ]
    ));
  }

  public function isStillOnFinal($barcode) {
    $conn = (new Connector)->dbConnect();

    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT Barcode FROM InventTable 
      WHERE  WarehouseReceiveDate is null 
      AND WarehouseTransReceiveDate is null 
      AND Barcode = ?",
      [
        $barcode
      ]
    ));
  }

  public function isInWarehouseFG($barcode) {
    $conn = (new Connector)->dbConnect();

    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT Barcode FROM InventTable 
      WHERE  WarehouseReceiveDate is not null
      AND Barcode = ?",
      [
        $barcode
      ]
    ));
  }
}