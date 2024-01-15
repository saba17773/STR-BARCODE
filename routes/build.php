<?php

$app->get('/change_code', 'App\Controllers\BuildingController::changeCode');
$app->post('/api/v1/build/check', 'App\Controllers\BuildingController::checkBuild');
$app->post('/api/v1/building/change_code', 'App\Controllers\BuildingController::saveChangeCode');

$app->post('/api/v1/building/force_change_code', 'App\Controllers\BuildingController::forceChangeCode');
$app->post('/api/v1/build/check_cure', 'App\Controllers\BuildingController::checkcure');

// send to wms
$app->post('/api/v1/wms/check_save', 'App\Controllers\BuildingController::check_save_wms');

//mobile
$app->get('/change_code_mb', 'App\Controllers\BuildingController::changeCode_mb');

