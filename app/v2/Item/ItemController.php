<?php

namespace App\V2\Item;

use App\V2\Item\ItemAPI;

class ItemController
{
  private $itemApi = null;

  public function __construct()
  {
    $this->itemApi = new ItemAPI();
  }

  public function getAllItemFG()
  {
    echo $this->itemApi->getAllItemFG();
  }

  public function renderItem()
  {
    renderView('page/item_master');
  }

  public function getAllItem()
  {
    echo $this->itemApi->getAllItem();
  }

  // public function setManualBatch()
  // {
  //   $itemId = $_POST['itemId'];

  //   if ($_POST['manualBatch'] === 'true') {
  //     $manualBatch = 1;
  //   } else {
  //     $manualBatch = 0;
  //   }

  //   $manualBatchStatus = $manualBatch;

  //   if ($itemId === '' || is_null($itemId) === true) return json_encode(['result' => false]);

  //   $res = $this->itemApi->setManualBatch($itemId, $manualBatchStatus);

  //   if ($res === true)  {
  //     return json_encode([
  //       'result' => true,
  //       'message' => 'Update successful!'
  //     ]);
  //   } else {
  //     return json_encode([
  //       'result' => false,
  //       'message' => $res
  //     ]);
  //   }

  // }

  public function updateMaster()
  {

    $itemId = $_POST['itemId'];
    $channel = $_POST['channel'];

    if ($_POST['manualBatch'] === 'true') {
      $manualBatch = 1;
    } else {
      $manualBatch = 0;
    }

    if ($_POST['checkSerial'] === 'true') {
      $checkSerial = 1;
    } else {
      $checkSerial = 0;
    }

    if ($itemId === '' || is_null($itemId) === true) return json_encode(['result' => false]);

    $setCheckSerial = $this->itemApi->setCheckSerial($itemId, $checkSerial);
    $res = $this->itemApi->setManualBatch($itemId, $manualBatch);
    $updateChannel = $this->itemApi->updateChannel($itemId, $channel);

    if ($res === true && $setCheckSerial === true && $updateChannel === true) {
      return json_encode([
        'result' => true,
        'message' => 'Update successful!'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $res
      ]);
    }
  }
}
