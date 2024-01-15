<?php

namespace App\V2\PDF;

use App\V2\Pallet\PalletAPI;
use App\V2\Item\ItemAPI;
use App\V2\Location\LocationAPI;

class PDFController
{
  public function LPN($lpn)
  {
    $lpn_arr = explode(',', $lpn);
    $lpndata = (new PalletAPI)->printLPN($lpn_arr);

    // print_r($lpndata);

    return renderView('pdf/lpn_v2',[
      'data' => $lpndata
    ]);
    // $lpnInfo = (new PalletAPI)->getLpnInfo($lpn);
    // $item_name = (new ItemAPI)->getItemInfo($lpnInfo[0]['ItemID']);
    // return renderView('pdf/lpn', [
    //   'lpn' => $lpnInfo[0]['LPNID'],
    //   'item_id' => $lpnInfo[0]['ItemID'],
    //   'item_name' => $item_name[0]['NameTH'],
    //   'batch' => $lpnInfo[0]['BatchNo']
    // ]);
  }

  public function goodsTag($lpn)
  {
    $lpnInfo = (new PalletAPI)->getLpnInfo($lpn);
    $item_name = (new ItemAPI)->getItemInfo($lpnInfo[0]['ItemID']);
    $location = (new LocationAPI)->getLocationInfo($lpnInfo[0]['LocationID']);
    $whname = (new LocationAPI)->getWHNameFromLocation($lpnInfo[0]['LocationID']);

    $time = date('H', strtotime($lpnInfo[0]['CreateDate']));

    if ((int)$time >= 8 && (int)$time <= 20) {
      $shift = 'A';
    } else {
      $shift = 'B';
    }

    // header('content-type: application-json;');
    // echo json_encode($lpnInfo);
    // return;

    return renderView('pdf/goods_tag', [
      'lpn' => $lpnInfo[0]['LPNID'],
      'item_id' => $lpnInfo[0]['ItemID'],
      'item_name' => $item_name[0]['NameTH'],
      'batch' => $lpnInfo[0]['BatchNo'],
      'shift' => $shift,
      'receive' => $lpnInfo[0]['CompleteDate'],
      'location' => $location[0]['Description'],
      'wh_name' => $whname,
      'qty' => $lpnInfo[0]['QtyInUse']
    ]);
  }
}