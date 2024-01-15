<?php

$app->get('/p2/lpn_master', '\App\V2\Pallet\PalletController::renderLpnMaster');
$app->get('/p2/item_receive_location', '\App\V2\Pallet\PalletController::renderItemReceiveLocation');
$app->get('/p2/receive_location', '\App\V2\Pallet\PalletController::renderReceiveLocation');

$app->post('/p2/api/create_lpn_master', '\App\V2\Pallet\PalletController::createManualLPN');
$app->get('/p2/transfer_lpn', '\App\V2\Pallet\PalletController::renderTransferLPN');
$app->get('/p2/transfer_location', '\App\V2\Pallet\PalletController::renderTransferLocation');
$app->get('/p2/api/all_item_receive_location', '\App\V2\Pallet\PalletController::getAllItemReceiveLocation');
$app->post('/p2/api/create_item_receive_location', '\App\V2\Pallet\PalletController::createItemReceiveLocation');
$app->post('/p2/api/delete_item_receive_location', '\App\V2\Pallet\PalletController::deleteItemReceiveLocation');

$app->get('/api/v2/lpn_all', '\App\V2\Pallet\PalletController::getAllLPNMaster');


$app->post('/api/v2/genauto', '\App\V2\Pallet\PalletController::generateAuto');
$app->post('/api/v2/receive_location', '\App\V2\Pallet\PalletController::receiveLocation');
$app->post('/api/v2/item_receive_location/update_qty', '\App\V2\Pallet\PalletController::updateItemReceiveLocationQTY');
$app->post('/api/v2/receive_location/complete', '\App\V2\Pallet\PalletController::completeReceiveLocation');
$app->post('/api/v2/transfer_lpn', '\App\V2\Pallet\PalletController::transferLPN');
$app->post('/api/v2/transfer_location', '\App\V2\Pallet\PalletController::transferLocation');
$app->get('/api/v2/get_lpn_line', '\App\V2\Pallet\PalletController::getLPNLine');
// $app->post('/api/v2/save_update_location', '\App\V2\Pallet\PalletController::saveUpdateLocation');

$app->post('/api/v2/set_lpn_complete', '\App\V2\Pallet\PalletController::setLPNComplete');
$app->post('/api/v2/delete_lpn', 'App\V2\Pallet\PalletController::deleteLPN');