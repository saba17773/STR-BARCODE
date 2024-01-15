<?php

namespace App\V2\Transfer;

use App\V2\User\UserAPI;
use App\V2\Transfer\TransferAPI;
use App\V2\Barcode\BarcodeAPI;
use App\V2\Inventory\InventoryAPI;

class TransferController
{
  private $userApi = null;
  private $transferApi = null;
  private $barcodeApi = null;
  private $inventoryApi = null;

  public function __construct()
  {
    $this->userApi = new UserAPI();
    $this->transferApi = new TransferAPI();
    $this->barcodeApi = new BarcodeAPI();
    $this->inventoryApi = new InventoryAPI();
  }

  public function STRToSVOFinal()
  {
    $this->userApi->auth();
    $this->userApi->userAccess();
    renderView('page/str_to_svo_final');
  }

  public function saveSTRToSVOFinal()
  {

    $userAPI = $this->userApi;
    $transferAPI = $this->transferApi;
    $barcodeAPI = $this->barcodeApi;
    $inventoryAPI = $this->inventoryApi;

    if ($this->userApi->authAPI() === false) {
      return json_encode([
        'result' => false,
        'message' => 'Please login',
        'auth' => false
      ]);
    }

    if (!isset($_POST['barcode'])) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode incorrect'
      ]);
    }

    $barcode = $_POST["barcode"];

    if (trim($barcode) === '') {
      return json_encode([
        'result' => false,
        'message' => 'Barcode incorrect'
      ]);
    }

    if ($this->barcodeApi->isBarcodePrinted($barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode not found'
      ]);
    }

    if ($this->barcodeApi->isBarcodeRegistered($barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode not register'
      ]);
    }

    if ($this->inventoryApi->isReceive($barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode not receive'
      ]);
    }

    if ($this->inventoryApi->isFinalReceiveDate($barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode not receive to final'
      ]);
    }

    if ($this->inventoryApi->isStillOnFinal($barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode not in final'
      ]);
    }

    $result = $this->transferApi->saveSTRToSVOFinal($barcode);

    if ($result === true) {
      return json_encode([
        'result' => true,
        'message' => 'บันทึกข้อมูลสำเร็จ',
        "curecode" => $this->barcodeApi->getCureCode($barcode),
        'batch' => $this->barcodeApi->getBatch($barcode)
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $result,
        "curecode" => "",
        'batch' => ''
      ]);
    }
  }

  public function STRToSVOWH()
  {
    $this->userApi->auth();
    $this->userApi->userAccess();
    renderView('page/str_to_svo_wh');
  }

  public function saveSTRToSVOWH()
  {
    if ($this->userApi->authAPI() === false) {
      return json_encode([
        'result' => false,
        'message' => 'Please login',
        'auth' => false
      ]);
    }

    if (!isset($_POST['barcode'])) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode incorrect'
      ]);
    }

    $barcode = $_POST["barcode"];
    $journal = $_POST["journal"];

    if (trim($barcode) === '') {
      return json_encode([
        'result' => false,
        'message' => 'Barcode incorrect'
      ]);
    }

    if ($this->barcodeApi->isBarcodePrinted($barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode not found'
      ]);
    }

    if ($this->barcodeApi->isBarcodeRegistered($barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode not register'
      ]);
    }

    if ($this->inventoryApi->isReceive($barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode not receive'
      ]);
    }

    // if ($this->inventoryApi->isFinalReceiveDate($barcode) === false) {
    //   return json_encode([
    //     'result' => false,
    //     'message' => 'Barcode not receive to final'
    //   ]);
    // }

    if ($this->inventoryApi->isInWarehouseFG($barcode) === false) {
      return json_encode([
        'result' => false,
        'message' => 'Barcode not found in warehouse'
      ]);
    }
    
    $result = $this->transferApi->saveSTRToSVOWH($barcode, $journal);

    if ($result === true) {
      return json_encode([
        'result' => true,
        'message' => 'บันทึกข้อมูลสำเร็จ',
        "curecode" => $this->barcodeApi->getCureCode($barcode),
        'batch' => $this->barcodeApi->getBatch($barcode)
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $result,
        "curecode" => '',
        'batch' => ''
      ]);
    }
  }

  public function journalPCR()
  {

    $this->userApi->auth();
    $this->userApi->userAccess();

    renderView('page/journal_pcr');
  }

  public function getTruck()
  {
    // $transferAPI = $this->TransferAPI;

    $data = $this->transferApi->getTruck();

    return json_encode($data);
  }

  public function createJournalPCR()
  {
    $desc = trim($_POST['desc']);
    $truck = trim($_POST['truck']);

    $create = $this->transferApi->createJournalPCR($desc, $truck);


    if ($create === true) {

      return json_encode([
        'result' => true,
        'message' => 'Create successful!'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $create
      ]);
    }
  }

  public function getJournalPCR($device)
  {
    return json_encode($this->transferApi->getJournalPCR($device));
  }

  public function getJournalPCRNoComplete()
  {
    return json_encode($this->transferApi->getJournalPCRNoComplete());
  }

  public function getJournalPCRLine($journal_id)
  {
    $data = $this->transferApi->getJournalPCRLine($journal_id);

    if (count($data) === 0) {
      return json_encode([]);
    } else {
      return json_encode($data);
    }
  }

  public function updateJournal()
  {
    $journal_id = trim($_POST['journal_id']);
    $journal_description = trim($_POST['journal_description']);

    $update = $this->transferApi->updateJournal($journal_id, $journal_description);

    if ($update === true) {
      return json_encode([
        'result' => true,
        'message' => 'Update successful!'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $update
      ]);
    }
  }

  public function completeJournal()
  {
    $journal_id = $_POST['journal_id'];

    $update = $this->transferApi->completeJournal($journal_id);

    if ($update === true) {
      return json_encode([
        'result' => true,
        'message' => 'Update successful!'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $update
      ]);
    }
  }

  public function countJournalLine()
  {
    $journal_id = $_POST['journal_id'];
    return \json_encode(['count' => $this->transferApi->countJournalLine($journal_id)]);
  }

  public function printJournal($journal_id)
  {

    $journal = $this->transferApi->printJournalPCR($journal_id);

    // echo '<pre>' . print_r($journal, true) . '</pre>';
    renderView('pdf/pdf_journal_pcr', [
      'journal' => $journal
    ]);
  }

  public function manualInterface()
  {
    echo $this->transferApi->manualInterface();
    die();
  }
}
