<?php

namespace App\WarehouseLocation;

use App\WarehouseLocation\WarehouseLocationAPI as WHLAPI;
use App\V2\Barcode\BarcodeAPI;
use App\V2\Inventory\InventoryAPI;

class WarehouseLocationController
{

  public function __construct() {
    if (!isset($_SESSION['user_login'])) {
      header("Location: /");
      return;
    }
  }

  public function palletMaster() {
    renderView('wh_location/pallet_master');
  }

  public function palletReceive() {
    renderView('wh_location/pallet_receive');
  }

  public function palletTransfer() {
    renderView('wh_location/pallet_transfer');
  }

  public function transferLocation() {
    renderView('wh_location/transfer_location');
  }

  public function palletTable() {
    renderView('wh_location/pallet_table');
  }

  public function getPalletSeq() {
    echo (new WHLAPI)->getPallettSeq();
  }

  public function createPallet() {
    $qty = $_POST['qty'];

    $result = (new WHLAPI)->createPallet($qty);

    echo json_encode($result);
  }

  public function getAllPallet() {
    $result = (new WHLAPI)->getAllPallet();

    echo json_encode($result);
  }

  public function pdfPallet($pallet) {
    echo $pallet;
  }

  public function palletReceiveSave() {
    $pallet_no = $_POST['pallet_no'];
    $barcode = $_POST['barcode'];

    if ((new WHLAPI)->isPalletExists($pallet_no) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Pallet not found.'
      ]);
    }

    if ((new BarcodeAPI)->isBarcodePrinted($barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode not found.'
      ]);
    }

    if ((new InventoryAPI)->isReceive($barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode not found.'
      ]);
    }

    if ((new InventoryAPI)->isWHReceiveDateIsNull($barcode) === true) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode ยังไม่ได้รับเข้าคลัง'
      ]);
    }

    if ((new WHLAPI)->isInventoryHasPalletNo($pallet_no, $barcode) === true) {
      return json_encode([
        'result' => false,
        'message' => 'Pallet No. มีข้อมูลใน Invent Table'
      ]);
    }

    $res = (new WHLAPI)->receiveLocation($pallet_no, $barcode);

    return json_encode($res);
  }

  public function getPalletTable() {
    $data = (new WHLAPI)->getPalletTable();
    return json_encode($data);
  }

  public function getPalletLine($pallet_no) {
    $data = (new WHLAPI)->getPalletLine($pallet_no);
    return json_encode($data);
  }

  public function palletComplete() {
    $pallet_no = $_POST['pallet_no'];
    $data = (new WHLAPI)->palletComplete($pallet_no);
    return json_encode($data);
  }

  public function savePalletTransfer() {
    $pallet_no = $_POST['pallet_no'];
    $barcode = $_POST['barcode'];

    if ((new WHLAPI)->isPalletExists($pallet_no) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Pallet not found.'
      ]);
    }

    if ((new BarcodeAPI)->isBarcodePrinted($barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode not found.'
      ]);
    }

    if ((new WHLAPI)->isRemainZero($pallet_no) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Remain = 0'
      ]);
    }

    $data = (new WHLAPI)->savePalletTransfer($pallet_no, $barcode);
    return json_encode($data);
  }

  public function saveTransferLocation() {
    $pallet_no = $_POST['pallet_no'];
    $location = $_POST['location'];

    if ((new WHLAPI)->isPalletExists($pallet_no) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Pallet not found.'
      ]);
    }

    $data = (new WHLAPI)->saveTransferLocation($pallet_no, $location);
    return json_encode($data);
  }

  public function printTag($pallet_no) {

    $palletInfo = (new WHLAPI)->getPalletInfo($pallet_no);

    if ($palletInfo === false) {
      exit("pallet no. not found!");
    }

    $time = date('H', strtotime($palletInfo['create_date']));

    if ((int)$time >= 8 && (int)$time <= 20) {
      $shift = 'A';
    } else {
      $shift = 'B';
    }

    $location = (new WHLAPI)->getLocationInfo($palletInfo['location_id']);

    if ($location === false) {
      exit("pallet no. not found!");
    }

    $warehouse = (new WHLAPI)->getWarehouseNameFromLocation($location['ID']);

    $item = (new WHLAPI)->getItemInfo($palletInfo['item_id']);

    return renderView('wh_location/print_tag', [
      'pallet_no' => $palletInfo['pallet_no'],
      'item_id' => $palletInfo['item_id'],
      'item_name' => $item['NameTH'],
      'batch' => $palletInfo['batch_no'],
      'shift' => $shift,
      'receive' => $palletInfo['complete_date'],
      'location' => $location['Description'],
      'wh_name' => $warehouse['Description'],
      'qty' => $palletInfo['qty_in_use'],
      'qty_per_pallet' => $palletInfo['qty_per_pallet']
    ]);
  }

  public function updateLocation() {
    $location = $_POST['location'];
    $location_temp = $_POST['location_temp'];
    $pallet_no = $_POST['pallet_no'];

    $updateLocation = (new WHLAPI)->updateLocation($location, $location_temp, $pallet_no);
    return json_encode($updateLocation);
  }
  
  public function printPallet($pallet_no) {
    return renderView('wh_location/print_pallet', [
      'pallet_no' => $pallet_no
    ]);
  }
}