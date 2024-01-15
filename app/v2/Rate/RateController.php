<?php

namespace App\V2\Rate;

use App\Common\Response;
use App\V2\Rate\RateAPI;

class RateController
{
  private $response = null;
  private $rateApi = null;

  public function __construct()
  {
    $this->response = new Response();
    $this->rateApi = new RateAPI();
  }

  public function index()
  {
    renderView("page/except_rate");
  }

  public function saveExceptBarcode()
  {
    try {
      $barcode = $_POST["barcode"];

      if (!isset($barcode)) throw new \Exception("ไม่พบ Barcode");
      if (trim($barcode) === null || trim($barcode) === "") throw new \Exception("Barcode ไม่ถูกต้อง");

      $result = $this->rateApi->saveExceptBarcode($barcode);
      return json_encode([
        "result" => $result["result"],
        "message" => $result["message"] . " (" . date("Y-m-d H:i") . ")"
      ]);
    } catch (\Exception $e) {
      return json_encode($this->response->array(false, $e->getMessage()));
    }
  }
}
