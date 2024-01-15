<?php

namespace App\WarehouseCounting;

use App\WarehouseCounting\WarehouseCountingAPI;

class WarehouseCountingController
{
  private $warehouseCountingAPI;

  public function __construct()
  {
    if (!isset($_SESSION['user_login'])) {
      header("Location: /");
      return;
    }

    $this->warehouseCountingAPI = new WarehouseCountingAPI();
  }

  public function index()
  {
    renderView("warehouse_counting/index");
  }

  public function getItem()
  {
    return json_encode($this->warehouseCountingAPI->getItem());
  }

  public function getRemainItem()
  {
    $item = $_POST["item"];
    return json_encode($this->warehouseCountingAPI->getItem($item));
  }

  public function save()
  {
    $barcode = $_POST["barcode"];

    if ($barcode === "" || $barcode === null) {
      return json_encode([
        "result" => false,
        "color" => "red",
        "message" => "Barcode ไม่ถูกต้อง"
      ]);
    }

    $barcodeData = $this->warehouseCountingAPI->getBarcodeData($barcode);

    if (count($barcodeData) === 0) {
      return json_encode([
        "result" => false,
        "color" => "red",
        "message" => "ไม่พบ Barcode"
      ]);
    }

    $isAdded = $this->warehouseCountingAPI->isAlreadyAdded($barcode);
    if ($isAdded === true) {
      return json_encode([
        "result" => false,
        "color" => "yellow",
        "message" => "Barcode เคยยิงไปแล้ว"
      ]);
    }

    $result = $this->warehouseCountingAPI->save($barcode);
    return json_encode($result);
  }

  public function getBarcodeData()
  {
    try {
      // code
      $barcode = $_POST["barcode"];
      $item = $_POST["item"];

      if ($barcode === "" || $barcode === null) {
        // throw new \Exception("Barcode ไม่ถูกต้อง");
        return json_encode([
          "color" => "red",
          "result" => false,
          "message" => "Barcode ไม่ถูกต้อง"
        ]);
      }

      $barcodeData = $this->warehouseCountingAPI->getBarcodeData($barcode);

      if (count($barcodeData) > 0) {
        if ($item !== $barcodeData[0]["ItemID"]) {
          // throw new \Exception("Item ไม่ถูกต้อง");
          return json_encode([
            "color" => "red",
            "result" => false,
            "message" => "Item ไม่ถูกต้อง"
          ]);
        }
      } else {
        // throw new \Exception("ไม่พบ Barcode");
        return json_encode([
          "color" => "red",
          "result" => false,
          "message" => "Barcode ไม่ถูกต้อง"
        ]);
      }

      $isAdded = $this->warehouseCountingAPI->isAlreadyAdded($barcode);
      if ($isAdded === true) {
        // throw new \Exception("Barcode เคยยิงไปแล้ว");
        return json_encode([
          "color" => "yellow",
          "result" => false,
          "message" => "Barcode เคยยิงไปแล้ว"
        ]);
      }

      return json_encode([
        "result" => true,
        "message" => "Get data success",
        "color" => "green",
        "data" => $barcodeData
      ]);
    } catch (\Exception $e) {
      return json_encode([
        "color" => "red",
        "result" => false,
        "message" => $e->getMessage()
      ]);
    }
  }

  public function reportOnhandDiff()
  {
    return renderView("warehouse_counting/report_onhand_diff");
  }

  public function reportOnhandExcel()
  {
    $onhand = $this->warehouseCountingAPI->getOnhandDiff();

    return renderView("warehouse_counting/report_onhand_excel", [
      "data" => $onhand
    ]);
  }

  public function reportOnhandPdf()
  {
    $onhand = $this->warehouseCountingAPI->getOnhandDiff();

    return renderView("warehouse_counting/report_onhand_pdf", [
      "data" => $onhand
    ]);
  }

  public function setup()
  {
    $option = [
      "use_date" => $this->warehouseCountingAPI->getOption("use_date"),
      "use_batch" => $this->warehouseCountingAPI->getOption("use_batch"),
      "minimum_date" => $this->warehouseCountingAPI->getOption("minimum_date"),
      "minimum_batch" => $this->warehouseCountingAPI->getOption("minimum_batch"),
    ];

    renderView("warehouse_counting/setup", ["option" => $option]);
  }

  public function saveSetup()
  {
    $batch = $_POST["minimum_batch"] === "" ? null : $_POST["minimum_batch"];
    $date = $_POST["minimum_date"] === "" ? null : $_POST["minimum_date"];
    $use_batch = isset($_POST["use_batch"]) ? $_POST["use_batch"] : 0;
    $use_date = isset($_POST["use_date"]) ? $_POST["use_date"] : 0;

    $save = $this->warehouseCountingAPI->saveSetup(
      $batch,
      $date,
      $use_batch,
      $use_date
    );

    return json_encode($save);
  }

  public function reportCounting()
  {
    return renderView("warehouse_counting/report_counting");
  }

  public function reportCountingExport()
  {
    $typeReport = $_POST["type_report"];
    $countingFromDate = $_POST["counting_from_date"];
    $countingToDate = $_POST["counting_to_date"];
    $checkRadio = $_POST["item_group"];    

    if ($countingFromDate === "" || $countingToDate === "") {
      echo "<script type='text/javascript'>alert('กรุณาใส่วันที่');</script>";
      return renderView("warehouse_counting/report_counting");
    }
    if (isset($_POST['selectbrand'])) {
      $brand_select = $_POST['selectbrand'];
      $brand  = '';
      foreach ($brand_select as $v) {
        $brand .= $v . ', ';
      }
      $brand = trim($brand, ', ');       
    }
    if($brand == ""){
      echo "<script type='text/javascript'>alert('กรุณาเลือก Brand');</script>";
        return renderView("warehouse_counting/report_counting");
    }
    
    $onhand = $this->warehouseCountingAPI->getReportCounting($countingFromDate, $countingToDate, $brand, $checkRadio);
    if ($checkRadio == "summary"){
    if ($typeReport === "pdf" ) {
      return renderView("warehouse_counting/report_counting_pdf", [
        "data" => $onhand,
        "counting_from_date" => $countingFromDate,
        "counting_to_date" => $countingToDate,
      ]);
    } else if ($typeReport === "excel") {
      return renderView("warehouse_counting/report_counting_excel", [
        "data" => $onhand,
        "counting_from_date" => $countingFromDate,
        "counting_to_date" => $countingToDate,
      ]);
    }}else if ($checkRadio == "detail"){
      return renderView("warehouse_counting/report_warehouse_counting_excel", [
        "data" => $onhand,
        "counting_from_date" => $countingFromDate,
        "counting_to_date" => $countingToDate,
      ]);
    }
  }

  public function reportOnHand()
  {    
    $typeReport = $_POST["type_report"];
    $year = $_POST["year"];
    $type = $_POST["group"];      
    $onhand = $this->warehouseCountingAPI->getOnhandDiff($year, $type);
    // echo $year;
    // echo  $type;
    // echo "<pre>";
    // print_r($onhand);
    // echo "</pre>";exit();
    if ($typeReport === "pdf" ) {
      return renderView("warehouse_counting/report_onhand_pdf", [
        "data" => $onhand        
      ]);
    } else if ($typeReport === "excel") {
      return renderView("warehouse_counting/report_onhand_excel", [
        "data" => $onhand
      ]);
    }
  }

  public function removeBarcode() {
    if (count($_SESSION) === 0) {
      return [
        "result" => false,
        "message" => "please login"
      ];
    }

    $res = $this->warehouseCountingAPI->removeBarcode($_SESSION["user_login"]);
    return json_encode($res);
  }
}
