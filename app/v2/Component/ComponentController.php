<?php

namespace App\V2\Component;

use App\V2\Component\ComponentAPI;
use App\V2\Helper\Helper;

class ComponentController
{
  public function generateComponentTag() {
    renderView('pagecomponent/gen_component_tag');
  }

  public function getLastNumberByDate() {
    $print_date = $_POST['print_date'];
    $component = new ComponentAPI;

    $current = $component->getLastNumberByDate($print_date);

    echo json_encode([
      "current" => strtoupper($print_date),
      "qty" => $current
    ]);
  }

  public function printComponentTag($date, $qty) {
    
    $component = new ComponentAPI;
    $helper = new Helper;
    $current = $component->getLastNumberByDate($date);

    $current = (int)$current;
    $qty_new = (int)$qty;

    $data = [];

    for($i = $current; $i <= $current+$qty_new; $i++) {
      $data[] = strtoupper($date) . $helper->strpad($i, 4);
    } 

    $component->updateSeqComponentTag($date, $qty);

    \renderView("pagecomponent/print_tag", [
      "data" => $data
    ]);
  }
}