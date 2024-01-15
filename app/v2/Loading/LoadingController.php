<?php

namespace App\V2\Loading;

use App\V2\Database\Connector;
use App\V2\Loading\LoadingAPI;

class LoadingController
{
  private $loading;

  public function __construct()
  {
    $this->loading = new LoadingAPI();
  }

  public function denyLoading()
  {
    $data = $this->loading->getDenyLoading();
    renderView("loading/deny_loading", ["data" => $data]);
  }

  public function saveAddLoading()
  {
    $itemId = $_POST["item_id"];
    $batch = $_POST["batch"];
    $cusotmer_group = $_POST["customer_group"];

    if (isset($_POST["check_customer_group"])) {
      $checkCustomerGroup = $_POST["check_customer_group"];
    } else {
      $checkCustomerGroup = 0;
    }

    $result = $this->loading->saveAddLoading($itemId, $batch, $cusotmer_group, $checkCustomerGroup);
    return json_encode($result);
  }

  public function saveDeleteDenyLoading()
  {
    $id = $_POST["id"];
    $result = $this->loading->saveDeleteDenyLoading($id);
    return json_encode($result);
  }

  public function export()
  {
    $result = $this->loading->export();
    return renderView('report/deny_loading_excel', [
      "data" => $result
    ]);
  }
}
