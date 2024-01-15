<?php

$app->get('/check_build', 'App\Controllers\CheckController::checkBuild');
$app->get('/check_cure', 'App\Controllers\CheckController::checkTest');
$app->get('/handheld_loginMb', 'App\Controllers\CheckController::handheld_loginMb');
$app->get('/check_wmsbarcode', 'App\Controllers\CheckController::checkwmsbarcode');
