<?php

$app->get("/unbom", "App\Controllers\UnbomController::index");


$app->post('/api/v1/unbom/save', '\App\Controllers\UnbomController::saveUnbom');