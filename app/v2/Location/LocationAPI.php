<?php

namespace App\V2\Location;

use Wattanar\Sqlsrv;
use App\V2\Database\Connector;

class LocationAPI
{
  public function getAllLocation()
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT 
      ID,
      Description,
      QTY,
      QTYInUse,
      Remain
      FROM Location
      WHERE LocationType = 'sync'"
    );
  }

  public function getWHIDFromLocation($locationId)
  {
    $conn = (new Connector)->dbConnect();
    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 WarehouseID FROM [Location] 
      WHERE ID = ?",
      [
        $locationId
      ]
    );

    return $query[0]['WarehouseID'];
  }

  public function getWHNameFromLocation($locationId) {
    $conn = (new Connector)->dbConnect();
    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 WarehouseID FROM [Location] 
      WHERE ID = ?",
      [
        $locationId
      ]
    );
    
    $whid = $query[0]['WarehouseID'];

    $whname = Sqlsrv::queryArray(
      $conn,
      "SELECT [Description] from WarehouseMaster
      WHERE ID = ?",
      [
        $whid
      ]
    );

    return $whname[0]['Description'];
  }

  public function getLocationInfo($locationId)
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryArray(
      $conn,
      "SELECT * FROM Location 
      WHERE ID = ?",
      [
        $locationId
      ]
    );
  }

  public function getLocationByType($item)
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT L.ID, L.Description 
      FROM [Location] L
      LEFT JOIN ItemReceiveLocation IR ON IR.LocationID = L.ID
      WHERE L.Remain > 0 
      AND L.LocationType = 'sync'
      AND IR.ItemID = ?",
      [
        $item
      ]
    );
  }

  public function checkLocationForTransfer($locationId) {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT ID FROM [Location]
      WHERE ID = ?
      AND LocationType = 'trans'
      AND Remain > 0",
      [
        $locationId
      ]
    ));
  }

  public function getLocationIdFromName($locationName) {
    $conn = (new Connector)->dbConnect();
    $data = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 ID FROM [Location] 
      WHERE [Description] = ?",
      [
        $locationName
      ]
    );
    return $data[0]['ID'];
  } 

  public function getLocationNameFromId($locationId) {
    $conn = (new Connector)->dbConnect();


    $isIdCorrect = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT ID FROM [Location] WHERE ID = ?",
      [
        $locationId
      ]
    ));

    if ($isIdCorrect === false) {
      return '';
    }

    $data = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 [Description] as LocationName FROM [Location] 
      WHERE ID = ?",
      [
        $locationId
      ]
    );
    return $data[0]['LocationName'];
  } 
}