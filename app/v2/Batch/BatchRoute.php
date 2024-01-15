<?php

$app->get('/batch/setup', 'App\V2\Batch\BatchController::renderBatchSetup');

$app->get('/api/v2/batch/all', 'App\V2\Batch\BatchController::getBatchSetup');
$app->get('/api/v2/batch/active', 'App\V2\Batch\BatchController::getBatchSetupActive');
$app->post('/api/v2/batch/create_new_setup', 'App\V2\Batch\BatchController::createNewSetup');
$app->post('/api/v2/batch/save_batch_setup', 'App\V2\Batch\BatchController::saveBatchSetup');
$app->post('/api/v1/batch/active_setup', 'App\V2\Batch\BatchController::activeBatch');
$app->post('/api/v2/batch/set_batch_setup_active', 'App\V2\Batch\BatchController::setBatchSetupActive');
$app->post('/api/v2/batch/is_active', 'App\V2\Batch\BatchController::isBatchSetupActive');

// $app->get('/batch/setup_test/([^/]+)/([^/]+)/([^/]+)', 'App\V2\Batch\BatchController::testBatchSetup');