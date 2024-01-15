<?php

namespace App\V2\Component;

use App\V2\Database\Connector;
use App\V2\Database\Handler;
use Wattanar\Sqlsrv;
use App\V2\Helper\Helper;

class ComponentAPI
{ 
  public function getLastNumberByDate($date) {
    $db = new Connector;
    $helper = new Helper;

    $conn = $db->dbConnect();

    $meta_key = "component_tag_" . $date;

    $result = Sqlsrv::queryArray(
      $conn,
      "SELECT SeqValue FROM SeqNumber
      WHERE SeqName = ?",
      [
        $meta_key
      ]
    );

    return $helper->strpad($result[0]['SeqValue'], 4);
  }

  public function updateSeqComponentTag($date, $qty) {
    $db = new Connector;
    $conn = $db->dbConnect();

    $meta_key = "component_tag_" . $date;

    $result = sqlsrv_query(
      $conn,
      "UPDATE SeqNumber SET SeqValue += ?
      WHERE SeqName = ?",
      [
        $qty,
        $meta_key
      ]
    );

    if ($result) {
      return true;
    } else {
      return false;
    }
  }
}