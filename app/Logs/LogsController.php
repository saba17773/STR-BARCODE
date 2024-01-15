<?php

namespace App\Logs;

use App\Logs\LogsAPI;

class LogsController
{
  public function __construct() {
    $this->logsAPI = new LogsAPI();
  }

  public function readerSave() {
    try {
      if (count($_POST) === 0) {
				$_POST = json_decode(file_get_contents('php://input'), true);
      }
      
      $save = $this->logsAPI->readerSave(
        $_POST['barcode'],
        $_POST['log_status'],
        $_POST['log_message'],
        $_POST['date_now']
      );

      if ($save === false) throw new \Exception("Error");

      return json_encode([
        'result' => $save,
        'message' => "Save success."
      ]);
    } catch (\Exception $e) {
      return json_encode([
        'result' => false,
        'message' => $e->getMessage()
      ]);
    }
  }
}