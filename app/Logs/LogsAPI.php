<?php

namespace App\Logs;

use App\V2\Database\Connector;
use Wattanar\SqlsrvHelper;

class LogsAPI
{
  public function __construct() {
   $this->conn = Connector::getInstance('str_barcode');
  }

  public function readerSave($barcode, $log_status, $log_message, $date_now) {
    try {
      sqlsrv_query(
        $this->conn,
        "INSERT INTO BarcodeReaderLogs(barcode, status, message, create_date) 
        VALUES(?, ?, ?, ?)",
        [
          $barcode,
          $log_status,
          $log_message,
          $date_now
        ]
      );
      return true;
    } catch (\Exception $e) {
      return false;
    }
  }
}