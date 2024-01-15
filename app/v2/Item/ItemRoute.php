<?php

$app->get('/p2/api/all_item_fg', '\App\V2\Item\ItemController::getAllItemFG');
$app->get('/item', '\App\V2\Item\ItemController::renderItem');
$app->get('/api/v2/item/all', 'App\V2\Item\ItemController::getAllItem');
// $app->post('/api/v1/item/set_manual_batch', 'App\V2\Item\ItemController::setManualBatch');
$app->post('/api/v1/item/update_master', 'App\V2\Item\ItemController::updateMaster');