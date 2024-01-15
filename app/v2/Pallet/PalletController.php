<?php

namespace App\V2\Pallet;

use App\V2\Pallet\PalletAPI;
use App\V2\Barcode\BarcodeAPI;
use App\V2\Inventory\InventoryAPI;
use App\V2\Location\LocationAPI;
use Webmozart\Assert\Assert;

class PalletController
{
  public function renderLpnMaster()
  {
    renderView('page/lpn_master');
  }

  public function renderTransferLPN()
  {
    renderView("page/transfer_lpn");
  }

  public function renderTransferLocation() {
    renderView("page/transfer_location");
  }

  public function createManualLPN()
  {
    $item = $_POST['item'];
    $batch = $_POST['batch'];

    $res =  (new PalletAPI)->createManualLPN($item, $batch);

    if ($res === true)  {
      return json_encode([
        'result' => true,
        'message' => 'Create successful!'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $res
      ]);
    }
  }

  public function renderItemReceiveLocation()
  {
    renderView("page/item_receive_location");
  }
  
  public function getAllItemReceiveLocation()
  {
    echo (new PalletAPI)->getAllItemReceiveLocation($_GET['location_id']);
  }

  public function renderReceiveLocation()
  {
    renderView("page/receive_location");
  }

  public function createItemReceiveLocation()
  {

    $item_id = $_POST['item_id'];
    $location_id = $_POST['location_id'];
    $id = $_POST['id'];
    $type = $_POST['type'];

    $res = (new PalletAPI)->createItemReceiveLocation($id, $location_id, $item_id, $type);

    if ($res === true)  {
      return json_encode([
        'result' => true,
        'message' => $type . ' successful!'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $res
      ]);
    }
  }

  public function deleteItemReceiveLocation()
  {
    $id = $_POST['id'];

    $res = (new PalletAPI)->deleteItemReceiveLocation($id);

    if ($res === true)  {
      return json_encode([
        'result' => true,
        'message' => 'Delete successful!'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $res
      ]);
    }
  }

  public function getAllLPNMaster()
  {
    echo (new PalletAPI)->getAllLPNMaster();
  }

  public function generateAuto()
  {
    $res = (new PalletAPI)->generateAuto();

    if ($res === true)  {
      return json_encode([
        'result' => true,
        'message' => 'Generate successful!'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $res
      ]);
    }
  }

  public function receiveLocation()
  {
    $lpn = $_POST['lpn'];
    $barcode = $_POST['barcode'];

    if ((new PalletAPI)->isLPNExists($lpn) === false) {
      return json_encode([
        'result' => false,
        'message' => 'LPN not found.'
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

    if ((new InventoryAPI)->isLPNExists($lpn, $barcode) === true) {
      return json_encode([
        'result' => false,
        'message' => 'LPN มีข้อมูลใน Invent Table'
      ]);
    }

    if ((new PalletAPI)->isBarcodeCanReceiveLPN($lpn, $barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Batch และ item ไม่ตรง'
      ]);
    }

    $barcodeInfo = (new InventoryAPI)->getBarcodeInfo($barcode);

    if ((new InventoryAPI)->mapLPN($barcodeInfo[0]['ItemID'], $barcodeInfo[0]['Batch']) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Item and Batch not match.'
      ]);
    }

    $res = (new PalletAPI)->receiveLocation($lpn, $barcode);

    if ($res === true) {
      return json_encode([
        'result' => true,
        'message' => 'Update successful.',
        'location' => (new LocationAPI)->getLocationNameFromId((new PalletAPI)->getLocationFromLPN($lpn))
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $res,
        'location' => (new LocationAPI)->getLocationNameFromId((new PalletAPI)->getLocationFromLPN($lpn))
      ]);
    }
  }

  public function transferLPN()
  {
    if (empty($_POST['barcode']) || empty($_POST['lpn'])) {
      return json_encode([
        'result' => false,
        'message' => 'LPN or Barcode not found'
      ]);
    }

    $lpn = $_POST['lpn'];
    $barcode = $_POST['barcode'];

    if ( (new PalletAPI)->isRemainLPNZero($lpn) === 0 ) {
       return json_encode([
        'result' => false,
        'message' => 'LPN ปลายทาง remain = 0'
      ]);
    } 

    if((new PalletAPI)->verifyTransferLPN($lpn) === false) {
      return json_encode([
        'result' => false,
        'message' => ' LPN ไม่ถูกต้อง หรือปิดไปแล้ว'
      ]);
    }

    if ( (new InventoryAPI)->isBarcodeExists($barcode) === false ) {
       return json_encode([
        'result' => false,
        'message' => 'Barcode not found.'
      ]);
    }

    if ( (new PalletAPI)->isBarcodeAlreadyExistsInLPN($lpn, $barcode) === true ) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode is alreay exists on this LPN.'
      ]);
    }

    if ((new InventoryAPI)->isReceive($barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode not found.'
      ]);
    }

    $oldLPN = (new PalletAPI)->getLPNFromBarcode($barcode);

    if ( (new PalletAPI)->isLPNMatchedItemAndBatch($oldLPN, $lpn) === false ) {
      return json_encode([
        'result' => false,
        'message' => 'Item and batch not match.'
      ]);
    }

    $barcodeInfo = (new InventoryAPI)->getBarcodeInfo($barcode);

    if ((new InventoryAPI)->mapLPN($barcodeInfo[0]['ItemID'], $barcodeInfo[0]['Batch']) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Item and Batch not match.'
      ]);
    }

    $res = (new PalletAPI)->transferLPN($lpn, $barcode);

    if ($res === true) {

      return json_encode([
        'result' => true,
        'message' => 'Update successful.',
        'from_lpn' => (new PalletAPI)->getLPNFromBarcode($barcode)
      ]);
    } else {
      
      return json_encode([
        'result' => false,
        'message' => $res
      ]);
    }

  }

  public function updateItemReceiveLocationQTY()
  {
    $location = $_POST['location'];
    $qty = $_POST['qty'];

    $res = (new PalletAPI)->updateItemReceiveLocationQTY($location, $qty);

    if ($res === true) {
      return json_encode([
        'result' => true,
        'message' => 'Update successful.'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $res
      ]);
    }
  }

  public function completeReceiveLocation()
  {
    $lpn = $_POST['lpn'];
    $barcode = $_POST['barcode'];

    if ((new PalletAPI)->isLPNExists($lpn) === false) {
      return json_encode([
        'result' => false,
        'message' => 'LPN not found.'
      ]);
    }

    if ((new PalletAPI)->isLPNProcess($lpn) === false) {
      return json_encode([
        'result' => false,
        'message' => 'LPN isn\'t status processing.'
      ]);
    }

    if ((new PalletAPI)->isLPNProcess($lpn) === false) {
      return json_encode([
        'result' => false,
        'message' => 'LPN isn\'t status processing.'
      ]);
    }

    $lpnInfo = (new PalletAPI)->getLpnInfo($lpn);

    $isNewLPN = (new PalletAPI)->isNewItemOnLPN($lpnInfo[0]['ItemID'], $lpnInfo[0]['BatchNo']);

    if ( count($isNewLPN) !== 0 ) {
      
      $op1_location = (new PalletAPI)->getLocationByExistsLPN($lpnInfo[0]['ItemID'], $lpnInfo[0]['BatchNo']);
    
      if ( count($op1_location) !== 0 ) {

        if ( (new PalletAPI)->setLPNComplete($lpn, $op1_location[0]['LocationID']) === true ) {
          
          (new PalletAPI)->updateRemainLocation($op1_location[0]['LocationID']);
          
          return json_encode([
            'result' => true,
            'message' => 'Update successful.',
            'location' => (new LocationAPI)->getLocationNameFromId($op1_location[0]['LocationID'])
          ]);
        } else {
          
          return json_encode([
            'result' => false,
            'message' => 'Complete error'
          ]);
        }
      }
    }

    $loc = (new PalletAPI)->getLocationByLocationRemain($lpnInfo[0]['ItemID']);

    if ($loc === '') {

      if ( (new PalletAPI)->setLPNComplete($lpn, 7) === true ) {  // 7 = finish good
        (new PalletAPI)->updateRemainLocation(7);
        return json_encode([
          'result' => true,
          'message' => 'Update successful'
        ]);
      } else {
        return json_encode([
          'result' => false,
          'message' => 'Complete error'
        ]);
      }
    }

    if ( (new PalletAPI)->setLPNComplete($lpn, $loc) === true) {
      (new PalletAPI)->updateRemainLocation($loc);
      return json_encode([
        'result' => true,
        'message' => 'Update successful'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => 'Complete error'
      ]);
    }

    // $res = (new PalletAPI)->completeReceiveLocation($lpn, $barcode);

    // if ($res === true) {
    //   return json_encode([
    //     'result' => true,
    //     'message' => 'Update successful.'
    //   ]);
    // } else {
    //   return json_encode([
    //     'result' => false,
    //     'message' => $res
    //   ]);
    // }
  }

  public function getLPNLine()
  {
    $lpnid = $_GET['id'];

    echo (new PalletAPI)->getLPNLine($lpnid);
  }

  public function saveUpdateLocation()
  {
    $location = $_POST['location'];
    $location_temp = $_POST['location_temp'];
    $lpn = $_POST['lpn'];

    $res = (new PalletAPI)->saveUpdateLocation($location, $location_temp, $lpn);

    if ($res === true) {
      return json_encode([
        'result' => true,
        'message' => 'Update successful.'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $res
      ]);
    }
  }

  public function setLPNComplete()
  {
    $lpn = $_POST['lpn'];
    $item = $_POST['item'];

    $location = (new PalletAPI)->getLocationByLocationRemain($item);

    if (count($location) === 0) {
      $__location = 7; // fg
    } else {
      $__location = $location;
    }

    if ((new PalletAPI)->isLPNProcess($lpn) === false) {
      return json_encode([
        'result' => false,
        'message' => 'LPN isn\'t status processing.',
        'location' => (new LocationAPI)->getLocationNameFromId($__location)
      ]);
    }

    $lpnInfo = (new PalletAPI)->getLpnInfo($lpn);

    $isNewLPN = (new PalletAPI)->isNewItemOnLPN($lpnInfo[0]['ItemID'], $lpnInfo[0]['BatchNo']);

    if ( count($isNewLPN) !== 0 ) {
      $op1_location = (new PalletAPI)->getLocationByExistsLPN($lpnInfo[0]['ItemID'], $lpnInfo[0]['BatchNo']);
    
      if ( count($op1_location) !== 0 ) {

        if ( (new PalletAPI)->setLPNComplete($lpn, $op1_location[0]['LocationID']) === true ) {
          
          (new PalletAPI)->updateRemainLocation($op1_location[0]['LocationID']);
          
          return json_encode([
            'result' => true,
            'message' => 'Update successful.',
            'location' => (new LocationAPI)->getLocationNameFromId($op1_location[0]['LocationID'])
          ]);
        } else {
          
          return json_encode([
            'result' => false,
            'message' => 'Complete error',
            'location' => (new LocationAPI)->getLocationNameFromId($op1_location[0]['LocationID'])
          ]);
        }
      }
    }

    $loc = (new PalletAPI)->getLocationByLocationRemain($lpnInfo[0]['ItemID']);

    if ($loc === '') {

      if ( (new PalletAPI)->setLPNComplete($lpn, 7) === true ) {  // 7 = finish good
        (new PalletAPI)->updateRemainLocation(7);
        return json_encode([
          'result' => true,
          'message' => 'Update successful.',
          'location' => (new LocationAPI)->getLocationNameFromId(7)
        ]);
      } else {
        return json_encode([
          'result' => false,
          'message' => 'Complete error',
          'location' => (new LocationAPI)->getLocationNameFromId(7)
        ]);
      }
    }

    if ( (new PalletAPI)->setLPNComplete($lpn, $loc) === true) {
      (new PalletAPI)->updateRemainLocation($loc);
      return json_encode([
        'result' => true,
        'message' => 'Update successful.',
        'location' => (new LocationAPI)->getLocationNameFromId($loc)
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => 'Complete error',
        'location' => (new LocationAPI)->getLocationNameFromId($loc)
      ]);
    }
  }

  public function transferLocation() {

    if (empty($_POST['location']) || empty($_POST['lpn'])) {
      return json_encode([
        'result' => false,
        'message' => 'LPN or Location not found'
      ]);
    }

    $lpn = $_POST['lpn'];
    $location = $_POST['location'];

    if ( (new PalletAPI)->isComplete($lpn) === false ) {
      return json_encode([
        'result' => false,
        'message' => 'LPN not complete.'
      ]);
    }

    if ( (new LocationAPI)->checkLocationForTransfer($location) === false ) {
      return json_encode([
        'result' => false,
        'message' => 'Location ต้องมี type = trans และ Remain > 0'
      ]);
    }

    $res = (new PalletAPI)->transferLocation($lpn, $location);

    if ($res === true) {

      return json_encode([
        'result' => true,
        'message' => 'Transfer successful.',
        'to_location' => (new LocationAPI)->getLocationNameFromId($location)
      ]);
    } else {
      
      return json_encode([
        'result' => false,
        'message' => $res
      ]);
    }
  }

  public function deleteLPN() {

    $lpn = $_POST['lpnid'];

    if ( (new PalletAPI)->isStatusOpen($lpn) === false ) {
      return json_encode([
        'result' => false,
        'message' => 'LPN Not status open'
      ]);
    }

    $res = (new PalletAPI)->deleteLPN($lpn);

    if ($res === true) {
      return json_encode([
        'result' => true,
        'message' => 'Delete successful.'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => 'Delete failed.'
      ]);
    }
  }
}