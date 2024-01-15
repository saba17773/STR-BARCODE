<?php

namespace App\V2\Rate;

use App\Common\Response;
use Wattanar\SqlsrvHelper;
use App\V2\Database\Connector;
use App\V2\Barcode\BarcodeAPI;

class RateAPI
{
  private $response = null;
  private $sqlsrvHelper = null;
  private $db = null;
  private $barcodeApi = null;

  public function __construct()
  {
    $this->response = new Response();
    $this->sqlsrvHelper = new SqlsrvHelper();
    $this->db = Connector::getInstance("str_barcode");
    $this->barcodeApi = new BarcodeAPI();
  }

  public function saveExceptBarcode($barcode)
  {
    try {
      $isBarcodeRegistered = $this->barcodeApi->isBarcodeRegistered($barcode);
      if ($isBarcodeRegistered === false) {
        throw new \Exception("ไม่พบ Barcode ในระบบ");
      }

      $isBarcodeExists = self::checkBarcodeExists($barcode);
      if ($isBarcodeExists === true) {
        $remove = self::removeBarcode($barcode);
        if ($remove["result"] === true) {
          return $this->response->array(true, $remove["message"]);
        } else {
          return $this->response->array(false, $remove["message"]);
        }
      } else {
        $add = self::addBarcode($barcode);
        if ($add["result"] === true) {
          return $this->response->array(true, $add["message"]);
        } else {
          return $this->response->array(false, $add["message"]);
        }
      }
    } catch (\Exception $e) {
      return $this->response->array(false, $e->getMessage());
    }
  }

  public function removeBarcode($barcode)
  {
    try {
      // code
      $data = \sqlsrv_query(
        $this->db,
        "DELETE FROM ExceptBuildRate
        WHERE Barcode = ?",
        [
          $barcode
        ]
      );

      if ($data) {
        return $this->response->array(true, "ลบสำเร็จ " . $barcode . " จะถูกนำไปคิดค่าเรท");
      } else {
        return $this->response->array(false, "ลบ " . $barcode . " ไม่สำเร็จ");
      }
    } catch (\Exception $e) {
      return $this->response->array(false, $e->getMessage());
    }
  }

  public function addBarcode($barcode)
  {
    try {
      // code
      $data = \sqlsrv_query(
        $this->db,
        "INSERT INTO ExceptBuildRate(Barcode, CreateBy, CreateDate)
        VALUES(?, ?, ?)",
        [
          $barcode,
          $_SESSION["user_login"],
          date("Y-m-d H:i:s")
        ]
      );

      if ($data) {
        return $this->response->array(true, "เพิ่มสำเร็จ " . $barcode . " จะไม่ถูกนำไปคิดค่าเรท");
      } else {
        return $this->response->array(false, "เพิ่มไม่สำเร็จ");
      }

      return $data;
    } catch (\Exception $e) {
      // return $e->getMessage();
      return $this->response->array(false, $e->getMessage());
    }
  }

  public function checkBarcodeExists($barcode)
  {
    try {
      // code
      $data = \sqlsrv_has_rows(\sqlsrv_query(
        $this->db,
        "SELECT Barcode
        FROM ExceptBuildRate
        WHERE Barcode = ?",
        [
          $barcode
        ]
      ));

      return $data;
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }
}
