<?php

$app->get('/wh_location/pallet_master', 'App\WarehouseLocation\WarehouseLocationController::palletMaster');
$app->get('/wh_location/pallet_receive', 'App\WarehouseLocation\WarehouseLocationController::palletReceive');
$app->get('/wh_location/pallet_transfer', 'App\WarehouseLocation\WarehouseLocationController::palletTransfer');
$app->get('/wh_location/transfer_location', 'App\WarehouseLocation\WarehouseLocationController::transferLocation');
$app->get('/wh_location/pdf/pallet/([^/]+)', 'App\WarehouseLocation\WarehouseLocationController::pdfPallet');
$app->get('/wh_location/pallet_table', 'App\WarehouseLocation\WarehouseLocationController::palletTable');

$app->get('/api/v1/wh_location/last_seq', 'App\WarehouseLocation\WarehouseLocationController::getPalletSeq');
$app->post('/api/v1/wh_location/create_pallet', 'App\WarehouseLocation\WarehouseLocationController::createPallet');
$app->get('/api/v1/wh_location/get_all_pallet', 'App\WarehouseLocation\WarehouseLocationController::getAllPallet');
$app->post('/api/v1/wh_location/pallet_receive/save', 'App\WarehouseLocation\WarehouseLocationController::palletReceiveSave');
$app->get('/api/v1/wh_location/get_pallet_table', 'App\WarehouseLocation\WarehouseLocationController::getPalletTable');
$app->get('/api/v1/wh_location/get_pallet_line/([^/]+)', 'App\WarehouseLocation\WarehouseLocationController::getPalletLine');
$app->post('/api/v1/wh_location/pallet_complete', 'App\WarehouseLocation\WarehouseLocationController::palletComplete');

$app->post('/api/v1/wh_location/pallet_transfer/save', 'App\WarehouseLocation\WarehouseLocationController::savePalletTransfer');
$app->post('/api/v1/wh_location/transfer_location/save', 'App\WarehouseLocation\WarehouseLocationController::saveTransferLocation');

$app->get('/wh_location/tag/([^/]+)', 'App\WarehouseLocation\WarehouseLocationController::printTag');
$app->post('/api/v1/wh_location/update_location', 'App\WarehouseLocation\WarehouseLocationController::updateLocation');
$app->get('/wh_location/print/pallet/([^/]+)', 'App\WarehouseLocation\WarehouseLocationController::printPallet');
