<?php

$app->get("/warehouse_counting", "App\WarehouseCounting\WarehouseCountingController::index");
$app->get("/warehouse_counting/get_item", "App\WarehouseCounting\WarehouseCountingController::getItem");

$app->post("/warehouse_counting/save", "App\WarehouseCounting\WarehouseCountingController::save");
$app->post("/warehouse_counting/get_data", "App\WarehouseCounting\WarehouseCountingController::getBarcodeData");
$app->post("/warehouse_counting/get_remain_item", "App\WarehouseCounting\WarehouseCountingController::getRemainItem");

$app->get("/warehouse_counting/report_onhand_diff", "App\WarehouseCounting\WarehouseCountingController::reportOnhandDiff");

$app->get("/warehouse_counting/report/onhand/excel", "App\WarehouseCounting\WarehouseCountingController::reportOnhandExcel");
$app->get("/warehouse_counting/report/onhand/pdf", "App\WarehouseCounting\WarehouseCountingController::reportOnhandPdf");
$app->post("/warehouse_counting/report_onhand", "App\WarehouseCounting\WarehouseCountingController::reportOnHand");

$app->get("/warehouse_counting/setup", "App\WarehouseCounting\WarehouseCountingController::setup");
$app->post("/warehouse_counting/setup/save", "App\WarehouseCounting\WarehouseCountingController::saveSetup");

$app->get("/warehouse_counting/report_counting", "App\WarehouseCounting\WarehouseCountingController::reportCounting");
$app->post("/warehouse_counting/report_counting_export", "App\WarehouseCounting\WarehouseCountingController::reportCountingExport");
$app->post("/warehouse_counting/remove_barcode", "App\WarehouseCounting\WarehouseCountingController::removeBarcode");