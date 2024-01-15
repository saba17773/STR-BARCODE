<?php

namespace App\V2\Report;

use App\V2\Report\ReportAPI;
use App\Services\BOIService;

class ReportController
{

  public function __construct()
  {
    $this->report_api = new ReportAPI;
    $this->boi = new BOIService;
  }

  public function all()
  {
    renderView('report/report_all');
  }

  public function dailyFinalHold()
  {
    renderView('page/daily_final_hold');
  }

  public function dailyFinalHoldView()
  {
    $date = filter_input(INPUT_POST, "param_date");
    $shift = filter_input(INPUT_POST, "param_shift");
    $type = filter_input(INPUT_POST, "param_type");
    $check = filter_input(INPUT_POST, "check_type");
    $holdtype = filter_input(INPUT_POST, "holdtype");
    $BOI    = 'filter_input(INPUT_POST, "selectMenuBOI")';

    function convertforin($str)
    {
      $strploblem = "";
      $a = explode(',', $str);
      foreach ($a as $value) {
        if ($strploblem === "") {
          $strploblem .= $value;
        } else {
          $strploblem .= "," . $value;
        }
      }
      return $strploblem;
    }
    $pressBOI  = convertforin(implode(',', $_POST["selectMenuBOI"]));
    $dataBOIName = $this->boi->BOIName($pressBOI);
    if ($dataBOIName == "") {
      $dataBOIName = "ALL";
    }
    $result = $this->report_api->dailyFinalHoldView($date, $shift, $type, $pressBOI, $holdtype);

    if ($result !== null) {

      if ($check == 1) {
        renderView('page/daily_final_hold_pdf', [
          'data' => $result,
          'date' => $date,
          'shift' => (int) $shift === 1 ? 'กลางวัน (08.01 - 20.00 น.)' : 'กลางคืน (20.01 - 08.00 น.)',
          'type' => strtoupper($type),
          'BOIName' => $dataBOIName,
          'HoldType' => (int) $holdtype === 1 ? 'Normal' : 'Mode Light Buff',
        ]);
      } else {
        renderView('page/daily_final_hold_excel', [
          'data' => $result,
          'date' => $date,
          'shift' => (int) $shift === 1 ? 'กลางวัน (08.01 - 20.00 น.)' : 'กลางคืน (20.01 - 08.00 น.)',
          'type' => strtoupper($type),
          'BOIName' => $dataBOIName,
          'HoldType' => (int) $holdtype === 1 ? 'Normal' : 'Mode Light Buff',
        ]);
      }
    } else {
      echo 'data not found!';
    }
  }
}
