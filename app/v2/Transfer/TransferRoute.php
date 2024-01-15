<?php

// loading to svo
// $app->get('/loading_to_svo', 'App\V2\Loading\LoadingController::loadingToSVO');
// $app->post('/save_loading_to_svo', 'App\V2\Loading\LoadingController::saveLoadingToSVO');
$app->get('/transfer/str_to_svo_wh', 'App\V2\Transfer\TransferController::STRToSVOWH');
$app->post('/transfer/str_to_svo_wh/save', 'App\V2\Transfer\TransferController::saveSTRToSVOWH');
$app->get('/transfer/str_to_svo_final', 'App\V2\Transfer\TransferController::STRToSVOFinal');
$app->post('/transfer/str_to_svo_final/save', 'App\V2\Transfer\TransferController::saveSTRToSVOFinal');

// $app->get('/transfer/str_to_svo_final', 'App\V2\Transfer\TransferController::STRToSVOFN');
// $app->post('/transfer/str_to_svo_final/save', 'App\V2\Transfer\TransferController::saveSTRToSVOFN');

// force svo
$app->get('/transfer/pcr_to_svo', 'App\Controllers\ReportController::pagePCRtoSVO');
$app->post('/api/v2/transfer_pcr_to_svo', 'App\Controllers\ReportController::reportPCRToSVO');

// $app->get('/force_svo', 'App\V2\Transfer\TransferController::forceSVO');
// $app->post('/save_force_svo', 'App\V2\Transfer\TransferController::saveForceSVO');

$app->get('/transfer/journal_pcr', 'App\V2\Transfer\TransferController::journalPCR');
$app->get('/api/v1/transfer/get_truck', 'App\V2\Transfer\TransferController::getTruck');
$app->post('/api/v1/journal_pcr/create_journal', 'App\V2\Transfer\TransferController::createJournalPCR');
$app->get('/api/v1/journal/([a-z]+)', 'App\V2\Transfer\TransferController::getJournalPCR');
$app->get('/api/v1/journal_no_complete', 'App\V2\Transfer\TransferController::getJournalPCRNoComplete');

$app->get('/api/v1/journal/line/([^/]+)', 'App\V2\Transfer\TransferController::getJournalPCRLine');
$app->post('/api/v1/journal/update', 'App\V2\Transfer\TransferController::updateJournal');
$app->post('/api/v1/journal_pcr/complete', 'App\V2\Transfer\TransferController::completeJournal');
$app->post('/api/v1/journal_count_by_journal', 'App\V2\Transfer\TransferController::countJournalLine');
$app->get('/transfer/journal_pcr/print/([^/]+)', 'App\V2\Transfer\TransferController::printJournal');

// test
// $app->get('/transfer/test', 'App\V2\Transfer\TransferController::test');

// manual
// $app->get('/wms_interface_manual', 'App\V2\Transfer\TransferController::manualInterface');
