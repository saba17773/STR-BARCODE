<?php

namespace App\Libs;

use App\Components\Database as DB;
use Wattanar\Sqlsrv;

class InventTable
{
  public function isBarcodeInRange($barcode)
  {
    $conn = DB::connect();

    $barcode = substr($barcode, 1);

    if (!is_numeric($barcode)) {
      return false;
    }

    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT TOP 1 * FROM BarcodePrinting
        WHERE CONVERT(nvarchar, SUBSTRING(StartBarcode, 2, 11)) <= ?
        AND CONVERT(nvarchar, SUBSTRING(FinishBarcode, 2, 11)) >= ?",
      [
        $barcode,
        $barcode
      ]
    ));
  }

  public function getBarcodeDetail($barcode)
  {
    $conn = DB::connect();
    return Sqlsrv::queryArray(
      $conn,
      "SELECT * FROM InventTable
      WHERE Barcode = ?",
      [
        $barcode
      ]
    );
  }

  public function isBarcodeExist($barcode)
  {
    $conn = DB::connect();
    return Sqlsrv::hasRows(
      $conn,
      "SELECT Barcode
      FROM InventTable
      WHERE Barcode = ?",
      [
        $barcode
      ]
    );
  }

  public function isCuring($barcode)
  {
    $conn = DB::connect();
    return Sqlsrv::hasRows(
      $conn,
      'SELECT Barcode
          FROM InventTable
          WHERE CuringDate is not null
          AND Barcode = ?',
      [
        $barcode
      ]
    );
  }

  public function isCuringCode($barcode)
  {
    $conn = DB::connect();
    return Sqlsrv::hasRows(
      $conn,
      'SELECT Barcode
        FROM InventTable
        WHERE CuringCode is not null
        AND Barcode = ?',
      [
        $barcode
      ]
    );
  }

  public function isRefItemExists($barcode)
  {
    $conn = DB::connect();
    return Sqlsrv::hasRows(
      $conn,
      'SELECT Barcode
      FROM InventTable
      WHERE RefItemId is not null
      AND Barcode = ?',
      [
        $barcode
      ]
    );
  }
}
