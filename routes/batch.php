<?php

$app->get('/change_batch', 'App\Controllers\BatchController::render');
$app->post('/get_week', 'App\Controllers\BatchController::getWeekNormal');
$app->post('/change_batch/save', 'App\Controllers\BatchController::saveNewBatch');

$app->get('/update_manual_batch', 'App\Controllers\BatchController::renderUpdateManualBatch');
//mobile
$app->get('/change_batch_mb', 'App\Controllers\BatchController::render_mb');
