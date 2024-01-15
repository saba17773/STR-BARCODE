<?php

namespace App\WarehouseCounting;

use App\V2\Database\Connector;
use Respect\Validation\Rules\Type;
use Wattanar\Sqlsrv;

class WarehouseCountingAPI
{
  public function __construct()
  {
  }

  public function getItem($item = null)
  {
    $conn = Connector::getInstance("str_barcode");

    if ($item !== null) {

      $qBatch = "";
      $qDate = "";

      $useBatch = $this->getMinimumBatch();
      if ($useBatch !== "") {

        if (strlen($useBatch) > 7) { // default is 2020-01
          $useBatch = substr($useBatch, 0, 7);
        }

        $qBatch = " AND IT.Batch < '" . $useBatch . "' ";
      }

      $useDate = $this->getMinimumDate();
      if ($useDate !== "") {
        $qDate = " AND IT.WarehouseReceiveDate < '" . $useDate . "' ";
      }

      $sql =  "SELECT COUNT(*) AS Remain
      from InventTable IT 
      left join WarehouseCounting W ON W.Barcode = IT.Barcode
      Where IT.WarehouseID = 3 
      AND IT.Status = 1 -- receive
      AND IT.ItemID = ? $qBatch $qDate
      and W.Barcode is null";

      $remain = Sqlsrv::queryArray(
        $conn,
        $sql,
        [
          $item
        ]
      );

      return [
        "remain" => $remain[0]["Remain"]
      ];
    } else {
      return Sqlsrv::queryArray(
        $conn,
        "SELECT IT.ItemID 
        from InventTable IT 
        Where IT.WarehouseID = 3 
        AND IT.Status = 1 -- receive
        group by IT.ItemID 
        order by IT.ITemID asc"
      );
    }
  }

  public function save($barcode)
  {
    $conn = Connector::getInstance("str_barcode");

    $q = sqlsrv_query(
      $conn,
      "INSERT INTO WarehouseCounting (Barcode, CreateBy, CreateDate)
      VALUES(?, ?, ?)",
      [
        $barcode,
        $_SESSION["user_login"],
        date("Y-m-d H:i:s")
      ]
    );

    if (!$q) {
      return [
        "result" => false,
        "color" => "red",
        "message" => var_dump(sqlsrv_errors())
      ];
    } else {
      return [
        "result" => true,
        "color" => "green",
        "message" => "บันทึกข้อมูลสำเร็จ"
      ];
    }
  }

  public function isAlreadyAdded($barcode)
  {
    $conn = Connector::getInstance("str_barcode");

    $c = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT Barcode 
      FROM WarehouseCounting
      WHERE Barcode = ?",
      [
        $barcode
      ]
    ));

    return $c;
  }

  public function getBarcodeData($barcode)
  {
    $conn = Connector::getInstance("str_barcode");

    $data = Sqlsrv::queryArray(
      $conn,
      "SELECT 
      T.Barcode,
      T.WarehouseReceiveDate,
      T.ItemID,
      I.NameTH,
      I.Brand,
      T.Batch,
      T.Status
      FROM InventTable T
      LEFT JOIN ItemMaster I ON I.ID = T.ItemID
      WHERE T.WarehouseID = 3 
      AND T.Status = 1 -- receive
      AND T.Barcode = ?",
      [
        $barcode
      ]
    );

    return $data;
  }

  public function saveSetup(
    $batch,
    $date,
    $use_batch,
    $use_date
  ) {
    $conn = Connector::getInstance("str_barcode");

    $updateUseDate = sqlsrv_query(
      $conn,
      "UPDATE WarehouseCountingSetup
      SET MetaValue = ?
      WHERE MetaKey = ?",
      [
        $use_date,
        "use_date"
      ]
    );

    if ((int) $use_date === 1) {
      $updateMinimumDate = sqlsrv_query(
        $conn,
        "UPDATE WarehouseCountingSetup
        SET MetaValue = ?
        WHERE MetaKey = ?",
        [
          $date,
          "minimum_date"
        ]
      );
    }

    $updateUseBatch = sqlsrv_query(
      $conn,
      "UPDATE WarehouseCountingSetup
      SET MetaValue = ?
      WHERE MetaKey = ?",
      [
        $use_batch,
        "use_batch"
      ]
    );

    if ((int) $use_batch === 1) {
      $updateMinimumBatch = sqlsrv_query(
        $conn,
        "UPDATE WarehouseCountingSetup
        SET MetaValue = ?
        WHERE MetaKey = ?",
        [
          $batch,
          "minimum_batch"
        ]
      );
    }

    return [
      "result" => true,
      "message" => "Update Success"
    ];
  }

  public function getMinimumBatch()
  {
    $conn = Connector::getInstance("str_barcode");

    $isUseBatch = \sqlsrv_has_rows(\sqlsrv_query(
      $conn,
      "SELECT * 
      FROM WarehouseCountingSetup
      WHERE MetaKey = 'use_batch'
      AND MetaValue = 1"
    ));

    if ($isUseBatch) {
      $minimumBatch = Sqlsrv::queryArray(
        $conn,
        "SELECT MetaValue 
        FROM WarehouseCountingSetup
        WHERE MetaKey = 'minimum_batch'"
      );

      if (count($minimumBatch) > 0) {
        return $minimumBatch[0]["MetaValue"];
      } else {
        return "";
      }
    } else {
      return "";
    }
  }

  public function getMinimumDate()
  {
    $conn = Connector::getInstance("str_barcode");

    $isUseDate = \sqlsrv_has_rows(\sqlsrv_query(
      $conn,
      "SELECT * 
      FROM WarehouseCountingSetup
      WHERE MetaKey = 'use_date'
      AND MetaValue = 1"
    ));

    if ($isUseDate) {
      $minimumDate = Sqlsrv::queryArray(
        $conn,
        "SELECT MetaValue 
        FROM WarehouseCountingSetup
        WHERE MetaKey = 'minimum_date'"
      );

      if (count($minimumDate) > 0) {
        return $minimumDate[0]["MetaValue"];
      } else {
        return "";
      }
    } else {
      return "";
    }
  }

  public function getOption($key)
  {
    $conn = Connector::getInstance("str_barcode");

    $value = Sqlsrv::queryArray(
      $conn,
      "SELECT MetaValue
      FROM WarehouseCountingSetup
      WHERE MetaKey = '$key'"
    );

    return $value[0]["MetaValue"];
  }

  public function getOnhandDiff($year,$type)
  {
    $conn = Connector::getInstance("str_barcode");

    $sql = "SELECT 
      T.ItemID,
      IM.NameTH,
      IM.Brand,
      T.Batch,
      IM.ProductGroup,
      COUNT(T.Barcode) AS QTY,
      COUNT(WC.Barcode) AS Counted
      FROM InventTable T
      LEFT JOIN ItemMaster IM ON IM.ID = T.ItemID
      LEFT JOIN WarehouseCounting WC ON WC.Barcode = T.Barcode
      WHERE T.WarehouseID = 3
      AND T.Status = 1 -- receive
      AND T.Batch LIKE '%$year%'
      AND IM.ProductGroup = '$type'
      GROUP BY 
      T.ItemID,
      T.Batch,
      IM.Brand,
      IM.ProductGroup,
      IM.NameTH
      ORDER BY IM.Brand, T.ItemID, T.Batch ASC";

    return Sqlsrv::queryArray(
      $conn,
      $sql
    );
  }

  public function getReportCounting($countingFromDate, $countingToDate, $brand, $checkRadio)
  {
    $conn = Connector::getInstance("str_barcode");
    if($checkRadio == "summary"){
    $sql = "SELECT 
    CONVERT(date, C.CreateDate) AS CountingDate,
    T.ItemID,
    IM.NameTH,
    IM.Brand,
    T.Batch,
    COUNT(T.Barcode) AS QTY
    FROM WarehouseCounting C
    LEFT JOIN InventTable T ON T.Barcode = C.Barcode
    LEFT JOIN ItemMaster IM ON IM.ID = T.ItemID
    LEFT JOIN BrandMaster BM ON BM.BrandName = IM.Brand
    WHERE CONVERT(date, C.CreateDate) >= '$countingFromDate'
    AND CONVERT(date, C.CreateDate) <= '$countingToDate'
    AND BM.BrandID IN ($brand)
    AND T.Status = 1
    GROUP BY 
    T.ItemID,
    IM.NameTH,
    IM.Brand,
    T.Batch,
    CONVERT(date, C.CreateDate)
    order by CONVERT(date, C.CreateDate), IM.Brand, T.ItemID, T.Batch ASC";
    }
    else{
      $sql = "SELECT 
      CONVERT(date, C.CreateDate) AS CountingDate,
      T.ItemID,
      IM.NameTH,
      IM.Brand,
      T.Batch,
      C.Barcode      
      FROM WarehouseCounting C
      LEFT JOIN InventTable T ON T.Barcode = C.Barcode
      LEFT JOIN ItemMaster IM ON IM.ID = T.ItemID
      LEFT JOIN BrandMaster BM ON BM.BrandName = IM.Brand
      WHERE CONVERT(date, C.CreateDate) >= '$countingFromDate'
      AND CONVERT(date, C.CreateDate) <= '$countingToDate'
      AND BM.BrandID IN ($brand)
      AND T.Status = 1      
      order by CONVERT(date, C.CreateDate), IM.Brand, T.ItemID, T.Batch ASC";
    }
    return Sqlsrv::queryArray(
      $conn,
      $sql
    );
  }

  public function removeBarcode($userId) {
    $conn = Connector::getInstance("str_barcode");
    $q = sqlsrv_query($conn, "EXEC RemoveWarehouseCountingLog $userId, 'remove all barcode except 3 items', 'warehouse_counting_remove_old_barcode'");
    if ($q) {
      return ["result" => true, "message" => "Remove success"];
    } else {
      return ["result" => false, "message" => "Remove failed"];
    }
  }
}
