<?php

$app->get("/bom", "App\Controllers\BomController::index");
$app->get("/report_bom", "App\Controllers\BomController::reportBom");

$app->post("/api/v1/bom/save", "App\Controllers\BomController::save");
$app->post('/report/bom/pdf', 'App\Controllers\BomController::reportBomPDF');