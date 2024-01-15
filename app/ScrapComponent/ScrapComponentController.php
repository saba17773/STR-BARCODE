<?php

namespace App\ScrapComponent;

use App\ScrapComponent\ScrapComponentAPI;

class ScrapComponentController
{
 public function home() {
   return renderView('scrap_component/home');
 } 

 public function save() {
  $area = $_POST['area'];
  $defect = $_POST['defect'];
  $part_code = $_POST['part_code'];
  $qty = $_POST['qty'];
  $scrap_location = $_POST['scrap_location'];

  $result = (new ScrapComponentAPI)->saveScrapComponent(
    $area,
    $defect,
    $part_code,
    $qty,
    $scrap_location
  );

  return json_encode($result);
 }

 public function getAll() {
  $result = (new ScrapComponentAPI)->getAll();
  return json_encode($result);
 }

 public function saveCancel() {
  $scrapId = $_POST['scrap_id'];
  $result = (new ScrapComponentAPI)->saveCancel($scrapId);
  return json_encode($result);
 }

 public function saveComplete() {
  $SCHDate = $_POST['sch_date'];
  $partCode = $_POST['part_code'];
  $result = (new ScrapComponentAPI)->saveComplete($SCHDate, $partCode);
  return json_encode($result);
 }
}