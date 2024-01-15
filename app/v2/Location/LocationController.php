<?php

namespace App\V2\Location;

use App\V2\Location\LocationAPI;

class LocationController
{
  public function getAllLocation()
  {
    echo (new LocationAPI)->getAllLocation();
  }

  public function getLocationByType()
  {
    $item = $_GET['item'];
    echo (new LocationAPI)->getLocationByType($item);
  }
}