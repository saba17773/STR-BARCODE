<?php

namespace App\V2\Barcode;

use App\V2\Database\Connector;
use Wattanar\Sqlsrv;

class BarcodeAPI
{
  private $db = null;

  public function __construct()
  {
    $this->db = Connector::getInstance("str_barcode");
  }

  public function isBarcodePrinted($barcode)
  {
    $barcode = substr($barcode, 1);

    if ((int) $barcode === 0) {
      return false;
    }

    return sqlsrv_has_rows(sqlsrv_query(
      $this->db,
      "SELECT TOP 1 * FROM BarcodePrinting
      WHERE CONVERT(nvarchar, SUBSTRING(StartBarcode, 2, 11)) <= ?
      AND CONVERT(nvarchar, SUBSTRING(FinishBarcode, 2, 11)) >= ?",
      [
        $barcode,
        $barcode
      ]
    ));
  }

  public function barcodeInfo($barcode)
  {
    return Sqlsrv::queryArray(
      $this->db,
      "SELECT * FROM InventTable
      WHERE Barcode = ?",
      [
        $barcode
      ]
    );
  }

  public function isBarcodeRegistered($barcode)
  {
    return sqlsrv_has_rows(sqlsrv_query(
      $this->db,
      "SELECT Barcode FROM InventTable
      WHERE Barcode = ?",
      [
        $barcode
      ]
    ));
  }

  public function getCureCode($barcode)
  {
    $curecode = Sqlsrv::queryArray(
      $this->db,
      "SELECT CuringCode FROM InventTable
      WHERE Barcode = ?",
      [
        $barcode
      ]
    );

    if (count($curecode) === 0) {
      return '';
    } else {
      return $curecode[0]['CuringCode'];
    }
  }

  public function getBatch($barcode)
  {
    $curecode = Sqlsrv::queryArray(
      $this->db,
      "SELECT Batch FROM InventTable
      WHERE Barcode = ?",
      [
        $barcode
      ]
    );

    if (count($curecode) === 0) {
      return '';
    } else {
      return $curecode[0]['Batch'];
    }
  }
  public function getGtCode($barcode) {
    $db = new Connector;
    $conn = $db->dbConnect();

    $gtcode = Sqlsrv::queryArray(
      $conn,
      "SELECT GT_Code FROM InventTable
      WHERE Barcode = ?",
      [
        $barcode
      ]
    );

    if (count($gtcode) === 0) {
      return '';
    } else {
      return $gtcode[0]['GT_Code'];
    }
  }

  public function getPressNo($barcode)
  {
    $curecode = Sqlsrv::queryArray(
      $this->db,
      "SELECT PressNo FROM InventTable
      WHERE Barcode = ?",
      [
        $barcode
      ]
    );

    if (count($curecode) === 0) {
      return '';
    } else {
      return $curecode[0]['PressNo'];
    }
  }

  public function getPressSide($barcode)
  {
    $curecode = Sqlsrv::queryArray(
      $this->db,
      "SELECT PressSide FROM InventTable
      WHERE Barcode = ?",
      [
        $barcode
      ]
    );

    if (count($curecode) === 0) {
      return '';
    } else {
      return $curecode[0]['PressSide'];
    }
  }

}
