<?php

$app->get('/component', 'App\Controllers\ComponentController::component');
$app->get('/component/tmc', 'App\Controllers\ComponentController::component_TMC');
$app->get('/component/bei', 'App\Controllers\ComponentController::component_BEI');
$app->get('/component/bst', 'App\Controllers\ComponentController::component_BST');
$app->get('/component/stf', 'App\Controllers\ComponentController::component_STF');
$app->get('/component/shw', 'App\Controllers\ComponentController::component_SHW');
$app->get('/component/swl', 'App\Controllers\ComponentController::component_SWL');
$app->get('/component/trd', 'App\Controllers\ComponentController::component_TRD');
$app->get('/component/bel', 'App\Controllers\ComponentController::component_BEL');
$app->get('/component/inl', 'App\Controllers\ComponentController::component_INL');
$app->get('/component/nch', 'App\Controllers\ComponentController::component_NCH');
$app->get('/component/wch', 'App\Controllers\ComponentController::component_WCH');
$app->get('/component/ply', 'App\Controllers\ComponentController::component_PLY');
$app->get('/component/report', 'App\Controllers\ComponentController::component_report');

$app->get('/component_barcode', 'App\Controllers\ComponentController::component_barcode');
$app->get('/component_item', 'App\Controllers\ComponentController::component_item');
$app->get('/component_gen_item', 'App\Controllers\ComponentController::component_gen_item');

$app->get('/component/defect', 'App\Controllers\ComponentController::component_defect');
$app->get('/component/defect/check', 'App\Controllers\ComponentController::component_defectcheck');
$app->get('/component/pastcode', 'App\Controllers\ComponentController::component_pastcode');
$app->get('/component/pastcodecheck', 'App\Controllers\ComponentController::component_pastcodecheck');
$app->get('/component/unit', 'App\Controllers\ComponentController::component_unit');
$app->get('/component/section', 'App\Controllers\ComponentController::component_section');
$app->get('/component/load', 'App\Controllers\ComponentController::component_load');
$app->get('/component/load/pastcode', 'App\Controllers\ComponentController::loadpastcode');
$app->get('/component/print/pastcode', 'App\Controllers\ComponentController::printpastcode');

// $app->post('/component/insert/item', 'App\Controllers\ComponentController::insert_item');
$app->post('/component/insert/barcode', 'App\Controllers\ComponentController::insert_barcode');
$app->post('/component/update/barcode', 'App\Controllers\ComponentController::update_barcode');

$app->post('/component/update/defect', 'App\Controllers\ComponentController::update_defect');
$app->post('/component/update/time', 'App\Controllers\ComponentController::update_time');

$app->post('/component/report/origin/pdf', 'App\Controllers\ComponentController::report_origin_pdf');
$app->post('/component/report/pdf', 'App\Controllers\ComponentController::report_pdf');
$app->post('/component/update/shift', 'App\Controllers\ComponentController::update_shift');
$app->post('/component/update/error', 'App\Controllers\ComponentController::update_error');
$app->post('/component/create/item', 'App\Controllers\ComponentController::create_item');
$app->post('/component/update/status', 'App\Controllers\ComponentController::update_status');
$app->get('/component/check/status', 'App\Controllers\ComponentController::check_status');
$app->get('/component/check/status/number', 'App\Controllers\ComponentController::check_status_number');
$app->get('/component/get/section', 'App\Controllers\ComponentController::getSectionAll');
