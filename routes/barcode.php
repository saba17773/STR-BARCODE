<?php

$app->get('/change_barcode', 'App\Controllers\BarcodeController::changeBarcode');
$app->post('/check_barcodewh', 'App\Controllers\BarcodeController::check_barcodewh');
$app->post('/change_barcode/save', 'App\Controllers\BarcodeController::saveChangeBarcode');

//mobile
$app->get('/change_barcode_mb', 'App\Controllers\BarcodeController::changeBarcode_mb');