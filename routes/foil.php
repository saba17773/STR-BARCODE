<?php

$app->get("/foil", "App\Controllers\FoilController::renderFoil");
$app->get("/unfoil", "App\Controllers\FoilController::renderUnfoil");
$app->get("/report_foil", "App\Controllers\FoilController::reportFoil");

$app->post('/api/v1/foil/save', 'App\Controllers\FoilController::saveFoil');
$app->post('/api/v1/unfoil/save', 'App\Controllers\FoilController::saveUnfoil');
$app->post('/report/foil/pdf', 'App\Controllers\FoilController::reportFoilPDF');