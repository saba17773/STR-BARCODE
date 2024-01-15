<?php

namespace App\Movement;

use App\Movement\MovementAPI;

class MovementController
{
  public function __construct() {
    $this->movement = new MovementAPI();
  }

  public function getItemAvailable($journal_id) {
    try {
      $item = $this->movement->getItemAvailable($journal_id);
      echo \json_encode($item);
    } catch (\Exception $e) {
      echo json_encode([$e->getMessage()]);
    }
  }

  public function getBatchAvailable($journal_id, $item) {
    try {
      $item = $this->movement->getBatchAvailable($journal_id, $item);
      echo \json_encode($item);
    } catch (\Exception $e) {
      echo json_encode([$e->getMessage()]);
    }
  }

  public function checkcounbatch($journal_id, $item,$batch) {
    try {

          $result = (new MovementAPI)->checkcounbatch($journal_id, $item,$batch);

     if ($result["status"] == 200) {
     echo json_encode(["status" => 200, "message" => $result["message"]]);
     } else {
       echo json_encode(["status" => 404, "message" => $result["message"]]);
     }

    } catch (\Exception $e) {
      echo json_encode([$e->getMessage()]);
    }
  }
}
