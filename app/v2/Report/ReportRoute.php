<?php


$app->get('/report', 'App\V2\Report\ReportController::all');
// $app->get('/report_repair_daily', 'App\Controllers\ReportController::repairDaily');
$app->get('/report/daily_final_hold', 'App\V2\Report\ReportController::dailyFinalHold');
// $app->get('/report/daily_final_hold/([^/]+)/([1-9])/([a-z]+)/([1-9])/view', 'App\V2\Report\ReportController::dailyFinalHoldView');
$app->post('/report/daily_final_hold_report', 'App\V2\Report\ReportController::dailyFinalHoldView');
